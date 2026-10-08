<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Employee::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('employee_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department')) {

            $query->where(
                'department',
                $request->department
            );
        }

        $employees = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $departments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view(
            'crm.employees.index',
            compact(
                'employees',
                'departments'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'crm.employees.create'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_no' => [
                'required',
                'string',
                'max:50',
                'unique:employees,employee_no',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'position' => [
                'nullable',
                'string',
                'max:100',
            ],

            'department' => [
                'nullable',
                'string',
                'max:100',
            ],

            'employment_type' => [
                'required',
                Rule::in([
                    'full_time',
                    'part_time',
                    'contract',
                    'temporary',
                    'casual',
                    'project_based',
                ]),
            ],

            'hire_date' => [
                'nullable',
                'date',
            ],

            'hourly_rate' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'on_leave',
                    'terminated',
                ]),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'emergency_contact_relationship' => [
                'nullable',
                'string',
                'max:50',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        Employee::create($validated);

        return redirect()
            ->route('crm.employees.index')
            ->with(
                'success',
                'Employee created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(Employee $employee)
    {
        $employee->load([
            'jobAssignments.job',
        ]);

        return view(
            'crm.employees.show',
            compact('employee')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(Employee $employee)
    {
        return view(
            'crm.employees.edit',
            compact('employee')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Employee $employee
    ) {
        $validated = $request->validate([
            'employee_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'employees',
                    'employee_no'
                )->ignore($employee),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'position' => [
                'nullable',
                'string',
                'max:100',
            ],

            'department' => [
                'nullable',
                'string',
                'max:100',
            ],

            'employment_type' => [
                'required',
                Rule::in([
                    'full_time',
                    'part_time',
                    'contract',
                    'temporary',
                    'casual',
                    'project_based',
                ]),
            ],

            'hire_date' => [
                'nullable',
                'date',
            ],

            'hourly_rate' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'on_leave',
                    'terminated',
                ]),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'emergency_contact_relationship' => [
                'nullable',
                'string',
                'max:50',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $employee->update($validated);

        return redirect()
            ->route(
                'crm.employees.show',
                $employee
            )
            ->with(
                'success',
                'Employee updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(Employee $employee)
    {
        if ($employee->status === 'active') {

            $employee->update([
                'status' => 'inactive',
            ]);

            $message = 'Employee deactivated successfully.';

        } else {

            $employee->update([
                'status' => 'active',
            ]);

            $message = 'Employee activated successfully.';
        }

        return back()->with(
            'success',
            $message
        );
    }
}