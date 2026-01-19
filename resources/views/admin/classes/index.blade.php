@extends('admin.layout')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Manajemen Kelas</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Tambah Kelas -->
        <div class="md:col-span-1">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Kelas Baru</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Masukkan nama kelas baru untuk ditambahkan ke sistem.</p>
                    </div>
                    <form class="mt-5" action="{{ route('admin.classes.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="name" class="sr-only">Nama Kelas</label>
                            <input type="text" name="name" id="name" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Contoh: X-1" required>
                        </div>
                        <button type="submit" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                            Tambah
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Kelas -->
        <div class="md:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Daftar Kelas</h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                    @forelse($classes as $class)
                        <li class="px-4 py-4 flex items-center justify-between sm:px-6 hover:bg-gray-50">
                            <div class="text-sm font-medium text-blue-600 truncate">
                                {{ $class->name }}
                            </div>
                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-sm text-gray-500 text-center">
                            Belum ada data kelas.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
