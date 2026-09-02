<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4">
                    <a href="{{ route('admin.buku.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        + Tambah Buku Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="border p-3 text-left">Kode Buku</th>
                                <th class="border p-3 text-left">Judul Buku</th>
                                <th class="border p-3 text-left">Pengarang</th>
                                <th class="border p-3 text-left">Penerbit</th>
                                <th class="border p-3 text-center">Stok</th>
                                <th class="border p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bukus as $buku)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="border p-3 font-semibold">{{ $buku->kode_buku }}</td>
                                    <td class="border p-3">{{ $buku->judul }}</td>
                                    <td class="border p-3">{{ $buku->pengarang }}</td>
                                    <td class="border p-3">{{ $buku->penerbit }}</td>
                                    <td class="border p-3 text-center">{{ $buku->stok }}</td>
                                    <td class="border p-3 text-center">
                                        <a href="{{ route('admin.buku.edit', $buku->id) }}" class="text-blue-600 hover:underline mr-3 font-medium">Edit</a>
                                        <form action="{{ route('admin.buku.destroy', $buku->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline font-medium">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">Belum ada data buku.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
