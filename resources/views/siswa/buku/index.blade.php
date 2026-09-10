{{-- Menggunakan Layout Induk 'layouts.app' bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- Slot Header: Menampilkan judul pada navigasi atas --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Koleksi Buku Perpustakaan') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Bagian Atas: Deskripsi & Form Pencarian Buku --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Buku Tersedia</h3>
                        <p class="text-sm text-gray-500">Pilih buku yang ingin Anda baca dan pinjam langsung secara online.</p>
                    </div>

                    {{-- Form Filter Pencarian Buku --}}
                    <form method="GET" action="{{ route('siswa.buku.index') }}" class="flex items-center gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search ?? '' }}" 
                            placeholder="Cari judul, pengarang, kode..." 
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                        />
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition shadow-sm">
                            Cari
                        </button>
                        @if (!empty($search))
                            <a href="{{ route('siswa.buku.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-2 rounded-md border border-gray-300 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Tabel Katalog Buku --}}
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Kode Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Judul Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Pengarang</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Penerbit</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Sisa Stok</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bukus as $buku)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="border p-3 font-semibold text-gray-800">{{ $buku->kode_buku }}</td>
                                    <td class="border p-3 text-gray-900 font-medium">{{ $buku->judul }}</td>
                                    <td class="border p-3 text-gray-600">{{ $buku->pengarang }}</td>
                                    <td class="border p-3 text-gray-600">{{ $buku->penerbit }}</td>
                                    <td class="border p-3 text-center">
                                        @if ($buku->stok > 0)
                                            <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                {{ $buku->stok }} Tersedia
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                                Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border p-3 text-center">
                                        @if (in_array($buku->id, $bukuDipinjamIds))
                                            {{-- Kondisi 1: Buku sudah dipinjam oleh siswa aktif --}}
                                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                                Sedang Anda Pinjam
                                            </span>
                                        @elseif ($buku->stok > 0)
                                            {{-- Kondisi 2: Stok tersedia dan belum dipinjam --}}
                                            <form action="{{ route('siswa.peminjaman.store') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin meminjam buku &quot;{{ $buku->judul }}&quot;?')">
                                                @csrf
                                                <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md shadow-sm transition">
                                                    + Pinjam Buku
                                                </button>
                                            </form>
                                        @else
                                            {{-- Kondisi 3: Stok habis --}}
                                            <button type="button" disabled class="bg-gray-200 text-gray-400 cursor-not-allowed text-xs font-semibold px-3 py-1.5 rounded-md">
                                                Tidak Tersedia
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <p class="font-medium text-gray-600">Tidak ada buku yang ditemukan.</p>
                                        @if (!empty($search))
                                            <p class="text-sm mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                                        @endif
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
