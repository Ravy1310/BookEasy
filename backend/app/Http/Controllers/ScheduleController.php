<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    //mengambil semua jadwal operasional mingguan
    public function getSchedules() {
        return response()->json([
            'success' => true,
            'data' => Schedule::orderBy('day_of_week')->get()
        ], 200);
    }

    // memperbarui jadwal (menerima array of schedule)
    public function updateSchedules(Request $request) {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.is_clodes' => 'required|boolean',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
        ]);

        foreach ($request->schedules as $scheduleData) {
            Schedule::updateOrCreate(
                ['day_of_week' => $scheduleData['day_of_week']],
                [
                    'start_time' => $scheduleData['start_time'] ?? null,
                    'end_time' => $scheduleData['end_time'] ?? null,
                    'is_closed' => $scheduleData['is_closed']
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Schedule Update successfully'
        ], 200);
    }

    // mengambil semua hari libur
    public function getHolidays() {
        return rsponse()->json([
            'success' => true,
            'data' => Holiday::orderBy('date', 'asc')->get()
        ], 200);
    }

    // menambah haru libur baru
    public function addHolidays(Request $request) {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'reason' => 'nullable|string|max:255'
        ]);

        $holiday = Holiday::create($request->only(['date', 'reason']));

        return response()->json([
            'success' => true,
            'data' => $holiday
        ], 200);
    }

    // menghapus hari libur berdasarkan tanggal
    public function removeHoliday($date) {
        $holiday = Holiday::where('date', $date)->firstOrFail();
        $holiday->delete();

        return response()->json([
            'success' => true,
            'message' => 'Holiday removed'
        ], 200);
    }
}
