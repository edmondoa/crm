<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\CustomerController;
use App\Http\Controllers\Crm\ContactController;
use App\Http\Controllers\Crm\PropertyController;
use App\Http\Controllers\Crm\PropertyLocationController;
use App\Http\Controllers\Crm\ActivityController;
use App\Http\Controllers\Crm\TaskController;
use App\Http\Controllers\Crm\JobController;
use App\Http\Controllers\Crm\EmployeeController;
use App\Http\Controllers\Crm\JobAssignmentController;
use App\Http\Controllers\Crm\JobScheduleController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('crm.dashboard');
});
Route::get('dashboard', function () {
    return redirect()->route('crm.dashboard');
});

Route::prefix('crm') ->name('crm.') ->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::get(
        'customers/{customer}/contacts',
        [
            CustomerController::class,
            'contacts'
        ]
    )->name('customers.contacts');

     Route::get(
        'customers/{customer}/task-context',
        [
            TaskController::class,
            'customerContext'
        ]
    )->name('customers.task-context');

    Route::get(
        'customers/{customer}/job-context',
        [
            JobController::class,
            'customerContext',
        ]
    )->name('customers.job-context');


    Route::resource(
        'customers',
        CustomerController::class
    );

    Route::resource(
        'contacts',
        ContactController::class
    );

     Route::get(
        'properties/{property}/task-locations',
        [
            TaskController::class,
            'propertyLocations'
        ]
    )->name('properties.task-locations');

    Route::get(
        'properties/{property}/job-locations',
        [
            JobController::class,
            'propertyLocations',
        ]
    )->name('properties.job-locations');

    Route::resource(
        'properties',
        PropertyController::class
    );

    Route::get(
        'property-locations-by-property/{property}',
        [
            PropertyLocationController::class,
            'byProperty'
        ]
    )->name(
        'property-locations.by-property'
    );

    Route::resource(
        'property-locations',
        PropertyLocationController::class
    );
    

    Route::resource(
        'activities',
        ActivityController::class
    );

    Route::post(
        'activities/{activity}/complete',
        [
            ActivityController::class,
            'complete'
        ]
    )->name('activities.complete');

    Route::resource(
        'jobs',
        JobController::class
    );

    Route::resource(
        'tasks',
        TaskController::class
    );
    Route::post(
        'employees/{employee}/toggle-status',
        [EmployeeController::class, 'toggleStatus']
    )->name(
        'employees.toggle-status'
    );

    Route::resource(
        'employees',
        EmployeeController::class
    )->except([
        'destroy',
    ]);

    Route::prefix('jobs/{job}/assignments')
    ->name('jobs.assignments.')
    ->group(function () {

        Route::get(
            '/',
            [JobAssignmentController::class, 'index']
        )->name('index');

        Route::get(
            '/create',
            [JobAssignmentController::class, 'create']
        )->name('create');

        Route::post(
            '/',
            [JobAssignmentController::class, 'store']
        )->name('store');

        Route::get(
            '/{assignment}/edit',
            [JobAssignmentController::class, 'edit']
        )->name('edit');

        Route::put(
            '/{assignment}',
            [JobAssignmentController::class, 'update']
        )->name('update');

        Route::delete(
            '/{assignment}',
            [JobAssignmentController::class, 'destroy']
        )->name('destroy');

        Route::post(
            '/{assignment}/primary',
            [JobAssignmentController::class, 'makePrimary']
        )->name('primary');

    });

    Route::get(
        'job-schedules/calendar',
        [JobScheduleController::class, 'calendar']
    )->name('job-schedules.calendar');

    Route::get(
        'job-schedules/events',
        [JobScheduleController::class, 'events']
    )->name('job-schedules.events');

    Route::get(
        'job-schedules/assignment-employees',
        [JobScheduleController::class, 'assignmentEmployees']
    )->name('job-schedules.assignment-employees');

    Route::resource(
        'job-schedules',
        JobScheduleController::class
    );

    Route::resource(
        'estimates',
        EstimateController::class
    )->names('estimates');

    

    Route::resource(
        'invoices',
        InvoiceController::class
    );
 
});
