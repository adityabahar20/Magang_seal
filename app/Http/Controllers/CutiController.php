<?php

namespace App\Http\Controllers;
use App\Services\LeaveRequestServices;
use App\Models\datacuti as LeaveRequest;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    protected $service;

    public function __construct(LeaveRequestServices $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        // Validasi input wajib 
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        try {
            $leave = $this->service->submitRequest($request->all(), auth()->user());
            return response()->json(['message' => 'Pengajuan berhasil terkirim', 'data' => $leave], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        // Employee hanya bisa memantau status pengajuannya sendiri 
        $leaves = auth()->user()->leaveRequests()->latest()->get()->map(function ($leave) {
        $leave->attachment = asset('storage/' . $leave->attachment);
        return $leave;
        });

        return response()->json([
            'message' => 'Daftar pengajuan cuti Anda',
            'data' => $leaves
        ]);
    }

    // app/Http/Controllers/LeaveRequestController.php

public function update(Request $request, $id)
{
    $leave = LeaveRequest::where('user_id', auth()->id())
                ->where('id_cuti', $id)
                ->firstOrFail();

    if ($leave->status !== 'pending') {
        return response()->json(['message' => 'Hanya pengajuan pending yang bisa diubah'], 400);
    }

    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string',
    ]);

    $leave->update($request->all());

    return response()->json([
        'message' => 'Pengajuan berhasil diperbarui',
        'data' => $leave
    ]);
}

    public function destroy($id)
    {
    $leave = LeaveRequest::where('user_id', auth()->id())
                ->where('id_cuti', $id)
                ->firstOrFail();

    if ($leave->status !== 'pending') {
        return response()->json(['message' => 'Hanya pengajuan pending yang bisa dibatalkan'], 400);
    }

    $leave->delete();

    return response()->json(['message' => 'Pengajuan cuti berhasil dihapus']);
}

    // Melihat SEMUA pengajuan (Hanya Admin) 
    public function allRequests()
    {
        $leaves = LeaveRequest::with('user')->latest()->get()->map(function ($leave) {
            $leave->attachment = asset('storage/' . $leave->attachment);
            return $leave;
        });
        return response()->json(['data' => $leaves]);
    }

    // Update Status: Approve atau Reject 
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        try {
            $leave = $this->service->updateStatus($id, $request->status);
            return response()->json([
                'message' => "Status berhasil diubah menjadi {$request->status}",
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
