<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

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

        $normalHours = 9; // standard working hours per day

        // Add worked_hours and overtime to each record
        foreach ($attendances as $att) {
            if ($att->clock_in && $att->clock_out) {
                $clockIn = Carbon::parse($att->clock_in);
                $clockOut = Carbon::parse($att->clock_out);

                $hours = $clockOut->diffInHours($clockIn);
                $minutes = $clockOut->diffInMinutes($clockIn) % 60;

                $att->worked_hours = sprintf('%02d hrs %02d min', $hours, $minutes);
                $att->overtime = $hours > $normalHours ? ($hours - $normalHours) . ' hrs' : '0 hrs';
            } else {
                $att->worked_hours = '—';
                $att->overtime = '—';
            }
        }

        return view('admin.attendance_excel', [
            'attendances' => $attendances,
            'start' => $this->start,
            'end' => $this->end
        ]);
    }
}
