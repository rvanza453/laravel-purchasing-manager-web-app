@extends('systemsupport::components.layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('systemsupport.tickets.index') }}" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Report a System Issue</h2>
            <p class="text-sm text-gray-500">Tim kami siap membantu Anda menyelesaikan kendala teknis.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-900/40 border border-red-500/50 text-red-300 px-4 py-4 rounded-xl mb-6 shadow-lg shadow-red-900/10 backdrop-blur-sm">
            <div class="flex items-center gap-2 mb-2 font-bold"><i class="fa-solid fa-triangle-exclamation text-red-500"></i> Ada kesalahan pada form:</div>
            <ul class="list-disc list-inside text-sm mt-1 ml-2 space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-panel shadow-2xl relative overflow-hidden">
        <!-- Decoration line -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
        
        <form action="{{ route('systemsupport.tickets.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="form-label" for="title">
                        <i class="fa-solid fa-heading text-blue-400 w-4 mr-1"></i> Ticket Subject / Judul Masalah
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="form-input text-lg py-3 placeholder:text-gray-700" 
                           placeholder="Contoh: Error 500 saat mencoba simpan PR baru" required>
                    <p class="text-xs text-gray-600 mt-2">Buat judul singkat dan jelas mengenai keluhan Anda.</p>
                </div>

                <!-- Module -->
                <div>
                    <label class="form-label" for="module">
                        <i class="fa-solid fa-layer-group text-blue-400 w-4 mr-1"></i> Modul Terkait
                    </label>
                    <div class="relative">
                        <select name="module" id="module" class="form-select appearance-none cursor-pointer" required>
                            <option value="" disabled selected>-- Pilih Modul --</option>
                            <option value="Service Agreement System (SAS)">Service Agreement System (SAS)</option>
                            <option value="Purchase Request (PR)">Purchase Request (PR)</option>
                            <option value="System ISPO">System ISPO</option>
                            <option value="QC Complaint">QC Complaint</option>
                            <option value="Global Management">Global Management (User/Role)</option>
                            <option value="Lainnya">Lainnya / Umum</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                    </div>
                </div>

                <!-- Priority -->
                <div>
                    <label class="form-label" for="priority">
                        <i class="fa-solid fa-bolt text-blue-400 w-4 mr-1"></i> Tingkat Prioritas
                    </label>
                    <div class="relative">
                        <select name="priority" id="priority" class="form-select appearance-none cursor-pointer" required>
                            <option value="Low">Low (Bukan isu mendesak)</option>
                            <option value="Medium" selected>Medium (Mengganggu pekerjaan minor)</option>
                            <option value="High">High (Fungsi inti tidak jalan namun ada jalan pintas)</option>
                            <option value="Urgent">Urgent (Sistem mati total atau fatal error)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-10">
                <label class="form-label" for="description">
                    <i class="fa-solid fa-align-left text-blue-400 w-4 mr-1"></i> Rincian Keluhan
                </label>
                <textarea name="description" id="description" rows="7" 
                          class="form-textarea placeholder:text-gray-700 leading-relaxed" 
                          placeholder="Mohon jelaskan secara berurutan langkah-langkah sebelum error terjadi, atau keterangan tambahan yang membantu tim IT melacak penyebabnya..." required>{{ old('description') }}</textarea>
                <div class="flex justify-between mt-2">
                    <p class="text-xs text-gray-600">Terima markdown text / bullet points sederhana.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-[#30363d] flex items-center justify-end gap-4">
                <button type="button" onclick="window.history.back()" class="px-6 py-2.5 rounded-lg text-sm font-semibold text-gray-400 hover:text-white transition">Cancel</button>
                <button type="submit" class="btn-neon px-8 py-2.5 shadow-[0_0_15px_rgba(35,134,54,0.4)] flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
