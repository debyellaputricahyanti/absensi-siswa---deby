<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $attendances = Attendance::with('user')
            ->where('date', $today)
            ->get();

        $checkInTime = Setting::where('key', 'check_in_time')->value('value') ?? '07:00:00';
        $totalStudents = User::where('role', 'student')->count();
        $presentCount = $attendances->where('status', 'hadir')->count();
        $lateCount = $attendances->where('status', 'hadir')
            ->where('check_in_time', '>', $checkInTime)
            ->count();

        $totalClasses = User::where('role', 'student')->distinct('kelas')->count('kelas');
        $permissionCount = $attendances->whereIn('status', ['izin', 'sakit'])->count();

        return view('admin.dashboard', compact('attendances', 'totalStudents', 'presentCount', 'lateCount', 'totalClasses', 'permissionCount'));
    }

    public function users()
    {
        $today = Carbon::today();

        $users = User::where('role', 'student')
            ->with(['attendances' => function ($query) use ($today) {
                $query->where('date', $today);
            }])
            ->latest()
            ->get();

        return view('admin.users.index', compact('users', 'today'));
    }

    public function createUser()
    {
        $classes = SchoolClass::orderBy('name')->pluck('name');

        return view('admin.users.create', compact('classes'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:50|unique:users,nis',
            'kelas' => 'required|string|max:50|exists:school_classes,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        return redirect()->route('admin.users')->with('success', 'User siswa berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::where('role', 'student')->findOrFail($id);

        $classes = SchoolClass::orderBy('name')->pluck('name');
        if ($user->kelas && ! $classes->contains($user->kelas)) {
            $classes = collect([$user->kelas])->merge($classes);
        }

        return view('admin.users.edit', compact('user', 'classes'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::where('role', 'student')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => ['required', 'string', 'max:50', Rule::unique('users', 'nis')->ignore($user->id)],
            'kelas' => 'required|string|max:50|exists:school_classes,name',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $request->name;
        $user->nis = $request->nis;
        $user->kelas = $request->kelas;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User siswa berhasil dihapus.');
    }

    public function settings()
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric',
        ]);

        Setting::updateOrCreate(['key' => 'latitude'], ['value' => $request->latitude]);
        Setting::updateOrCreate(['key' => 'longitude'], ['value' => $request->longitude]);
        Setting::updateOrCreate(['key' => 'radius'], ['value' => $request->radius]);

        return back()->with('success', 'Pengaturan lokasi berhasil disimpan.');
    }

    public function timeSettings()
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings_time', compact('settings'));
    }

    public function updateTimeSettings(Request $request)
    {
        $request->validate([
            'check_in_time' => 'required',
            'check_out_time' => 'required',
        ]);

        Setting::updateOrCreate(['key' => 'check_in_time'], ['value' => $request->check_in_time]);
        Setting::updateOrCreate(['key' => 'check_out_time'], ['value' => $request->check_out_time]);

        return back()->with('success', 'Pengaturan waktu berhasil disimpan.');
    }

    public function classes()
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:school_classes,name',
        ]);

        SchoolClass::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function destroyClass($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function permissions()
    {
        $permissions = Permission::with('user')->latest()->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function approvePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update(['status' => 'approved']);

        $exists = Attendance::where('user_id', $permission->user_id)
            ->where('date', $permission->date)
            ->exists();
        if (! $exists) {
            Attendance::create([
                'user_id' => $permission->user_id,
                'date' => $permission->date,
                'status' => 'izin',
                'note' => $permission->reason,
            ]);
        }

        return back()->with('success', 'Izin disetujui.');
    }

    public function rejectPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update(['status' => 'rejected']);

        return back()->with('success', 'Izin ditolak.');
    }

    public function showPermissionEvidence($id)
    {
        $permission = Permission::findOrFail($id);

        if (! $permission->image) {
            abort(404);
        }

        $path = storage_path('app/public/'.$permission->image);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function attendanceReport(Request $request)
    {
        $today = Carbon::today();
        $checkInTimeLimit = Setting::where('key', 'check_in_time')->value('value') ?? '07:00:00';

        $search = trim((string) $request->query('search', ''));
        $kelas = (string) $request->query('kelas', 'all');
        $startDateInput = $request->query('start_date');
        $endDateInput = $request->query('end_date');

        $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : $today->copy();
        $endDate = $endDateInput ? Carbon::parse($endDateInput)->startOfDay() : $startDate->copy();

        if ($endDate->lessThan($startDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $statsTotalHadir = Attendance::where('date', $today)
            ->where('status', 'hadir')
            ->count();
        $statsTotalTerlambat = Attendance::where('date', $today)
            ->where('status', 'hadir')
            ->where('check_in_time', '>', $checkInTimeLimit)
            ->count();
        $statsTotalIzinSakit = Attendance::where('date', $today)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        $classOptions = User::where('role', 'student')
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $isSingleDate = $startDate->equalTo($endDate);
        $export = (string) $request->query('export', '');

        if ($isSingleDate) {
            $date = $startDate->toDateString();

            $studentsQuery = User::where('role', 'student');
            if ($search !== '') {
                $studentsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('nis', 'like', '%'.$search.'%');
                });
            }
            if ($kelas !== '' && $kelas !== 'all') {
                $studentsQuery->where('kelas', $kelas);
            }

            $students = $studentsQuery->orderBy('name')->get();
            $attendances = Attendance::with('user')
                ->where('date', $date)
                ->whereIn('user_id', $students->pluck('id'))
                ->get()
                ->keyBy('user_id');

            $rows = $students->map(function ($student) use ($attendances, $checkInTimeLimit, $date) {
                $attendance = $attendances->get($student->id);
                $checkInTime = $attendance?->check_in_time;
                $checkOutTime = $attendance?->check_out_time;

                $status = $attendance?->status ?? 'alpa';
                if ($status === 'hadir' && $checkInTime && $checkInTime > $checkInTimeLimit) {
                    $status = 'terlambat';
                }

                $location = $attendance?->check_in_location;
                $locationUrl = $location ? 'https://maps.google.com/?q='.$location : null;

                return [
                    'tanggal' => $date,
                    'nama' => $student->name,
                    'kelas' => $student->kelas ?? '-',
                    'jam_masuk' => $checkInTime ? Carbon::parse($checkInTime)->format('H:i') : '-',
                    'jam_pulang' => $checkOutTime ? Carbon::parse($checkOutTime)->format('H:i') : '-',
                    'lokasi_url' => $locationUrl,
                    'status' => $status,
                ];
            });

            if ($export === 'csv') {
                return response()->streamDownload(function () use ($rows) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['Tanggal', 'Nama Siswa', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Lokasi', 'Status']);
                    foreach ($rows as $row) {
                        fputcsv($out, [
                            $row['tanggal'],
                            $row['nama'],
                            $row['kelas'],
                            $row['jam_masuk'],
                            $row['jam_pulang'],
                            $row['lokasi_url'] ?? '-',
                            ucfirst($row['status']),
                        ]);
                    }
                    fclose($out);
                }, 'laporan-absensi-'.$date.'.csv', ['Content-Type' => 'text/csv']);
            }

            if ($export === 'print') {
                return view('admin.reports.attendance_print', [
                    'rows' => $rows,
                    'rangeLabel' => Carbon::parse($date)->format('d/m/Y'),
                ]);
            }

            $perPage = 10;
            $page = LengthAwarePaginator::resolveCurrentPage();
            $items = $rows->slice(($page - 1) * $perPage, $perPage)->values();
            $paginator = new LengthAwarePaginator($items, $rows->count(), $perPage, $page, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);

            return view('admin.reports.attendance', [
                'rows' => $paginator,
                'classOptions' => $classOptions,
                'filters' => [
                    'search' => $search,
                    'kelas' => $kelas,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'stats' => [
                    'hadir' => $statsTotalHadir,
                    'terlambat' => $statsTotalTerlambat,
                    'izin_sakit' => $statsTotalIzinSakit,
                ],
                'rangeLabel' => $startDate->format('d/m/Y'),
                'isSingleDate' => true,
            ]);
        }

        $attendancesQuery = Attendance::with('user')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('user', function ($q) use ($search, $kelas) {
                $q->where('role', 'student');
                if ($search !== '') {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('nis', 'like', '%'.$search.'%');
                    });
                }
                if ($kelas !== '' && $kelas !== 'all') {
                    $q->where('kelas', $kelas);
                }
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($export === 'csv') {
            return response()->streamDownload(function () use ($attendancesQuery, $checkInTimeLimit) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Nama Siswa', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Lokasi', 'Status']);
                foreach ($attendancesQuery->cursor() as $attendance) {
                    $checkInTime = $attendance->check_in_time;
                    $checkOutTime = $attendance->check_out_time;
                    $status = $attendance->status;
                    if ($status === 'hadir' && $checkInTime && $checkInTime > $checkInTimeLimit) {
                        $status = 'terlambat';
                    }
                    $location = $attendance->check_in_location;
                    $locationUrl = $location ? 'https://maps.google.com/?q='.$location : null;

                    fputcsv($out, [
                        Carbon::parse($attendance->date)->toDateString(),
                        $attendance->user?->name ?? '-',
                        $attendance->user?->kelas ?? '-',
                        $checkInTime ? Carbon::parse($checkInTime)->format('H:i') : '-',
                        $checkOutTime ? Carbon::parse($checkOutTime)->format('H:i') : '-',
                        $locationUrl ?? '-',
                        ucfirst($status),
                    ]);
                }
                fclose($out);
            }, 'laporan-absensi-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
        }

        if ($export === 'print') {
            $rows = $attendancesQuery->get()->map(function ($attendance) use ($checkInTimeLimit) {
                $checkInTime = $attendance->check_in_time;
                $checkOutTime = $attendance->check_out_time;
                $status = $attendance->status;
                if ($status === 'hadir' && $checkInTime && $checkInTime > $checkInTimeLimit) {
                    $status = 'terlambat';
                }
                $location = $attendance->check_in_location;
                $locationUrl = $location ? 'https://maps.google.com/?q='.$location : null;

                return [
                    'tanggal' => Carbon::parse($attendance->date)->toDateString(),
                    'nama' => $attendance->user?->name ?? '-',
                    'kelas' => $attendance->user?->kelas ?? '-',
                    'jam_masuk' => $checkInTime ? Carbon::parse($checkInTime)->format('H:i') : '-',
                    'jam_pulang' => $checkOutTime ? Carbon::parse($checkOutTime)->format('H:i') : '-',
                    'lokasi_url' => $locationUrl,
                    'status' => $status,
                ];
            });

            return view('admin.reports.attendance_print', [
                'rows' => $rows,
                'rangeLabel' => $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y'),
            ]);
        }

        $paginator = $attendancesQuery->paginate(10)->appends($request->query());
        $rows = $paginator->getCollection()->map(function ($attendance) use ($checkInTimeLimit) {
            $checkInTime = $attendance->check_in_time;
            $checkOutTime = $attendance->check_out_time;
            $status = $attendance->status;
            if ($status === 'hadir' && $checkInTime && $checkInTime > $checkInTimeLimit) {
                $status = 'terlambat';
            }

            $location = $attendance->check_in_location;
            $locationUrl = $location ? 'https://maps.google.com/?q='.$location : null;

            return [
                'tanggal' => Carbon::parse($attendance->date)->toDateString(),
                'nama' => $attendance->user?->name ?? '-',
                'kelas' => $attendance->user?->kelas ?? '-',
                'jam_masuk' => $checkInTime ? Carbon::parse($checkInTime)->format('H:i') : '-',
                'jam_pulang' => $checkOutTime ? Carbon::parse($checkOutTime)->format('H:i') : '-',
                'lokasi_url' => $locationUrl,
                'status' => $status,
            ];
        });
        $paginator->setCollection($rows);

        return view('admin.reports.attendance', [
            'rows' => $paginator,
            'classOptions' => $classOptions,
            'filters' => [
                'search' => $search,
                'kelas' => $kelas,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'stats' => [
                'hadir' => $statsTotalHadir,
                'terlambat' => $statsTotalTerlambat,
                'izin_sakit' => $statsTotalIzinSakit,
            ],
            'rangeLabel' => $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y'),
            'isSingleDate' => false,
        ]);
    }
}
