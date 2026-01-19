<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                        <span class="ml-2 font-bold text-xl text-gray-800">Absensi Siswa</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4 font-medium">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Guest' }}&background=random" alt="Avatar">
                    <form method="POST" action="{{ route('logout') }}" class="ml-4">
                        @csrf
                        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-4 rounded">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <!-- Greeting -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Selamat Pagi, {{ explode(' ', Auth::user()->name ?? 'User')[0] }}!</h1>
            <p class="text-gray-500 mt-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-8">
            <form action="{{ route('attendance.store') }}" method="POST" id="checkInForm">
                @csrf
                <input type="hidden" name="latitude" id="latIn">
                <input type="hidden" name="longitude" id="lonIn">
                <button type="button" id="btnCheckIn" onclick="submitCheckIn()" class="bg-cyan-400 hover:bg-cyan-500 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-300 w-40 flex justify-center items-center">
                    <span id="textCheckIn">Absen Masuk</span>
                    <svg id="loadCheckIn" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <form action="{{ route('attendance.update') }}" method="POST" id="checkOutForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="latitude" id="latOut">
                <input type="hidden" name="longitude" id="lonOut">
                <button type="button" id="btnCheckOut" onclick="submitCheckOut()" class="bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 px-6 rounded-lg shadow-md border border-gray-200 transition duration-300 w-40 flex justify-center items-center">
                    <span id="textCheckOut">Absen Pulang</span>
                    <svg id="loadCheckOut" class="animate-spin ml-2 h-5 w-5 text-gray-700 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Status Absensi -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Status Absensi Hari Ini</h2>
                <p class="text-gray-500 text-sm mb-4">Update status kehadiran Anda.</p>

                <div class="bg-green-50 rounded-lg p-6 flex items-center justify-center">
                    @if($attendance)
                        @php
                            $limit = isset($checkInTimeLimit) ? \Carbon\Carbon::parse($checkInTimeLimit)->format('H:i:s') : '07:00:00';
                            $isLate = $attendance->status === 'hadir' && $attendance->check_in_time && \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') > $limit;
                        @endphp
                        @if($attendance->status == 'hadir' && ! $isLate)
                            <div class="flex items-center text-green-700 font-bold text-xl">
                                <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Hadir
                            </div>
                        @elseif($attendance->status == 'hadir' && $isLate)
                            <div class="flex items-center text-yellow-700 font-bold text-xl">
                                <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Terlambat
                            </div>
                        @else
                            <div class="text-gray-700 font-bold text-xl">{{ ucfirst($attendance->status) }}</div>
                        @endif
                    @else
                        <div class="text-gray-500 font-medium">Belum Absen</div>
                    @endif
                </div>
            </div>

            <!-- Jam Masuk & Pulang -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Jam Masuk & Pulang</h2>
                <p class="text-gray-500 text-sm mb-4">Detail waktu dan lokasi Anda.</p>

                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center text-green-600 text-sm font-medium mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Lokasi Terverifikasi
                    </div>
                    <div class="font-mono text-gray-800 text-lg">
                        Masuk: {{ $attendance ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '--:--' }} &nbsp;|&nbsp;
                        Pulang: {{ $attendance && $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '--:--' }}
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

            <!-- Riwayat Absensi -->
            <div class="md:col-span-2 bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Riwayat Absensi Singkat</h2>

                <div class="space-y-6">
                    @foreach($history as $item)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 last:border-0">
                        <div class="flex items-center">
                            @if($item->status == 'hadir')
                                <div class="bg-green-100 p-2 rounded-full mr-4">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            @elseif($item->status == 'alpa')
                                <div class="bg-red-100 p-2 rounded-full mr-4">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="bg-yellow-100 p-2 rounded-full mr-4">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->date)->isoFormat('dddd, D MMMM Y') }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($item->status == 'hadir')
                                        Masuk: {{ \Carbon\Carbon::parse($item->check_in_time)->format('H:i') }}, Pulang: {{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('H:i') : '--:--' }}
                                    @else
                                        {{ $item->note ?? 'Tanpa keterangan' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php
                            $limit = isset($checkInTimeLimit) ? \Carbon\Carbon::parse($checkInTimeLimit)->format('H:i:s') : '07:00:00';
                            $itemIsLate = $item->status === 'hadir' && $item->check_in_time && \Carbon\Carbon::parse($item->check_in_time)->format('H:i:s') > $limit;
                        @endphp
                        <div class="font-bold {{ $item->status == 'hadir' ? ($itemIsLate ? 'text-yellow-600' : 'text-gray-900') : ($item->status == 'alpa' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ $item->status == 'hadir' ? ($itemIsLate ? 'Terlambat' : 'Hadir') : ucfirst($item->status) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Izin -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Butuh Izin?</h2>
                <p class="text-gray-500 text-sm mb-6">Jika Anda tidak dapat hadir karena sakit atau keperluan lain, ajukan surat izin di sini.</p>

                <button onclick="openPermissionModal()" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold py-3 px-4 rounded-lg transition duration-300">
                    Ajukan Izin
                </button>
            </div>

        </div>

    </div>

    <!-- Permission Modal -->
    <div id="permissionModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('permission.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Ajukan Izin
                                </h3>
                                <div class="mt-2">
                                    <div class="mb-4">
                                        <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Tanggal:</label>
                                        <input type="date" name="date" id="date" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">Alasan:</label>
                                        <textarea name="reason" id="reason" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Sakit, Izin Keluarga, dll..." required></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Barang Bukti (opsional):</label>
                                        <input type="file" name="image" id="image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <p class="mt-1 text-xs text-gray-500">Unggah foto surat dokter, undangan, atau dokumen pendukung lainnya.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Kirim
                        </button>
                        <button type="button" onclick="closePermissionModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPermissionModal() {
            document.getElementById('permissionModal').classList.remove('hidden');
        }

        function closePermissionModal() {
            document.getElementById('permissionModal').classList.add('hidden');
        }
    </script>

    <script>
        function submitCheckIn() {
            const btn = document.getElementById('btnCheckIn');
            const text = document.getElementById('textCheckIn');
            const load = document.getElementById('loadCheckIn');

            if (navigator.geolocation) {
                // Show loading
                btn.disabled = true;
                text.classList.add('hidden');
                load.classList.remove('hidden');

                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latIn').value = position.coords.latitude;
                    document.getElementById('lonIn').value = position.coords.longitude;
                    document.getElementById('checkInForm').submit();
                }, function(error) {
                    // Reset loading
                    btn.disabled = false;
                    text.classList.remove('hidden');
                    load.classList.add('hidden');

                    let msg = "Error getting location.";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg = "Anda menolak permintaan lokasi. Silakan izinkan akses lokasi untuk absen.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = "Informasi lokasi tidak tersedia.";
                            break;
                        case error.TIMEOUT:
                            msg = "Permintaan lokasi waktu habis.";
                            break;
                        case error.UNKNOWN_ERROR:
                            msg = "Terjadi kesalahan yang tidak diketahui.";
                            break;
                    }
                    alert(msg);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function submitCheckOut() {
            const btn = document.getElementById('btnCheckOut');
            const text = document.getElementById('textCheckOut');
            const load = document.getElementById('loadCheckOut');

            if (navigator.geolocation) {
                // Show loading
                btn.disabled = true;
                text.classList.add('hidden');
                load.classList.remove('hidden');

                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latOut').value = position.coords.latitude;
                    document.getElementById('lonOut').value = position.coords.longitude;
                    document.getElementById('checkOutForm').submit();
                }, function(error) {
                     // Reset loading
                    btn.disabled = false;
                    text.classList.remove('hidden');
                    load.classList.add('hidden');

                    let msg = "Error getting location.";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg = "Anda menolak permintaan lokasi. Silakan izinkan akses lokasi untuk absen.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = "Informasi lokasi tidak tersedia.";
                            break;
                        case error.TIMEOUT:
                            msg = "Permintaan lokasi waktu habis.";
                            break;
                        case error.UNKNOWN_ERROR:
                            msg = "Terjadi kesalahan yang tidak diketahui.";
                            break;
                    }
                    alert(msg);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
</body>
</html>
