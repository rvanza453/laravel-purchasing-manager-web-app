<x-agriflow::layouts.master>
    <div class="container py-4">
        <h1 class="mb-1">AgriFlow</h1>
        <p class="text-muted mb-4">Modul awal untuk alur operasional Panen, Pengiriman, dan Monitoring Restan.</p>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Panen</h5>
                        <p class="card-text">Pusat data dan proses hasil panen kebun.</p>
                        <a href="{{ route('agriflow.panen') }}" class="btn btn-primary">Buka Panen</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pengiriman</h5>
                        <p class="card-text">Pengelolaan proses kirim TBS dari kebun ke pabrik.</p>
                        <a href="{{ route('agriflow.pengiriman') }}" class="btn btn-primary">Buka Pengiriman</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Monitoring Restan</h5>
                        <p class="card-text">Pemantauan restan untuk tindak lanjut operasional.</p>
                        <a href="{{ route('agriflow.monitoring-restan') }}" class="btn btn-primary">Buka Monitoring</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-agriflow::layouts.master>
