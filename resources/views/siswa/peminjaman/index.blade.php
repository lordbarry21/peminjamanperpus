{{-- Menggunakan Layout Induk 'layouts.app' bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- Slot Header: Menampilkan judul pada navigasi atas --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message: Notifikasi Sukses atau Error --}}
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
                
                {{-- Header Konten --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Seluruh Transaksi Peminjaman</h3>
                        <p class="text-sm text-gray-500">Mencatat riwayat buku yang sedang aktif Anda pinjam maupun yang sudah dikembalikan.</p>
                    </div>

                    <a href="{{ route('siswa.buku.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md transition shadow-sm self-start md:self-auto">
                        + Pinjam Buku Baru
                    </a>
                </div>

                {{-- Tabel Riwayat Peminjaman --}}
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700 w-12">No</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Kode Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Judul Buku</th>
                                <th class="border p-3 text-left text-sm font-semibold text-gray-700">Pengarang</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Tanggal Pinjam</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Tanggal Kembali</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="border p-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatPeminjaman as $index => $pinjam)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="border p-3 text-center text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="border p-3 font-semibold text-gray-800 text-sm">{{ $pinjam->buku->kode_buku }}</td>
                                    <td class="border p-3 text-gray-900 font-medium text-sm">{{ $pinjam->buku->judul }}</td>
                                    <td class="border p-3 text-gray-600 text-sm">{{ $pinjam->buku->pengarang }}</td>
                                    <td class="border p-3 text-center text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="border p-3 text-center text-sm text-gray-700">
                                        @if ($pinjam->tanggal_kembali)
                                            {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->translatedFormat('d M Y') }}
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="border p-3 text-center">
                                        @if ($pinjam->status === 'dipinjam')
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                                Dipinjam
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                Dikembalikan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border p-3 text-center">
                                        @if ($pinjam->status === 'dipinjam')
                                            {{-- Tombol Kembalikan Buku --}}
                                            <form action="{{ route('siswa.peminjaman.kembalikan', $pinjam->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan buku &quot;{{ $pinjam->buku->judul }}&quot;?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md shadow-sm transition">
                                                    Kembalikan Buku
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="font-medium text-gray-600">Belum ada riwayat transaksi peminjaman buku.</p>
                                            <a href="{{ route('siswa.buku.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
                                                Mulai pinjam buku dari katalog &rarr;
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
