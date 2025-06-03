<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(AttendanceRequest $request)
    {
        $student = Student::query()->where('qrcode', $request->qrcode)->first();
        $attendance = Attendance::query()->create([
            'student_id' => $student->id,
            'lesson_id' => $request->lesson_id,
        ]);
        return new AttendanceResource($attendance);
    }

    public function changeAttendanceStatus(Request $request)
    {

        $request->validate([
            'qrcode' => 'required|exists:attendances,qrcode',
        ]);
        $attendance = Attendance::query()->where('qrcode', $request->qrcode)->first();
        $attendance->update([
            'attended' => true,
        ]);
        return new AttendanceResource($attendance);
    }

    public function deleteAttendance(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json([
            'success' => true,
            'message' => 'api.success'
        ]);

    }
}
