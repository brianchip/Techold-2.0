<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'category' => 'required|in:Contracts & BOQs,Design & Drawings,Site Surveys,Procurement & Invoices,Progress Reports,SHEQ,Photos & Media,Meeting Minutes,Other',
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png|max:10240', // 10MB max
            'version' => 'nullable|string|max:50',
        ]);

        $project = Project::find($validated['project_id']);
        
        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            
            // Create unique filename
            $filename = time() . '_' . str_replace(' ', '_', $originalName);
            
            // Store file in project-specific directory
            $path = $file->storeAs(
                "projects/{$project->project_code}/documents",
                $filename,
                'public'
            );
            
            // Get a default employee ID for uploaded_by (temporary solution)
            $defaultEmployee = \App\Models\Employee::first();
            
            // Create document record
            $document = Document::create([
                'project_id' => $validated['project_id'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'file_name' => $filename,
                'original_file_name' => $originalName,
                'file_path' => $path,
                'file_size' => $size,
                'file_type' => $extension,
                'version' => $validated['version'] ?? '1.0',
                'uploaded_by' => $defaultEmployee ? $defaultEmployee->id : 1, // Use first employee or default to 1
                'uploaded_at' => now(),
                'is_public' => false,
            ]);

            if ($request->expectsJson()) {
                return response()->json($document->load('project'), 201);
            }

            return redirect()->route('projects.show', $project)
                ->with('success', 'Document uploaded successfully.');
        }

        return back()->withErrors(['document' => 'File upload failed.']);
    }

    public function view(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        // For images, PDFs, and text files, display inline
        $inlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
        
        if (in_array(strtolower($document->file_type), $inlineTypes)) {
            return Storage::disk('public')->response(
                $document->file_path,
                $document->original_file_name,
                ['Content-Disposition' => 'inline']
            );
        }
        
        // For other file types, redirect to download
        return $this->download($document);
    }

    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_file_name
        );
    }

    public function destroy(Document $document)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $project = $document->project;
        $document->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document deleted successfully.']);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Document deleted successfully.');
    }
}