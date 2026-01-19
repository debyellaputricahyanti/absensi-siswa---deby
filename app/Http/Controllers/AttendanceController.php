<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $checkInTimeLimit = Setting::where('key', 'check_in_time')->value('value') ?? '07:00:00';

        return view('dashboard', compact('user', 'attendance', 'history', 'checkInTimeLimit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        // Check if already checked in
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Geofencing Check
        if (! $this->checkLocation($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar area absensi.');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in_time' => Carbon::now(),
            'check_in_location' => $request->latitude.','.$request->longitude,
            'status' => 'hadir',
        ]);

        return back()->with('success', 'Absen masuk berhasil.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah melakukan absen pulang.');
        }

        // Geofencing Check
        if (! $this->checkLocation($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar area absensi.');
        }

        $checkOutLimit = Setting::where('key', 'check_out_time')->value('value') ?? '15:00:00';
        $note = $attendance->note;
        if (Carbon::now()->format('H:i:s') < $checkOutLimit) {
            $note = $note ? $note.', Pulang Cepat' : 'Pulang Cepat';
        }

        $attendance->update([
            'check_out_time' => Carbon::now(),
            'check_out_location' => $request->latitude.','.$request->longitude,
            'note' => $note,
        ]);

        return back()->with('success', 'Absen pulang berhasil.');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check if already present or permission exists
        $existing = Attendance::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah absen atau mengajukan izin pada tanggal ini.');
        }

        $existingPermission = Permission::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->first();

        if ($existingPermission) {
            return back()->with('error', 'Anda sudah mengajukan izin pada tanggal ini.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('permissions', 'public');
        }

        Permission::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'reason' => $request->reason,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Izin berhasil diajukan, menunggu persetujuan admin.');
    }

    private function checkLocation($lat, $lon)
    {
        $schoolLat = Setting::where('key', 'latitude')->value('value');
        $schoolLon = Setting::where('key', 'longitude')->value('value');
        $radius = Setting::where('key', 'radius')->value('value'); // meters

        if (! $schoolLat || ! $schoolLon || ! $radius) {
            // If settings are missing, allow attendance (or handle as error)
            // For now, let's assume valid if settings are missing to avoid lockout during setup
            return true;
        }

        $distance = $this->calculateDistance($lat, $lon, $schoolLat, $schoolLon);

        return $distance <= $radius;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
