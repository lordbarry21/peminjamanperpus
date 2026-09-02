{{-- Menggunakan Layout Induk 'layouts.app' --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Buku Baru') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Form Tambah Buku: Mengirim data ke Route admin.buku.store dengan method POST --}}
                <form action="{{ route('admin.buku.store') }}" method="POST">
                    {{-- @csrf: Token Keamanan Wajib di Laravel untuk mencegah serangan Cross-Site Request Forgery --}}
                    @csrf
                    
                    {{-- Input Kode Buku --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Kode Buku</label>
                        <input type="text" name="kode_buku" class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Contoh: BK-001" required>
                    </div>

                    {{-- Input Judul Buku --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Judul Buku</label>
                        <input type="text" name="judul" class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Masukkan judul buku" required>
                    </div>

                    {{-- Input Nama Pengarang --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Pengarang</label>
                        <input type="text" name="pengarang" class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Nama penulis/pengarang" required>
                    </div>

                    {{-- Input Nama Penerbit --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Penerbit</label>
                        <input type="text" name="penerbit" class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Nama penerbit buku" required>
                    </div>

                    {{-- Input Jumlah Stok --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Stok</label>
                        <input type="number" name="stok" class="border-gray-300 rounded-md shadow-sm w-full" placeholder="Jumlah buku fisik" min="0" required>
                    </div>

                    {{-- Tombol Aksi --}}
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Simpan</button>
                    <a href="{{ route('admin.buku.index') }}" class="ml-2 text-gray-600 hover:underline">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
