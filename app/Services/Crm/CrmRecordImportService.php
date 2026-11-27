<?php

namespace App\Services\Crm;

use App\Models\CrmSetting;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Throwable;

class CrmRecordImportService
{
    private const MAX_FILE_KILOBYTES = 10240;
    private const MAX_ROWS = 2000;
    private const MAX_REPORTED_ERRORS = 25;

    public function headers(string $type): array
    {
        return match ($type) {
            'customers' => [
                'customer_code',
                'customer_type',
                'company_name',
                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'email',
                'phone',
                'mobile',
                'tax_id',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'zip_code',
                'country',
                'notes',
                'status',
            ],
            'employees' => [
                'employee_no',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'email',
                'phone',
                'position',
                'department',
                'employment_type',
                'hire_date',
                'hourly_rate',
                'status',
                'address',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'notes',
            ],
            default => throw new \InvalidArgumentException('Unknown import type.'),
        };
    }

    public function importCustomers(UploadedFile $file): array
    {
        return $this->import($file, 'customers');
    }

    public function importEmployees(UploadedFile $file): array
    {
        return $this->import($file, 'employees');
    }

    private function import(UploadedFile $file, string $type): array
    {
        $rows = $this->readRows($file, $type);
        $records = [];
        $errors = [];
        $skipped = 0;

        $existingIdentifiers = $type === 'customers'
            ? Customer::query()->pluck('customer_code')->all()
            : Employee::query()->pluck('employee_no')->all();
        $existingIdentifiers = array_fill_keys(
            array_map(
                fn ($value) => mb_strtolower(trim((string) $value)),
                $existingIdentifiers
            ),
            true
        );

        $customerCodePrefix = $type === 'customers'
            ? (CrmSetting::active()->customer_code_prefix ?: 'CUST-')
            : '';
        $customerCodeSequence = $type === 'customers'
            ? $this->nextCustomerCodeSequence($customerCodePrefix)
            : null;

        foreach ($rows as $row) {
            if ($this->isBlankRow($row['values'])) {
                continue;
            }

            $record = $this->mapRow($row['values'], $row['headers']);
            $record = $this->normalizeRecord($record, $type);
            $validator = Validator::make(
                $record,
                $this->rules($type)
            );

            if ($validator->fails()) {
                $skipped++;
                $this->addErrors(
                    $errors,
                    $row['number'],
                    $validator->errors()->all()
                );
                continue;
            }

            $identifierField = $type === 'customers'
                ? 'customer_code'
                : 'employee_no';
            $identifier = trim((string) ($record[$identifierField] ?? ''));

            if ($type === 'customers' && $identifier === '') {
                $identifier = $this->generateCustomerCode(
                    $customerCodeSequence,
                    $existingIdentifiers,
                    $customerCodePrefix
                );
                $record[$identifierField] = $identifier;
            }

            $identifierKey = mb_strtolower($identifier);

            if (isset($existingIdentifiers[$identifierKey])) {
                $skipped++;
                $this->addErrors(
                    $errors,
                    $row['number'],
                    ["{$identifierField} '{$identifier}' already exists; row skipped."]
                );
                continue;
            }

            $existingIdentifiers[$identifierKey] = true;
            $records[] = $record;
        }

        if ($records !== []) {
            DB::transaction(function () use ($records, $type) {
                foreach ($records as $record) {
                    if ($type === 'customers') {
                        Customer::query()->create($record);
                    } else {
                        Employee::query()->create($record);
                    }
                }
            });
        }

        return [
            'imported' => count($records),
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function readRows(UploadedFile $file, string $type): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'xlsx'], true)) {
            throw ValidationException::withMessages([
                'file' => 'Upload a CSV or .xlsx file.',
            ]);
        }

        if ($file->getSize() > self::MAX_FILE_KILOBYTES * 1024) {
            throw ValidationException::withMessages([
                'file' => 'The import file must be 10 MB or smaller.',
            ]);
        }

        try {
            $reader = IOFactory::createReader(
                $extension === 'csv' ? 'Csv' : 'Xlsx'
            );
            $reader->setReadDataOnly(true);

            if ($reader instanceof Csv) {
                $reader->setInputEncoding('UTF-8');
                $reader->setDelimiter(',');
                $reader->setEnclosure('"');
            }

            $spreadsheet = $reader->load($file->getRealPath());
            $matrix = $spreadsheet->getActiveSheet()
                ->toArray(null, false, true, false);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'file' => 'The file could not be read. Check that it is a valid CSV or .xlsx workbook.',
            ]);
        }

        $headerIndex = null;

        foreach ($matrix as $index => $values) {
            if (!$this->isBlankRow($values)) {
                $headerIndex = $index;
                break;
            }
        }

        if ($headerIndex === null) {
            throw ValidationException::withMessages([
                'file' => 'The file is empty. Add a header row and at least one record.',
            ]);
        }

        $headers = array_map(
            fn ($header) => $this->normalizeHeader($header),
            $matrix[$headerIndex]
        );

        if (count(array_filter($headers)) !== count(array_unique(array_filter($headers)))) {
            throw ValidationException::withMessages([
                'file' => 'The file contains duplicate column headers. Keep each header only once.',
            ]);
        }

        $requiredHeaders = $type === 'customers'
            ? ['customer_type', 'country']
            : ['employee_no', 'first_name', 'last_name'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if ($missingHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => 'Missing required columns: ' . implode(', ', $missingHeaders) . '.',
            ]);
        }

        $rows = [];

        foreach (array_slice($matrix, $headerIndex + 1, null, true) as $index => $values) {
            $rows[] = [
                'number' => $index + 1,
                'headers' => $headers,
                'values' => array_values($values),
            ];
        }

        $nonBlankRows = array_filter(
            $rows,
            fn ($row) => !$this->isBlankRow($row['values'])
        );

        if (count($nonBlankRows) > self::MAX_ROWS) {
            throw ValidationException::withMessages([
                'file' => 'The file exceeds the 2,000-row import limit.',
            ]);
        }

        if ($nonBlankRows === []) {
            throw ValidationException::withMessages([
                'file' => 'No data rows were found below the header row.',
            ]);
        }

        return $rows;
    }

    private function mapRow(array $values, array $headers): array
    {
        $record = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $record[$header] = $this->normalizeValue($values[$index] ?? null);
        }

        return $record;
    }

    private function normalizeRecord(array $record, string $type): array
    {
        $allowedFields = $this->headers($type);
        $record = array_intersect_key($record, array_fill_keys($allowedFields, true));

        foreach ($record as $field => $value) {
            if (in_array($field, ['customer_type', 'status', 'employment_type'], true)) {
                $record[$field] = $value === null
                    ? null
                    : strtolower(str_replace([' ', '-'], '_', $value));
            }
        }

        if ($type === 'customers') {
            $record['status'] = $record['status'] ?: 'active';
        } else {
            $record['employment_type'] = $record['employment_type'] ?: 'full_time';
            $record['status'] = $record['status'] ?: 'active';
        }

        return $record;
    }

    private function rules(string $type): array
    {
        if ($type === 'customers') {
            return [
                'customer_code' => ['nullable', 'string', 'max:50'],
                'customer_type' => ['required', 'in:individual,company'],
                'company_name' => ['required_if:customer_type,company', 'nullable', 'string', 'max:255'],
                'first_name' => ['required_if:customer_type,individual', 'nullable', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'last_name' => ['required_if:customer_type,individual', 'nullable', 'string', 'max:100'],
                'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'mobile' => ['nullable', 'string', 'max:50'],
                'tax_id' => ['nullable', 'string', 'max:100'],
                'address_line_1' => ['nullable', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:2'],
                'zip_code' => ['nullable', 'string', 'max:10'],
                'country' => ['required', 'string', 'max:100'],
                'notes' => ['nullable', 'string'],
                'status' => ['required', 'in:active,inactive'],
            ];
        }

        return [
            'employee_no' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,temporary,casual,project_based'],
            'hire_date' => ['nullable', 'date'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,on_leave,terminated'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function nextCustomerCodeSequence(string $prefix): int
    {
        $lastCode = Customer::query()
            ->whereNotNull('customer_code')
            ->orderByDesc('id')
            ->value('customer_code');

        if (!$lastCode) {
            return 1;
        }

        return ((int) preg_replace(
            '/\D/',
            '',
            str_replace($prefix, '', $lastCode)
        )) + 1;
    }

    private function generateCustomerCode(
        int &$sequence,
        array $existingIdentifiers,
        string $prefix
    ): string {
        do {
            $code = $prefix . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
            $sequence++;
        } while (isset($existingIdentifiers[mb_strtolower($code)]));

        return $code;
    }

    private function normalizeHeader(mixed $header): string
    {
        $header = ltrim((string) $header, "\xEF\xBB\xBF");
        $header = strtolower(trim($header));

        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $header), '_');
    }

    private function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function isBlankRow(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function addErrors(array &$errors, int $rowNumber, array $messages): void
    {
        foreach ($messages as $message) {
            if (count($errors) >= self::MAX_REPORTED_ERRORS) {
                return;
            }

            $errors[] = "Row {$rowNumber}: {$message}";
        }
    }
}
