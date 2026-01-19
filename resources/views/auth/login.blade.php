<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.15);
            border: 1px solid #e0f2ff;
        }

        .login-header {
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            color: white;
        }

        .input-field {
            border: 1px solid #dbeafe;
            transition: all 0.2s;
            background-color: #f8fafc;
        }

        .input-field:hover {
            border-color: #93c5fd;
        }

        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
            background-color: white;
        }

        .btn-login {
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: linear-gradient(to right, #2563eb, #3b82f6);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .error-message {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .icon-container {
            background: linear-gradient(135deg, #e0f2ff, #dbeafe);
            border: 2px solid #dbeafe;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md">
        <div class="login-header text-center p-8 rounded-t-lg">
            <div class="mb-5">
                <div class="inline-flex items-center justify-center w-20 h-20 icon-container rounded-full">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white">Sistem Absensi Siswa</h1>
            <p class="text-blue-50 mt-2">Masuk dengan akun Anda</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="error-message text-red-700 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium">Login gagal</p>
                            <ul class="mt-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="email">Email</label>
                    <input
                        class="input-field w-full py-3 px-4 rounded-lg focus:bg-white"
                        id="email"
                        type="email"
                        name="email"
                        placeholder="contoh: siswa@sekolah.com"
                        value="budi@school.com"
                        required
                    >
                </div>

                <div class="mb-7">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="password">Password</label>
                    <input
                        class="input-field w-full py-3 px-4 rounded-lg focus:bg-white"
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        value="password"
                        required
                    >
                </div>

                <div class="mb-6">
                    <button class="btn-login text-white font-medium py-3 px-4 rounded-lg w-full shadow-md" type="submit">
                        Masuk ke Sistem
                    </button>
                </div>
            </form>

            <div class="text-center text-gray-500 text-sm pt-4 border-t border-gray-100">
                <p>© 2026 Sistem Absensi Siswa</p>
            </div>
        </div>
    </div>
</body>
</html>
