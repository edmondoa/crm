<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\CrmEstimate;
use App\Models\CrmEstimateItem;
use App\Models\CrmSetting;
use App\Models\Customer;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\JobSchedule;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyLocation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;

class CrmDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new LogicException('CRM demo data cannot be seeded in production.');
        }

        $user = User::firstOrCreate(
            ['email' => 'crm-demo@example.test'],
            [
                'name' => 'CRM Demo User',
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(64)),
            ]
        );

        $this->seedSettings();

        $customers = $this->seedCustomers();
        $contacts = $this->seedContacts($customers);
        $properties = $this->seedProperties($customers, $contacts);
        $locations = $this->seedPropertyLocations($properties);
        $employees = $this->seedEmployees();
        $jobs = $this->seedJobs($customers, $contacts, $properties, $locations);
        $assignments = $this->seedAssignments($jobs, $employees);

        $this->seedSchedules($jobs, $assignments, $employees, $locations);
        $this->seedActivities($customers, $contacts, $properties, $locations);
        $this->seedTasks($customers, $contacts, $properties, $locations);

        $lead = $this->seedLead($user);
        $this->seedOpportunity($lead, $customers['company'], $user);
        $this->seedLeadActivity($lead, $user);

        $estimate = $this->seedEstimate($customers['individual'], $properties['home'], $jobs['maintenance']);
        $this->seedEstimateItem($estimate);
        $invoice = $this->seedInvoice($customers['individual'], $properties['home'], $jobs['maintenance'], $estimate);
        $this->seedInvoiceItem($invoice);
        $this->seedPayment($invoice, $user);

        $template = $this->seedEmailTemplate($user);
        $this->seedEmailCampaigns($template, $user);
    }

    private function seedSettings(): void
    {
        CrmSetting::query()->firstOrCreate([], [
            'customer_code_prefix' => 'CUST',
            'property_code_prefix' => 'PROP',
            'task_code_prefix' => 'TASK',
            'job_code_prefix' => 'JOB',
            'quotation_code_prefix' => 'EST',
            'lead_code_prefix' => 'LEAD',
            'opportunity_code_prefix' => 'OPP',
            'enabled' => true,
        ]);
    }

    private function seedCustomers(): array
    {
        $individual = Customer::updateOrCreate(
            ['customer_code' => 'CUST-DEMO-001'],
            [
                'customer_type' => 'individual',
                'first_name' => 'Avery',
                'last_name' => 'Santos',
                'email' => 'avery.santos@example.test',
                'phone' => '+63 2 555 0101',
                'mobile' => '+63 917 555 0101',
                'address_line_1' => '18 Sampaguita Street',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1100',
                'country' => 'Philippines',
                'notes' => 'Demo customer record for testing CRM workflows.',
                'status' => 'active',
            ]
        );

        $company = Customer::updateOrCreate(
            ['customer_code' => 'CUST-DEMO-002'],
            [
                'customer_type' => 'company',
                'company_name' => 'Northstar Office Group',
                'email' => 'office@example.test',
                'phone' => '+63 2 555 0200',
                'address_line_1' => '240 Aurora Boulevard',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1109',
                'country' => 'Philippines',
                'notes' => 'Demo company customer with linked property and contact.',
                'status' => 'active',
            ]
        );

        return compact('individual', 'company');
    }

    private function seedContacts(array $customers): array
    {
        $individual = Contact::updateOrCreate(
            ['customer_id' => $customers['individual']->id, 'email' => 'avery.santos@example.test'],
            [
                'contact_type' => 'primary',
                'first_name' => 'Avery',
                'last_name' => 'Santos',
                'job_title' => 'Homeowner',
                'email' => 'avery.santos@example.test',
                'phone' => '+63 2 555 0101',
                'mobile' => '+63 917 555 0101',
                'address_line_1' => '18 Sampaguita Street',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1100',
                'country' => 'Philippines',
                'is_primary' => true,
                'preferred_contact_method' => 'email',
                'status' => 'active',
                'notes' => 'Primary demo contact.',
            ]
        );

        $company = Contact::updateOrCreate(
            ['customer_id' => $customers['company']->id, 'email' => 'jordan.lee@example.test'],
            [
                'contact_type' => 'primary',
                'first_name' => 'Jordan',
                'last_name' => 'Lee',
                'job_title' => 'Facilities Manager',
                'department' => 'Operations',
                'email' => 'jordan.lee@example.test',
                'phone' => '+63 2 555 0201',
                'mobile' => '+63 917 555 0201',
                'address_line_1' => '240 Aurora Boulevard',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1109',
                'country' => 'Philippines',
                'is_primary' => true,
                'preferred_contact_method' => 'mobile',
                'status' => 'active',
                'notes' => 'Primary office contact for the demo company.',
            ]
        );

        return compact('individual', 'company');
    }

    private function seedProperties(array $customers, array $contacts): array
    {
        $home = Property::updateOrCreate(
            ['property_code' => 'PROP-DEMO-001'],
            [
                'customer_id' => $customers['individual']->id,
                'primary_contact_id' => $contacts['individual']->id,
                'name' => 'Sampaguita Residence',
                'property_type' => 'residential',
                'description' => 'Demo residential property.',
                'status' => 'active',
                'notes' => 'Use for estimate, job, and invoice testing.',
            ]
        );

        $office = Property::updateOrCreate(
            ['property_code' => 'PROP-DEMO-002'],
            [
                'customer_id' => $customers['company']->id,
                'primary_contact_id' => $contacts['company']->id,
                'name' => 'Northstar Main Office',
                'property_type' => 'office',
                'description' => 'Demo commercial office property.',
                'status' => 'active',
                'notes' => 'Use for service scheduling and property list testing.',
            ]
        );

        return compact('home', 'office');
    }

    private function seedPropertyLocations(array $properties): array
    {
        $home = PropertyLocation::updateOrCreate(
            ['location_code' => 'LOC-DEMO-001'],
            [
                'property_id' => $properties['home']->id,
                'location_name' => 'Main residence',
                'location_type' => 'residential',
                'address_line_1' => '18 Sampaguita Street',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1100',
                'county' => 'Quezon City',
                'latitude' => 14.6760,
                'longitude' => 121.0437,
                'is_primary' => true,
                'status' => 'active',
                'notes' => 'Primary demo service location.',
            ]
        );

        $office = PropertyLocation::updateOrCreate(
            ['location_code' => 'LOC-DEMO-002'],
            [
                'property_id' => $properties['office']->id,
                'location_name' => 'Office service entrance',
                'location_type' => 'job_site',
                'address_line_1' => '240 Aurora Boulevard',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'zip_code' => '1109',
                'county' => 'Quezon City',
                'latitude' => 14.6235,
                'longitude' => 121.0509,
                'is_primary' => true,
                'status' => 'active',
                'notes' => 'Demo office service location.',
            ]
        );

        return compact('home', 'office');
    }

    private function seedEmployees(): array
    {
        $technician = Employee::updateOrCreate(
            ['employee_no' => 'EMP-DEMO-001'],
            [
                'first_name' => 'Taylor',
                'last_name' => 'Reyes',
                'email' => 'taylor.reyes@example.test',
                'phone' => '+63 917 555 0301',
                'position' => 'Service Technician',
                'department' => 'Field Operations',
                'employment_type' => 'full_time',
                'hire_date' => '2024-06-15',
                'hourly_rate' => 350,
                'status' => 'active',
                'address' => 'Quezon City, Metro Manila',
                'emergency_contact_name' => 'Casey Reyes',
                'emergency_contact_relationship' => 'Sibling',
                'emergency_contact_phone' => '+63 917 555 0302',
                'notes' => 'Demo employee for assignment and schedule testing.',
            ]
        );

        $coordinator = Employee::updateOrCreate(
            ['employee_no' => 'EMP-DEMO-002'],
            [
                'first_name' => 'Morgan',
                'last_name' => 'Cruz',
                'email' => 'morgan.cruz@example.test',
                'phone' => '+63 917 555 0401',
                'position' => 'Operations Coordinator',
                'department' => 'Operations',
                'employment_type' => 'full_time',
                'hire_date' => '2023-11-01',
                'hourly_rate' => 420,
                'status' => 'active',
                'address' => 'Manila, Metro Manila',
                'notes' => 'Demo coordinator for operations testing.',
            ]
        );

        return compact('technician', 'coordinator');
    }

    private function seedJobs(array $customers, array $contacts, array $properties, array $locations): array
    {
        $start = today()->addDays(2)->setTime(9, 0);
        $maintenance = Job::updateOrCreate(
            ['job_code' => 'JOB-DEMO-001'],
            [
                'work_order_number' => 'WO-DEMO-001',
                'customer_id' => $customers['individual']->id,
                'contact_id' => $contacts['individual']->id,
                'property_id' => $properties['home']->id,
                'property_location_id' => $locations['home']->id,
                'title' => 'Preventive maintenance visit',
                'job_type' => 'maintenance',
                'description' => 'Routine inspection and maintenance for demo testing.',
                'scope_of_work' => 'Inspect equipment, complete maintenance, and record findings.',
                'scheduled_start_at' => $start,
                'scheduled_end_at' => $start->copy()->addHours(2),
                'priority' => 'normal',
                'status' => 'scheduled',
                'estimated_amount' => 1120,
                'approved_amount' => 1120,
                'customer_notes' => 'Please call before arriving.',
                'internal_notes' => 'Demo job linked to estimate and invoice.',
            ]
        );

        $installation = Job::updateOrCreate(
            ['job_code' => 'JOB-DEMO-002'],
            [
                'work_order_number' => 'WO-DEMO-002',
                'customer_id' => $customers['company']->id,
                'contact_id' => $contacts['company']->id,
                'property_id' => $properties['office']->id,
                'property_location_id' => $locations['office']->id,
                'title' => 'Office lighting installation',
                'job_type' => 'installation',
                'description' => 'Install replacement lighting in the office.',
                'scope_of_work' => 'Install and test the replacement fixtures.',
                'scheduled_start_at' => today()->subDay()->setTime(8, 0),
                'scheduled_end_at' => today()->subDay()->setTime(12, 0),
                'started_at' => today()->subDay()->setTime(8, 10),
                'priority' => 'high',
                'status' => 'in_progress',
                'estimated_amount' => 4500,
                'approved_amount' => 4500,
                'customer_notes' => 'Coordinate access with the facilities manager.',
                'internal_notes' => 'Demo in-progress job for status filtering.',
            ]
        );

        return compact('maintenance', 'installation');
    }

    private function seedAssignments(array $jobs, array $employees): array
    {
        $maintenance = JobAssignment::updateOrCreate(
            ['job_id' => $jobs['maintenance']->id, 'employee_id' => $employees['technician']->id],
            [
                'assignment_role' => 'Lead technician',
                'is_primary' => true,
                'status' => 'assigned',
                'assigned_at' => now(),
                'estimated_hours' => 2,
                'hourly_rate' => 350,
                'notes' => 'Primary demo assignment.',
            ]
        );

        $installation = JobAssignment::updateOrCreate(
            ['job_id' => $jobs['installation']->id, 'employee_id' => $employees['coordinator']->id],
            [
                'assignment_role' => 'Coordinator',
                'is_primary' => true,
                'status' => 'in_progress',
                'assigned_at' => today()->subDay(),
                'started_at' => today()->subDay()->setTime(8, 10),
                'estimated_hours' => 4,
                'actual_hours' => 1,
                'hourly_rate' => 420,
                'notes' => 'Demo in-progress assignment.',
            ]
        );

        return compact('maintenance', 'installation');
    }

    private function seedSchedules(array $jobs, array $assignments, array $employees, array $locations): void
    {
        JobSchedule::updateOrCreate(
            [
                'job_id' => $jobs['maintenance']->id,
                'employee_id' => $employees['technician']->id,
            ],
            [
                'job_assignment_id' => $assignments['maintenance']->id,
                'scheduled_date' => today()->addDays(2)->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'status' => 'scheduled',
                'location' => $locations['home']->address_line_1.', '.$locations['home']->city,
                'notes' => 'Demo upcoming schedule.',
            ]
        );

        JobSchedule::updateOrCreate(
            [
                'job_id' => $jobs['installation']->id,
                'employee_id' => $employees['coordinator']->id,
            ],
            [
                'job_assignment_id' => $assignments['installation']->id,
                'scheduled_date' => today()->subDay()->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'status' => 'in_progress',
                'location' => $locations['office']->address_line_1.', '.$locations['office']->city,
                'notes' => 'Demo active schedule.',
            ]
        );
    }

    private function seedActivities(array $customers, array $contacts, array $properties, array $locations): void
    {
        Activity::updateOrCreate(
            ['customer_id' => $customers['individual']->id, 'subject' => 'Demo maintenance confirmation'],
            [
                'contact_id' => $contacts['individual']->id,
                'property_id' => $properties['home']->id,
                'property_location_id' => $locations['home']->id,
                'activity_type' => 'call',
                'description' => 'Confirm the upcoming maintenance appointment.',
                'scheduled_at' => today()->addDay()->setTime(15, 0),
                'status' => 'planned',
                'priority' => 'normal',
                'notes' => 'Demo planned activity.',
            ]
        );

        Activity::updateOrCreate(
            ['customer_id' => $customers['company']->id, 'subject' => 'Demo office walk-through'],
            [
                'contact_id' => $contacts['company']->id,
                'property_id' => $properties['office']->id,
                'property_location_id' => $locations['office']->id,
                'activity_type' => 'site_visit',
                'description' => 'Completed the initial office walk-through.',
                'scheduled_at' => today()->subDays(3)->setTime(10, 0),
                'completed_at' => today()->subDays(3)->setTime(10, 45),
                'status' => 'completed',
                'priority' => 'high',
                'notes' => 'Demo completed activity.',
            ]
        );
    }

    private function seedTasks(array $customers, array $contacts, array $properties, array $locations): void
    {
        Task::updateOrCreate(
            ['task_code' => 'TASK-DEMO-001'],
            [
                'customer_id' => $customers['individual']->id,
                'contact_id' => $contacts['individual']->id,
                'property_id' => $properties['home']->id,
                'property_location_id' => $locations['home']->id,
                'task_type' => 'follow_up',
                'subject' => 'Send maintenance visit reminder',
                'description' => 'Confirm the planned visit with the customer.',
                'due_at' => today()->addDay()->setTime(15, 0),
                'priority' => 'normal',
                'status' => 'pending',
                'notes' => 'Demo pending task.',
            ]
        );

        Task::updateOrCreate(
            ['task_code' => 'TASK-DEMO-002'],
            [
                'customer_id' => $customers['company']->id,
                'contact_id' => $contacts['company']->id,
                'property_id' => $properties['office']->id,
                'property_location_id' => $locations['office']->id,
                'task_type' => 'document',
                'subject' => 'Review office service notes',
                'description' => 'Check the notes collected during the walk-through.',
                'due_at' => today()->subDay(),
                'completed_at' => today()->subDay()->setTime(16, 0),
                'priority' => 'high',
                'status' => 'completed',
                'outcome' => 'Notes reviewed and filed.',
                'notes' => 'Demo completed task.',
            ]
        );
    }

    private function seedLead(User $user): Lead
    {
        $lead = Lead::withTrashed()->updateOrCreate(
            ['lead_number' => 'LEAD-DEMO-001'],
            [
                'first_name' => 'Jamie',
                'last_name' => 'Rivera',
                'company_name' => 'Rivera Property Group',
                'email' => 'jamie.rivera@example.test',
                'phone' => '+63 917 555 0501',
                'job_title' => 'Property Manager',
                'source' => 'website',
                'status' => 'new',
                'rating' => 'warm',
                'description' => 'Demo lead interested in recurring maintenance.',
                'follow_up_at' => today()->addDays(3)->setTime(10, 0),
                'assigned_to' => $user->id,
                'created_by' => $user->id,
            ]
        );

        if ($lead->trashed()) {
            $lead->restore();
        }

        return $lead;
    }

    private function seedOpportunity(Lead $lead, Customer $customer, User $user): void
    {
        $opportunity = Opportunity::withTrashed()->updateOrCreate(
            ['opportunity_number' => 'OPP-DEMO-001'],
            [
                'name' => 'Northstar annual service agreement',
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'assigned_to' => $user->id,
                'stage' => 'proposal',
                'estimated_value' => 24000,
                'probability' => 55,
                'expected_close_date' => today()->addDays(30),
                'description' => 'Demo opportunity for pipeline and forecast testing.',
                'created_by' => $user->id,
            ]
        );

        if ($opportunity->trashed()) {
            $opportunity->restore();
        }
    }

    private function seedLeadActivity(Lead $lead, User $user): void
    {
        LeadActivity::updateOrCreate(
            ['lead_id' => $lead->id, 'subject' => 'Demo discovery email'],
            [
                'user_id' => $user->id,
                'type' => 'email',
                'description' => 'Sent an introductory email and requested a discovery call.',
                'due_at' => today()->addDays(2)->setTime(13, 0),
            ]
        );
    }

    private function seedEstimate(Customer $customer, Property $property, Job $job): CrmEstimate
    {
        return CrmEstimate::updateOrCreate(
            ['estimate_number' => 'EST-DEMO-001'],
            [
                'customer_id' => $customer->id,
                'property_id' => $property->id,
                'job_id' => $job->id,
                'estimate_date' => today()->subDays(2),
                'expiration_date' => today()->addDays(28),
                'status' => 'accepted',
                'subtotal' => 1000,
                'discount' => 0,
                'tax' => 120,
                'other_charges' => 0,
                'grand_total' => 1120,
                'notes' => 'Demo estimate linked to the maintenance job.',
                'terms' => 'Valid for 30 days.',
            ]
        );
    }

    private function seedEstimateItem(CrmEstimate $estimate): void
    {
        CrmEstimateItem::updateOrCreate(
            ['estimate_id' => $estimate->id, 'sku' => 'DEMO-MAINTENANCE'],
            [
                'item_type' => 'service',
                'description' => 'Preventive maintenance visit',
                'quantity' => 2,
                'unit' => 'hour',
                'unit_cost' => 350,
                'markup_percent' => 42.86,
                'unit_price' => 500,
                'tax_percent' => 12,
                'tax_amount' => 120,
                'total' => 1000,
                'sort_order' => 1,
            ]
        );
    }

    private function seedInvoice(Customer $customer, Property $property, Job $job, CrmEstimate $estimate): Invoice
    {
        return Invoice::updateOrCreate(
            ['invoice_number' => 'INV-DEMO-001'],
            [
                'customer_id' => $customer->id,
                'property_id' => $property->id,
                'job_id' => $job->id,
                'estimate_id' => $estimate->id,
                'invoice_date' => today()->subDay(),
                'due_date' => today()->addDays(14),
                'status' => 'partial',
                'subtotal' => 1000,
                'discount' => 0,
                'tax' => 120,
                'other_charges' => 0,
                'grand_total' => 1120,
                'amount_paid' => 250,
                'balance_due' => 870,
                'notes' => 'Demo invoice for payment and balance testing.',
                'terms' => 'Payment due within 14 days.',
            ]
        );
    }

    private function seedInvoiceItem(Invoice $invoice): void
    {
        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice->id, 'sku' => 'DEMO-MAINTENANCE'],
            [
                'item_type' => 'service',
                'description' => 'Preventive maintenance visit',
                'quantity' => 2,
                'unit' => 'hour',
                'unit_price' => 500,
                'discount_percent' => 0,
                'tax_percent' => 12,
                'line_subtotal' => 1000,
                'discount_amount' => 0,
                'tax_amount' => 120,
                'line_total' => 1120,
                'sort_order' => 1,
                'notes' => 'Demo invoice line item.',
            ]
        );
    }

    private function seedPayment(Invoice $invoice, User $user): void
    {
        Payment::updateOrCreate(
            ['payment_number' => 'PAY-DEMO-001'],
            [
                'invoice_id' => $invoice->id,
                'amount' => 250,
                'payment_date' => today(),
                'payment_method' => 'bank_transfer',
                'reference_number' => 'DEMO-TRANSFER-001',
                'notes' => 'Demo partial payment.',
                'created_by' => $user->id,
            ]
        );
    }

    private function seedEmailTemplate(User $user): EmailTemplate
    {
        return EmailTemplate::updateOrCreate(
            ['slug' => 'demo-service-update'],
            [
                'name' => 'Demo service update',
                'category' => 'campaign',
                'subject' => 'A service update for {{customer_name}}',
                'body_html' => '<h2>Hello {{customer_name}},</h2><p>Here is a demo service update from {{company_name}}.</p>',
                'description' => 'Demo template for preview and campaign management testing.',
                'status' => true,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );
    }

    private function seedEmailCampaigns(EmailTemplate $template, User $user): void
    {
        EmailCampaign::updateOrCreate(
            ['name' => 'Demo customer service update'],
            [
                'email_template_id' => $template->id,
                'audience_description' => 'Active residential customers',
                'status' => 'draft',
                'scheduled_at' => null,
                'notes' => 'Demo draft campaign. This record does not send email.',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );

        EmailCampaign::updateOrCreate(
            ['name' => 'Demo maintenance reminder plan'],
            [
                'email_template_id' => $template->id,
                'audience_description' => 'Customers with upcoming maintenance visits',
                'status' => 'scheduled',
                'scheduled_at' => now()->addDays(7)->startOfHour(),
                'notes' => 'Planning-only scheduled record; no automatic email is sent.',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );
    }
}
