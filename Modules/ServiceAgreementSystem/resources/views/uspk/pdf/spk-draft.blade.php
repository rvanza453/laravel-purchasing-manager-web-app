<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .title { text-align: center; font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #4b5563; margin-bottom: 18px; }
        .section { margin-bottom: 14px; }
        .section h3 { font-size: 12px; margin: 0 0 6px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .label { width: 180px; font-weight: 700; background: #f9fafb; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="title">DRAFT DOKUMEN SPK</div>
    <div class="subtitle">Service Agreement System - Untuk Review Legal</div>

    <div class="section">
        <h3>Informasi USPK</h3>
        <table>
            <tr>
                <td class="label">Nomor USPK</td>
                <td>{{ $uspk->uspk_number }}</td>
            </tr>
            <tr>
                <td class="label">Judul Pekerjaan</td>
                <td>{{ $uspk->title }}</td>
            </tr>
            <tr>
                <td class="label">Department / Afdeling</td>
                <td>{{ $uspk->department->name ?? '-' }} / {{ $uspk->subDepartment->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Aktivitas</td>
                <td>{{ $uspk->job->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pengaju</td>
                <td>{{ $uspk->submitter->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Estimasi Awal</td>
                <td>Rp {{ number_format((float) $uspk->estimated_value, 0, ',', '.') }} @if($uspk->estimated_duration) / {{ $uspk->estimated_duration }} hari @endif</td>
            </tr>
            <tr>
                <td class="label">Deskripsi</td>
                <td>{{ $uspk->description ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Pemenang Kontraktor (Keputusan Approver Final)</h3>
        @if($uspk->selectedTender)
            <table>
                <tr>
                    <td class="label">Nama Kontraktor</td>
                    <td>{{ $uspk->selectedTender->contractor->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Perusahaan</td>
                    <td>{{ $uspk->selectedTender->contractor->company_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nilai Nego Final</td>
                    <td>Rp {{ number_format((float) $uspk->selectedTender->tender_value, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Durasi Final</td>
                    <td>{{ $uspk->selectedTender->tender_duration ? $uspk->selectedTender->tender_duration . ' hari' : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Catatan Nego</td>
                    <td>{{ $uspk->selectedTender->description ?: '-' }}</td>
                </tr>
            </table>
        @else
            <p class="muted">Belum ada pemenang final.</p>
        @endif
    </div>

    <div class="section">
        <h3>Lampiran Tender Pembanding</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kontraktor</th>
                    <th>Nilai</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($uspk->tenders as $idx => $tender)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $tender->contractor->name ?? '-' }}</td>
                    <td>Rp {{ number_format((float) $tender->tender_value, 0, ',', '.') }}</td>
                    <td>{{ $tender->tender_duration ? $tender->tender_duration . ' hari' : '-' }}</td>
                    <td>{{ $tender->is_selected ? 'Pemenang Final' : 'Pembanding' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="muted">Tidak ada data tender.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
