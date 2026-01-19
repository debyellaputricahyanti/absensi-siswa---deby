@extends('admin.layout')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Pengaturan Waktu Absensi</h1>

    <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Jam Masuk dan Pulang</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Atur batas waktu absen masuk dan jam minimal absen pulang.
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.settings.time.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="check_in_time" class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                        <div class="mt-1">
                            <input type="time" name="check_in_time" id="check_in_time" value="{{ $settings['check_in_time'] ?? '07:00' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Siswa yang absen setelah jam ini tetap tercatat hadir, tetapi akan dihitung sebagai terlambat di laporan.
                        </p>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="check_out_time" class="block text-sm font-medium text-gray-700">Jam Pulang</label>
                        <div class="mt-1">
                            <input type="time" name="check_out_time" id="check_out_time" value="{{ $settings['check_out_time'] ?? '15:00' }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Waktu minimal untuk absen pulang.</p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Pengaturan Waktu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

