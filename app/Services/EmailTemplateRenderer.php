<?php

namespace App\Services;

use Illuminate\Support\HtmlString;

class EmailTemplateRenderer
{
    /** Only these keys are allowed to be interpolated into a template. */
    public const PLACEHOLDERS = [
        'customer_name' => 'Customer name',
        'contact_name' => 'Contact name',
        'company_name' => 'Company name',
        'sender_name' => 'Sender name',
        'sender_email' => 'Sender email',
        'invoice_number' => 'Invoice number',
        'invoice_url' => 'Invoice URL',
        'job_number' => 'Job number',
        'job_title' => 'Job title',
        'property_address' => 'Property address',
        'due_date' => 'Due date',
        'amount_due' => 'Amount due',
        'portal_url' => 'Customer portal URL',
    ];

    public function sampleData(): array
    {
        return [
            'customer_name' => 'Jordan Smith',
            'contact_name' => 'Jordan Smith',
            'company_name' => config('app.name', 'Your Company'),
            'sender_name' => auth()->user()?->name ?? 'CRM Team',
            'sender_email' => auth()->user()?->email ?? 'team@example.com',
            'invoice_number' => 'INV-10025',
            'invoice_url' => url('/invoices/INV-10025'),
            'job_number' => 'JOB-2041',
            'job_title' => 'Kitchen Renovation',
            'property_address' => '123 Example Street, Springfield',
            'due_date' => now()->addDays(14)->format('F j, Y'),
            'amount_due' => '$1,250.00',
            'portal_url' => url('/'),
        ];
    }

    public function render(string $content, array $data = [], bool $escapeValues = true): string
    {
        $allowed = array_intersect_key($data, self::PLACEHOLDERS);
        $values = [];
        foreach ($allowed as $key => $value) {
            $value = (string) ($value ?? '');
            $values[$key] = $escapeValues ? e($value) : $value;
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', function ($matches) use ($values) {
            // Unknown placeholders become an empty string, never executable code.
            return $values[$matches[1]] ?? '';
        }, $content) ?? $content;
    }

    public function renderSubject(string $subject, array $data = []): string
    {
        return trim(strip_tags($this->render($subject, $data)));
    }
}
