@extends('admin.layout')

@section('content')
    <div class="mb-4">
        <div class="text-sm text-gray-500">Home / Absensi / Rekapitulasi</div>
        <h1 class="text-3xl font-semibold text-gray-900 mt-2">Rekap Absensi Siswa</h1>
        <div class="text-sm text-gray-500 mt-1">Periode: {{ $rangeLabel }}</div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
            <div class="p-5">
                <div class="text-sm font-medium text-gray-500">Total Hadir Hari Ini</div>
                <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $stats['hadir'] }}</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
            <div class="p-5">
                <div class="text-sm font-medium text-gray-500">Total Terlambat</div>
                <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $stats['terlambat'] }}</div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
            <div class="p-5">
                <div class="text-sm font-medium text-gray-500">Total Izin/Sakit</div>
                <div class="text-3xl font-semibold text-gray-900 mt-2">{{ $stats['izin_sakit'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg border border-gray-100 mb-6">
        <div class="p-5 border-b border-gray-100">
            <div class="text-lg font-semibold text-gray-900">Filter Data</div>
        </div>
        <div class="p-5">
            <form method="GET" action="{{ route('admin.attendance.report') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nama siswa / NIS..." class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        <option value="all" {{ $filters['kelas'] === 'all' ? 'selected' : '' }}>Semua Kelas</option>
                        @foreach($classOptions as $class)
                            <option value="{{ $class }}" {{ $filters['kelas'] === $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm">
                        Terapkan Filter
                    </button>
                </div>
            </form>

            <div class="mt-5 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-6 text-sm">
                    <div class="font-semibold text-blue-600 border-b-2 border-blue-600 pb-1">Harian</div>
                    <div class="text-gray-400">Mingguan</div>
                    <div class="text-gray-400">Bulanan</div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.attendance.report', array_merge(request()->query(), ['export' => 'csv'])) }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-md shadow-sm text-sm">
                        Ekspor Excel
                    </a>
                    <a href="{{ route('admin.attendance.report', array_merge(request()->query(), ['export' => 'print'])) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-md shadow-sm text-sm">
                        Ekspor PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rows as $row)
                        @php
                            $status = $row['status'];
                            $statusLabel = match ($status) {
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpa' => 'Alpa',
                                default => ucfirst($status),
                            };
                            $statusClass = match ($status) {
                                'hadir' => 'bg-green-100 text-green-800',
                                'terlambat' => 'bg-yellow-100 text-yellow-800',
                                'izin' => 'bg-yellow-100 text-yellow-800',
                                'sakit' => 'bg-blue-100 text-blue-800',
                                'alpa' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['nama'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $row['kelas'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $row['jam_masuk'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $row['jam_pulang'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($row['lokasi_url'])
                                    <a href="{{ $row['lokasi_url'] }}" target="_blank" class="text-blue-600 hover:text-blue-900">Lihat Peta</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows instanceof \Illuminate\Pagination\LengthAwarePaginator || $rows instanceof \Illuminate\Pagination\Paginator)
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-600">
                <div>
                    Showing {{ $rows->firstItem() ?? 0 }}-{{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }}
                </div>
                <div class="flex items-center gap-2">
                    @if($rows->onFirstPage())
                        <span class="px-3 py-1 border border-gray-300 rounded text-gray-400">Previous</span>
                    @else
                        <a class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50" href="{{ $rows->previousPageUrl() }}">Previous</a>
                    @endif

                    <span class="px-3 py-1 border border-blue-600 rounded text-blue-600 bg-blue-50">
                        {{ $rows->currentPage() }}
                    </span>

                    @if($rows->hasMorePages())
                        <a class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50" href="{{ $rows->nextPageUrl() }}">Next</a>
                    @else
                        <span class="px-3 py-1 border border-gray-300 rounded text-gray-400">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

