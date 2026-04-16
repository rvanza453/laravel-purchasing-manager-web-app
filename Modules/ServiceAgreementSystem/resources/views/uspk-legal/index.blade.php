<x-serviceagreementsystem::layouts.master :title="'Review Legal SPK'">
    <div class="d-flex justify-between align-center mb-4">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: var(--text-primary);">Review Legal SPK</h1>
            <p class="text-muted" style="font-size: 14px;">Daftar USPK yang sudah approved final dan menunggu dokumen SPK final dari Legal.</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. USPK</th>
                        <th>Judul Pekerjaan</th>
                        <th>Department</th>
                        <th>Pengaju</th>
                        <th>Pemenang Final</th>
                        <th>Tanggal Approved</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $uspk)
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">
                            <a href="{{ route('sas.uspk.show', $uspk) }}" style="color: var(--accent); text-decoration: none;">
                                {{ $uspk->uspk_number }}
                            </a>
                        </td>
                        <td>{{ Str::limit($uspk->title, 45) }}</td>
                        <td>{{ $uspk->department->name ?? '-' }}</td>
                        <td>{{ $uspk->submitter->name ?? '-' }}</td>
                        <td>
                            @if($uspk->selectedTender)
                                <strong>{{ $uspk->selectedTender->contractor->name ?? '-' }}</strong>
                            @else
                                <span class="text-muted">Belum ada</span>
                            @endif
                        </td>
                        <td>{{ optional($uspk->updated_at)->format('d M Y H:i') }}</td>
                        <td class="text-right">
                            <div class="d-flex gap-2" style="justify-content: flex-end;">
                                <a href="{{ route('sas.uspk.show', $uspk) }}" class="btn btn-secondary btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sas.uspk-legal.export', $uspk) }}" class="btn btn-primary btn-sm" title="Export Draft SPK">
                                    <i class="fas fa-file-export"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                <p class="mb-2" style="font-weight: 600; font-size: 16px; color: var(--text-primary);">Tidak Ada Antrean Legal</p>
                                <p class="text-muted">Semua USPK approved sudah memiliki dokumen SPK final.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="pagination-wrapper">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</x-serviceagreementsystem::layouts.master>
