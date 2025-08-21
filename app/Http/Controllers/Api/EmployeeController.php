<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code|max:50',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'employee_code' => $request->employee_code,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'status' => $request->get('status', 'active'),
                'hourly_rate' => $request->get('hourly_rate', '0.00'),
                'skills' => $request->get('skills', '[]'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'employee' => $employee
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $query = Employee::query();

        if ($request->has('active_only') && $request->active_only) {
            $query->where('status', 'active');
        }

        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'employees' => $employees
        ]);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json([
            'success' => true,
            'employee' => $employee
        ]);
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'status' => $request->get('status', $employee->status),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!',
                'employee' => $employee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Employee $employee): JsonResponse
    {
        try {
            DB::beginTransaction();

            $employee->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
    }
}