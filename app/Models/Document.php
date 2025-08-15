<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'task_id',
        'file_name',
        'original_file_name',
        'file_path',
        'file_url',
        'file_type',
        'file_size',
        'category',
        'version',
        'description',
        'uploaded_by',
        'uploaded_at',
        'metadata',
        'is_public'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
        'metadata' => 'array',
        'uploaded_at' => 'datetime'
    ];

    protected $dates = [
        'uploaded_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }

    // Accessors
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'picture_as_pdf',
            'doc', 'docx' => 'description',
            'xls', 'xlsx' => 'table_chart',
            'ppt', 'pptx' => 'slideshow',
            'jpg', 'jpeg', 'png', 'gif' => 'image',
            'mp4', 'avi', 'mov' => 'video_library',
            'mp3', 'wav' => 'audiotrack',
            'zip', 'rar' => 'archive',
            default => 'insert_drive_file'
        };
    }

    public function getFileTypeColorAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'error',
            'doc', 'docx' => 'primary',
            'xls', 'xlsx' => 'success',
            'ppt', 'pptx' => 'warning',
            'jpg', 'jpeg', 'png', 'gif' => 'info',
            'mp4', 'avi', 'mov' => 'secondary',
            'mp3', 'wav' => 'default',
            'zip', 'rar' => 'default',
            default => 'default'
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'Contracts & BOQs' => 'gavel',
            'Design & Drawings' => 'architecture',
            'Site Surveys' => 'location_on',
            'Procurement & Invoices' => 'receipt',
            'Progress Reports' => 'assessment',
            'SHEQ' => 'security',
            'Photos & Media' => 'photo_library',
            'Meeting Minutes' => 'event_note',
            'Other' => 'folder',
            default => 'insert_drive_file'
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'Contracts & BOQs' => 'primary',
            'Design & Drawings' => 'secondary',
            'Site Surveys' => 'info',
            'Procurement & Invoices' => 'success',
            'Progress Reports' => 'warning',
            'SHEQ' => 'error',
            'Photos & Media' => 'info',
            'Meeting Minutes' => 'default',
            'Other' => 'default',
            default => 'default'
        };
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg']);
    }

    public function getIsVideoAttribute(): bool
    {
        return in_array($this->file_type, ['mp4', 'avi', 'mov', 'wmv', 'flv']);
    }

    public function getIsAudioAttribute(): bool
    {
        return in_array($this->file_type, ['mp3', 'wav', 'aac', 'ogg']);
    }

    public function getIsDocumentAttribute(): bool
    {
        return in_array($this->file_type, ['pdf', 'doc', 'docx', 'txt', 'rtf']);
    }

    public function getIsSpreadsheetAttribute(): bool
    {
        return in_array($this->file_type, ['xls', 'xlsx', 'csv']);
    }

    public function getIsPresentationAttribute(): bool
    {
        return in_array($this->file_type, ['ppt', 'pptx']);
    }

    public function getIsArchiveAttribute(): bool
    {
        return in_array($this->file_type, ['zip', 'rar', '7z', 'tar', 'gz']);
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByUploader($query, $uploaderId)
    {
        return $query->where('uploaded_by', $uploaderId);
    }

    public function scopeByFileType($query, $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('uploaded_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('original_file_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    // Methods
    public function getDownloadUrl(): string
    {
        if ($this->file_url) {
            return $this->file_url;
        }
        
        return route('documents.download', $this->id);
    }

    public function getThumbnailUrl(): ?string
    {
        if ($this->is_image) {
            // Generate thumbnail URL for images
            return route('documents.thumbnail', $this->id);
        }
        
        return null;
    }

    public function getPreviewUrl(): ?string
    {
        if ($this->is_document || $this->is_spreadsheet || $this->is_presentation) {
            // Generate preview URL for documents
            return route('documents.preview', $this->id);
        }
        
        return null;
    }

    public function canDownload($user): bool
    {
        // Check if user has permission to download this document
        if ($this->is_public) return true;
        
        // Check if user is project member
        if ($this->project && $this->project->hasMember($user)) return true;
        
        // Check if user is document uploader
        if ($this->uploaded_by === $user->id) return true;
        
        return false;
    }

    public function canEdit($user): bool
    {
        // Check if user can edit this document
        if ($this->uploaded_by === $user->id) return true;
        
        // Check if user is project manager
        if ($this->project && $this->project->project_manager_id === $user->id) return true;
        
        return false;
    }

    public function canDelete($user): bool
    {
        // Check if user can delete this document
        if ($this->uploaded_by === $user->id) return true;
        
        // Check if user is project manager
        if ($this->project && $this->project->project_manager_id === $user->id) return true;
        
        return false;
    }

    public function incrementVersion(): void
    {
        $currentVersion = $this->version;
        $parts = explode('.', $currentVersion);
        $parts[count($parts) - 1]++;
        $this->version = implode('.', $parts);
        $this->save();
    }

    public function createNewVersion($file, $description = null): self
    {
        $this->incrementVersion();
        
        return static::create([
            'project_id' => $this->project_id,
            'task_id' => $this->task_id,
            'file_name' => $file->getClientOriginalName(),
            'original_file_name' => $file->getClientOriginalName(),
            'file_path' => $this->storeFile($file),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'category' => $this->category,
            'version' => $this->version,
            'description' => $description ?: $this->description,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
            'is_public' => $this->is_public
        ]);
    }

    protected function storeFile($file): string
    {
        $project = $this->project;
        $path = "projects/{$project->project_code} - {$project->project_name}/{$this->category}";
        
        return Storage::disk('public')->putFile($path, $file);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($document) {
            // Delete physical file when document is deleted
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }
}
