<?php
namespace App\Imports;

use App\Models\LetterHead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LetterHeadImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Check and convert date to Y-m-d format
        if (isset($row['date'])) {
            $date = Date::excelToDateTimeObject($row['date'])->format('Y-m-d');
        } else {
            // If date is missing, set to null (or use a default value like '1970-01-01' for debugging)
            $date = null;
        }

        // Debugging log
        if ($date === null) {
            \Log::error("Invalid or missing date for row: " . json_encode($row));
        }

        return new LetterHead([
            'date' => $date,  // Convert Excel serial date to a readable date format
            'name' => $row['name'] ?? 'N/A',  // If 'name' column is missing, set a default value
            'ref_no' => $row['ref_no'],
            'description' => $row['description'] ?? 'No description provided', // Default description if missing
        ]);
    }
}


