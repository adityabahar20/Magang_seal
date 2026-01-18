<?php
namespace App\Services;

use App\Models\datacuti as LeaveRequest;
use Carbon\Carbon;
use Exception;

class LeaveRequestServices
{
    public function submitRequest(array $data, $user)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        
        // Hitung durasi hari
        $daysRequested = $start->diffInDays($end) + 1;

        // Cek apakah melebihi kuota user 
        if ($user->kuota_cuti < $daysRequested) {
            throw new Exception("Kuota cuti anda telah habis. Sisa: {$user->kuota_cuti} hari.");
        }

        // Upload Attachment 
        $filePath = $data['attachment']->store('leave_attachments', 'public');

        return LeaveRequest::create([
            'user_id' => $user->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'attachment' => $filePath,
            'status' => 'pending'
        ]);
    }
    public function updateStatus($id, $status)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = $status;
        $leave->save();

    if ($status === 'approved') {
        $user = $leave->user;
        $start = Carbon::parse($leave->start_date);
        $end = Carbon::parse($leave->end_date);
        $duration = $start->diffInDays($end) + 1;

        // Potong kuota user 
        $user->kuota_cuti -= $duration;
        $user->save();
    }

    return $leave;
}
}