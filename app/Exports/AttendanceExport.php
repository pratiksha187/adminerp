<?php
namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{
    protected $start, $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        $attendances = Attendance::with('user')
            ->whereBetween('clock_in', [$this->start . ' 00:00:00', $this->end . ' 23:59:59'])
            ->orderBy('clock_in', 'desc')
            ->get();

        return view('admin.attendance_excel', compact('attendances'));
    }
}
