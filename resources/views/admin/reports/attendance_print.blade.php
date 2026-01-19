<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900">
    <div class="max-w-6xl mx-auto px-6 py-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">Laporan Absensi Siswa</h1>
                <div class="text-sm text-gray-600 mt-1">Periode: {{ $rangeLabel }}</div>
            </div>
            <button onclick="window.print()" class="border border-gray-300 rounded px-4 py-2 text-sm hover:bg-gray-50">
                Print
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Tanggal</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Nama Siswa</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Kelas</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Jam Masuk</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Jam Pulang</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Lokasi</th>
                        <th class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 px-4 py-3 border-b border-gray-200">Status</th>
                    </tr>
                </thead>
                <tbody>
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
                        @endphp
                        <tr class="odd:bg-white even:bg-gray-50">
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $row['nama'] }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $row['kelas'] }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $row['jam_masuk'] }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $row['jam_pulang'] }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $row['lokasi_url'] ?? '-' }}</td>
                            <td class="px-4 py-3 border-b border-gray-200 text-sm">{{ $statusLabel }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

