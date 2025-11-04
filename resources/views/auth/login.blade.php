<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-Permit</title>
    {{-- Tailwind dari Mix --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="antialiased text-white bg-gray-900">

    <section class="min-h-screen flex items-stretch">
        {{-- LEFT PANEL (Image) --}}
        <div class="hidden lg:flex w-1/2 bg-gray-600 bg-cover bg-center relative items-center" 
            style="background-image: url('{{ asset('images/bg-login.jpg') }}');">
            <div class="absolute inset-0 bg-black opacity-60"></div>
            <div class="z-10 px-10">
                <h1 class="text-5xl font-bold mb-3">Selamat Datang di <span class="text-blue-400">E-Permit</span></h1>
                <p class="text-lg text-gray-200 leading-relaxed">
                    Sistem perizinan digital yang cepat, aman, dan efisien untuk lingkungan kerja modern.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL (Form) --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-900 px-6 lg:px-12 relative z-10">
            <div class="w-full max-w-md space-y-8">
                {{-- Logo --}}
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-white">E-Permit</h2>
                    <p class="text-gray-400 mt-2">Silakan masuk untuk melanjutkan</p>
                </div>

                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- FORM LOGIN --}}
                <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium mb-1 text-gray-300">Email</label>
                        <input type="email" name="email" id="email" required autofocus
                            class="w-full px-4 py-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-400 transition duration-150"
                            value="{{ old('email') }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-1 text-gray-300">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-400 transition duration-150">
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-gray-400">
                            <input type="checkbox" name="remember" class="mr-2 rounded border-gray-600 bg-gray-800">
                            Ingat saya
                        </label>
                        <a href="#" class="text-blue-400 hover:underline">Lupa password?</a>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                        Masuk
                    </button>
                </form>

                {{-- FOOTER --}}
                <p class="text-center text-gray-500 text-sm mt-8">
                    © {{ date('Y') }} E-Permit. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </section>
</body>
</html>
