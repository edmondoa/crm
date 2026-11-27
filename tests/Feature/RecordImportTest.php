<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class RecordImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_customer_csv_import_generates_codes_and_skips_invalid_or_duplicate_rows(): void
    {
        Customer::query()->create([
            'customer_code' => 'CUST-EXISTS',
            'customer_type' => 'individual',
            'first_name' => 'Existing',
            'last_name' => 'Customer',
            'country' => 'Philippines',
            'status' => 'active',
        ]);

        $csv = implode("\n", [
            'customer_code,customer_type,company_name,first_name,middle_name,last_name,country,email,status',
            ',individual,,Ada,,Lovelace,Philippines,ada@example.test,active',
            'CUST-IMPORT-001,company,Northstar Group,,,,Philippines,ops@example.test,active',
            'CUST-IMPORT-001,company,Duplicate Group,,,,Philippines,duplicate@example.test,active',
            ',individual,,Invalid,,,Philippines,invalid@example.test,active',
            'CUST-EXISTS,individual,,Changed,,Existing,Philippines,existing@example.test,active',
        ]);

        $this->postWithCsrf(route('crm.customers.import.store'), [
            'file' => UploadedFile::fake()->createWithContent(
                'customers.csv',
                $csv
            ),
        ])
            ->assertRedirect(route('crm.customers.import'))
            ->assertSessionHas('import_result', function (array $result): bool {
                return $result['imported'] === 2
                    && $result['skipped'] === 3
                    && count($result['errors']) === 3;
            });

        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUST-00001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
        ]);
        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUST-IMPORT-001',
            'company_name' => 'Northstar Group',
            'customer_type' => 'company',
        ]);
        $this->assertDatabaseMissing('customers', [
            'first_name' => 'Changed',
        ]);
        $this->assertSame(3, Customer::query()->count());
    }

    public function test_employee_xlsx_import_creates_employee_and_skips_duplicate_numbers(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray([
            [
                'employee_no',
                'first_name',
                'middle_name',
                'last_name',
                'employment_type',
                'status',
                'email',
            ],
            [
                'EMP-IMPORT-001',
                'Jamie',
                '',
                'Fox',
                'Full Time',
                'Active',
                'jamie@example.test',
            ],
            [
                'EMP-IMPORT-001',
                'Duplicate',
                '',
                'Employee',
                'Part Time',
                'Active',
                'duplicate@example.test',
            ],
        ]);

        $tempFile = tempnam(sys_get_temp_dir(), 'employee-import-');
        (new Xlsx($spreadsheet))->save($tempFile);
        $workbook = file_get_contents($tempFile);
        unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        $this->postWithCsrf(route('crm.employees.import.store'), [
            'file' => UploadedFile::fake()->createWithContent(
                'employees.xlsx',
                $workbook
            ),
        ])
            ->assertRedirect(route('crm.employees.import'))
            ->assertSessionHas('import_result', function (array $result): bool {
                return $result['imported'] === 1
                    && $result['skipped'] === 1
                    && count($result['errors']) === 1;
            });

        $this->assertDatabaseHas('employees', [
            'employee_no' => 'EMP-IMPORT-001',
            'first_name' => 'Jamie',
            'employment_type' => 'full_time',
            'status' => 'active',
        ]);
        $this->assertDatabaseMissing('employees', [
            'first_name' => 'Duplicate',
        ]);
        $this->assertSame(1, Employee::query()->count());
    }

    public function test_import_pages_and_csv_templates_are_available_for_both_record_types(): void
    {
        $this->get(route('crm.customers.import'))
            ->assertOk()
            ->assertSee('Import Customers')
            ->assertSee('customer_type');

        $this->get(route('crm.employees.import'))
            ->assertOk()
            ->assertSee('Import Employees')
            ->assertSee('employee_no');

        $this->get(route('crm.customers.import.template'))
            ->assertDownload('customers-template.csv');

        $this->get(route('crm.employees.import.template'))
            ->assertDownload('employees-template.csv');
    }

    private function postWithCsrf(string $uri, array $data)
    {
        $token = str_repeat('a', 40);

        return $this->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->post($uri, $data);
    }
}
