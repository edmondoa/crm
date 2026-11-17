<?php

namespace App\Observers;

use App\Models\Customer;
use App\Mail\CustomerWelcomeMail;
use Illuminate\Support\Facades\Mail;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        if (empty($customer->email)) {
            return;
        }

        Mail::to($customer->email)
            ->queue(new CustomerWelcomeMail($customer));
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
