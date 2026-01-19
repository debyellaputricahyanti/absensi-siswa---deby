@extends('admin.layout')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Pengaturan Lokasi Absensi</h1>

    <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Koordinat Sekolah</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Tentukan titik pusat dan radius area absensi yang diizinkan.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <div class="mt-1">
                            <input type="text" name="latitude" id="latitude" value="{{ $settings['latitude'] ?? '' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Contoh: -6.175392</p>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <div class="mt-1">
                            <input type="text" name="longitude" id="longitude" value="{{ $settings['longitude'] ?? '' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Contoh: 106.827153</p>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="radius" class="block text-sm font-medium text-gray-700">Radius (Meter)</label>
                        <div class="mt-1">
                            <input type="number" name="radius" id="radius" value="{{ $settings['radius'] ?? '100' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Jarak maksimum siswa dari titik pusat untuk bisa absen.</p>
                    </div>

                    <div class="sm:col-span-6">
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-800">
                            Pengaturan jam masuk dan jam pulang sekarang dipisah di halaman
                            <a href="{{ route('admin.settings.time') }}" class="font-semibold underline">Pengaturan Waktu Absensi</a>.
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Pengaturan
                    </button>
                    <button type="button" onclick="getCurrentLocation()" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Gunakan Lokasi Saya Saat Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    alert("Error getting location: " + error.message);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
@endsection
