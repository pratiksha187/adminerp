<?php

namespace App\Imports;

use App\Models\EmployeePayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayrollImport implements WithMultipleSheets
{
    protected $fileName;
    protected $month;
    protected $year;

    public function __construct($fileName, $month = null, $year = null)
    {
        $this->fileName = $fileName;
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            'Final' => new FinalSheetImport($this->fileName, $this->month, $this->year),
        ];
    }
}

class FinalSheetImport implements ToCollection, WithCalculatedFormulas
{
    protected $fileName;
    protected $month;
    protected $year;

    public function __construct($fileName, $month = null, $year = null)
    {
        $this->fileName = $fileName;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index < 3) {
                continue;
            }

            $row = $row->toArray();

            if (empty($row[1])) {
                continue;
            }

            EmployeePayment::create([
                'month'                => $this->month,
                'year'                 => $this->year,
                'employee_name'        => trim((string)($row[1] ?? '')),
                'designation'          => $row[2] ?? null,
                'joining_date'         => $this->excelDate($row[3] ?? null),

                'gross_salary_monthly' => $this->num($row[36] ?? 0),
                'per_day_salary'       => $this->num($row[37] ?? 0),

                'half_days'            => 0,
                'present_days'         => $this->num($row[39] ?? 0),
                'weekly_off'           => $this->num($row[40] ?? 0),
                'paid_leave'           => $this->num($row[42] ?? 0),
                'extra_days'           => 0,
                'total_days'           => $this->num($row[46] ?? 0),

                'basic_salary'         => $this->num($row[47] ?? 0),
                'hra'                  => $this->num($row[48] ?? 0),
                'conveyance'           => $this->num($row[49] ?? 0),
                'other_allowance'      => $this->num($row[50] ?? 0),

                'gross_earnings'       => $this->num($row[52] ?? 0),

                'pf'                   => $this->num($row[53] ?? 0),
                'esic'                 => $this->num($row[54] ?? 0),
                'pt'                   => $this->num($row[55] ?? 0),
                'advance_amount'       => $this->num($row[56] ?? 0),
                'ot_amount'            => $this->num($row[51] ?? 0),
                'leave_deduction'      => 0,

                'total_deduction'      => $this->num($row[61] ?? 0),
                'net_payable'          => $this->num($row[62] ?? 0),

                'excel_file_name'      => $this->fileName,
            ]);
        }
    }

    private function num($value)
    {
        return ($value === null || $value === '') ? 0 : (float) $value;
    }

    private function excelDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                )->format('Y-m-d');
            }

            return $value ? Carbon::parse($value)->format('Y-m-d') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}