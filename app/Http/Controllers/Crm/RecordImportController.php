<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\CrmRecordImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RecordImportController extends Controller
{
    public function customers(CrmRecordImportService $imports): View
    {
        return $this->form('customers', $imports);
    }

    public function employees(CrmRecordImportService $imports): View
    {
        return $this->form('employees', $imports);
    }

    public function importCustomers(
        Request $request,
        CrmRecordImportService $imports
    ): RedirectResponse {
        return $this->handleImport($request, $imports, 'customers');
    }

    public function importEmployees(
        Request $request,
        CrmRecordImportService $imports
    ): RedirectResponse {
        return $this->handleImport($request, $imports, 'employees');
    }

    public function customerTemplate(CrmRecordImportService $imports)
    {
        return $this->downloadTemplate(
            $imports->headers('customers'),
            'customers-template.csv'
        );
    }

    public function employeeTemplate(CrmRecordImportService $imports)
    {
        return $this->downloadTemplate(
            $imports->headers('employees'),
            'employees-template.csv'
        );
    }

    private function form(string $type, CrmRecordImportService $imports): View
    {
        $label = $type === 'customers' ? 'Customers' : 'Employees';
        $routePrefix = "crm.{$type}";

        return view('crm.imports.create', [
            'label' => $label,
            'templateRoute' => "{$routePrefix}.import.template",
            'storeRoute' => "{$routePrefix}.import.store",
            'listRoute' => "{$routePrefix}.index",
            'headers' => $imports->headers($type),
            'requiredColumns' => $type === 'customers'
                ? ['customer_type', 'country', 'first_name and last_name for individuals', 'company_name for companies']
                : ['employee_no', 'first_name', 'last_name'],
        ]);
    }

    private function handleImport(
        Request $request,
        CrmRecordImportService $imports,
        string $type
    ): RedirectResponse {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $extension = strtolower($validated['file']->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'xlsx'], true)) {
            throw ValidationException::withMessages([
                'file' => 'Upload a CSV or .xlsx file.',
            ]);
        }

        $result = $type === 'customers'
            ? $imports->importCustomers($validated['file'])
            : $imports->importEmployees($validated['file']);

        return redirect()
            ->route("crm.{$type}.import")
            ->with('import_result', $result);
    }

    private function downloadTemplate(array $headers, string $filename)
    {
        return response()->streamDownload(function () use ($headers) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers);
            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
