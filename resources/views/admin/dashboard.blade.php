@extends('admin.layout')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Siswa -->
        <div class="bg-[#7CA9C2] rounded-lg p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Total Siswa</h3>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalStudents }}</p>
        </div>

        <!-- Total Kelas -->
        <div class="bg-[#F0CF7D] rounded-lg p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Total Kelas</h3>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalClasses }}</p>
        </div>

        <!-- Izin Hari Ini -->
        <div class="bg-[#D65A4A] rounded-lg p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Izin Hari Ini</h3>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $permissionCount }}</p>
        </div>

        <!-- Absen Hari Ini -->
        <div class="bg-[#B08BB0] rounded-lg p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Absen Hari Ini</h3>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $presentCount }}</p>
        </div>
    </div>

    <!-- Rekap Singkat -->
    <div class="bg-gray-100 rounded-lg p-6 shadow-sm mb-8">
        <h3 class="text-sm font-medium text-gray-500">Rekap Singkat</h3>
        <div class="mt-2">
            <h2 class="text-2xl font-bold text-gray-900">Lihat Rekap</h2>
            <p class="text-green-500 font-semibold mt-1 text-sm">+0%</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="mt-8">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Kehadiran Hari Ini ({{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }})</h2>
        <div class="flex flex-col mt-4">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $attendance->status == 'hadir' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="https://maps.google.com/?q={{ $attendance->check_in_location }}" target="_blank" class="text-blue-600 hover:text-blue-900">Lihat Peta</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data absensi hari ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
