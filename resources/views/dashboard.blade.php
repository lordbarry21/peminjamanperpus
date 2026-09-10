{{-- Menggunakan Layout Induk 'layouts.app' bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- Slot Header: Menampilkan judul pada navigasi atas --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message: Menampilkan notifikasi sukses atau error dari Controller --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Kartu Sambutan Siswa --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">
                        Halo, {{ Auth::user()->name }}! 👋
                    </h3>
                    <p class="text-gray-600 mt-1">
                        Selamat datang di Sistem Peminjaman Perpustakaan Digital. Temukan buku favoritmu dan tingkatkan wawasan membaca setiap hari.
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('siswa.buku.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md transition shadow-sm text-sm">
                        📖 Buka Katalog Buku
                    </a>
                    <a href="{{ route('siswa.peminjaman.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-4 py-2 rounded-md transition border border-gray-300 text-sm">
                        📋 Riwayat Lengkap
                    </a>
                </div>
            </div>

            {{-- Grid Kartu Statistik Ringkasan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Kartu 1: Buku Sedang Dipinjam --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $bukuDipinjamCount }}</p>
                            <p class="text-xs text-amber-600 mt-1">Buku yang perlu dikembalikan</p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-full text-amber-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Kartu 2: Buku Sudah Dikembalikan --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sudah Dikembalikan</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $bukuDikembalikanCount }}</p>
                            <p class="text-xs text-green-600 mt-1">Transaksi selesai</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Kartu 3: Total Koleksi Buku --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Koleksi Buku</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalKoleksiBuku }}</p>
                            <p class="text-xs text-blue-600 mt-1">Buku tersedia di perpustakaan</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Peminjaman Aktif (Buku yang Sedang Dipinjam) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">Buku yang Sedang Anda Pinjam</h4>
                        <p class="text-sm text-gray-500">Daftar buku yang saat ini masih Anda pinjam dan belum dikembalikan.</p>
                    </div>
                    @if ($peminjamanAktif->count() > 0)
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                            {{ $peminjamanAktif->count() }} Buku Aktif
                        </span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Kode Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Judul Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Pengarang</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Tanggal Pinjam</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjamanAktif as $pinjam)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="border p-3 font-semibold text-gray-800">{{ $pinjam->buku->kode_buku }}</td>
                                    <td class="border p-3 text-gray-800 font-medium">{{ $pinjam->buku->judul }}</td>
                                    <td class="border p-3 text-gray-600">{{ $pinjam->buku->pengarang }}</td>
                                    <td class="border p-3 text-center text-gray-700">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}</td>
                                    <td class="border p-3 text-center">
                                        <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                            Dipinjam
                                        </span>
                                    </td>
                                    <td class="border p-3 text-center">
                                        {{-- Form Pengembalian Buku --}}
                                        <form action="{{ route('siswa.peminjaman.kembalikan', $pinjam->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md shadow-sm transition">
                                                Kembalikan Buku
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <p class="font-medium text-gray-600">Saat ini Anda tidak memiliki buku yang sedang dipinjam.</p>
                                            <a href="{{ route('siswa.buku.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
                                                Jelajahi katalog buku untuk mulai meminjam &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
