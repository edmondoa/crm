<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmSetting;
use App\Models\Customer;


class DashboardController extends Controller
{
    public function index()
    {
        $settings = CrmSetting::active();

        /*
         * Customer, Property, Job and Task models will be
         * connected in the succeeding CRM phases.
         *
         * For Phase 1, these remain zero.
         */

        $stats = [
            'customers' => Customer::count(),
            'properties' => 0,
            'open_jobs' => 0,
            'pending_tasks' => 0,
        ];

        return view('crm.dashboard', compact(
            'stats',
            'settings'
        ));
    }
}