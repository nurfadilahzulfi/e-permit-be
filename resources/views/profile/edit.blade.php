@extends('layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')

    {{-- Notifikasi Sukses --}}
    @if (session('status'))
        <div class="mb-6 px-4 py-3 rounded-xl shadow-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium">
            {{ session('status') }}
        </div>
    @endif

    {{-- Container Grid 2 Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM 1: Update Informasi Profile --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Informasi Profile</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Perbarui informasi profile dan alamat email Anda.
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="space-y-5">
                        {{-- Nama --}}
                        <div>
                            <label for="nama" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('nama') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('email') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- NIP --}}
                        <div>
                            <label for="nip" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">NIP</label>
                            <input type="text" id="nip" name="nip" value="{{ old('nip', $user->nip) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('nip') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Grid 2 Kolom untuk Jabatan & Divisi --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jabatan</label>
                                <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" 
                                       class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                       required>
                                @error('jabatan') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="divisi" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Divisi</label>
                                <input type="text" id="divisi" name="divisi" value="{{ old('divisi', $user->divisi) }}" 
                                       class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                       required>
                                @error('divisi') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Perusahaan --}}
                        <div>
                            <label for="perusahaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Perusahaan</label>
                            <input type="text" id="perusahaan" name="perusahaan" value="{{ old('perusahaan', $user->perusahaan) }}" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('perusahaan') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM 2: Update Password --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Ubah Password</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Pastikan akun Anda menggunakan password yang kuat.
                    </p>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="space-y-5">
                        {{-- Password Saat Ini --}}
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('current_password', 'updatePassword') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Baru</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                            @error('password', 'updatePassword') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                                   required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection