<x-prsystem::app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <div class="bg-white text-gray-900 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Monitoring Budget (Tahun {{ $year }})</h3>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.budgets.monitoring') }}" class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                <select name="year" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                                    @for($y = date('Y'); $y >= 2024; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site</label>
                                <select name="site_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                                    <option value="">Semua Site</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ $site_id == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                                <select name="department_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                                    <option value="">Semua Departemen</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- View Toggle -->
                    <div class="flex justify-end mb-4" x-data="{ view: 'graph' }">
                        <div class="bg-gray-100 p-1 rounded-lg inline-flex">
                            <button @click="view = 'graph'; document.getElementById('chart-container').classList.remove('hidden'); document.getElementById('table-container').classList.add('hidden');" 
                                    :class="view === 'graph' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900'"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors">
                                Grafik
                            </button>
                            <button @click="view = 'table'; document.getElementById('chart-container').classList.add('hidden'); document.getElementById('table-container').classList.remove('hidden');"
                                    :class="view === 'table' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900'"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors">
                                Tabel
                            </button>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div id="chart-container" class="mb-8 p-4 border border-gray-200 rounded-xl">
                        <div id="budgetChart" style="min-height: 400px;"></div>
                        <div class="mt-4 text-center text-sm text-gray-500">
                            @if(!$site_id)
                                <span class="font-medium text-blue-600">Klik pada bar Site untuk melihat detail Departemen</span>
                            @elseif(!$department_id)
                                <span class="font-medium text-blue-600">Klik pada bar Departemen untuk melihat detail Budget</span>
                            @else
                                <span>Menampilkan detail Budget per Item</span>
                            @endif
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div id="table-container" class="hidden overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <!-- Table Content Same as Before -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Entity (Job / Station)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Budget</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Used</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">% Used</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($budgets as $budget)
                                    @php
                                        $limit = $budget->totalAmount();
                                        if ($limit > 0) {
                                            $percent = ($budget->used_amount / $limit) * 100;
                                        } else {
                                            $percent = $budget->used_amount > 0 ? 100 : 0;
                                        }

                                        $colorClass = 'bg-green-500';
                                        if ($percent > 80) $colorClass = 'bg-yellow-500';
                                        if ($percent >= 100) $colorClass = 'bg-red-500';
                                        
                                        $displayPercent = number_format($percent, 0) . '%';
                                        if ($limit == 0 && $budget->used_amount > 0) {
                                            $displayPercent = '>100%';
                                            $percent = 100; // Cap visual bar at 100
                                        }
                                        
                                        $name = '-';
                                        if ($budget->job) {
                                            $name = $budget->job->code . ' - ' . $budget->job->name;
                                            $dept = $budget->department->name ?? '-';
                                        } elseif ($budget->subDepartment) {
                                            $name = $budget->subDepartment->name;
                                            $dept = $budget->subDepartment->department->name ?? '-';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->category ?? 'Job Budget' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dept }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            <div>Rp {{ number_format($budget->amount, 0, ',', '.') }}</div>
                                            @if(($budget->pta_amount ?? 0) > 0)
                                                <div class="text-xs text-blue-600 font-medium">+ PTA: Rp {{ number_format($budget->pta_amount, 0, ',', '.') }}</div>
                                            @endif
                                            <div class="text-xs text-indigo-700 font-bold">Total: Rp {{ number_format($limit, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($budget->used_amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap align-middle">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="h-full {{ $colorClass }}" style="width: {{ min($percent, 100) }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-600 w-12 text-right">{{ $displayPercent }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button onclick="openBudgetDetail({{ $budget->id }})" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-xs font-bold">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                            Tidak ada data budget ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vanilla JS Modal (no Alpine dependency to avoid transition bugs) -->
        <div id="budget-modal-overlay" 
             onclick="closeBudgetModal()"
             style="display:none; position:fixed; inset:0; z-index:9000; background:rgba(107,114,128,0.75);"></div>
        <div id="budget-modal-panel"
             onclick="closeBudgetModal()"
             style="display:none; position:fixed; inset:0; z-index:9001; overflow-y:auto; padding:20px;">
            <div style="min-height:100%; display:flex; align-items:center; justify-content:center;">
                <div onclick="event.stopPropagation()" style="background:#fff; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; max-width:900px; overflow:hidden;">
                    <div style="padding:24px;">
                        <h3 style="font-size:18px; font-weight:700; color:#111827; margin:0 0 16px;">Detail Penggunaan Budget</h3>
                        <div id="budget-modal-body">
                            <!-- Content injected via JS -->
                        </div>
                    </div>
                    <div style="background:#f9fafb; padding:12px 24px; display:flex; justify-content:flex-end; border-top:1px solid #e5e7eb;">
                        <button onclick="closeBudgetModal()" 
                                style="padding:8px 20px; border:1px solid #d1d5db; border-radius:8px; background:#fff; font-size:14px; font-weight:600; color:#374151; cursor:pointer;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBudgetDetail(budgetId) {
            document.getElementById('budget-modal-overlay').style.display = 'block';
            document.getElementById('budget-modal-panel').style.display = 'block';
            document.body.style.overflow = 'hidden';

            const modalBody = document.getElementById('budget-modal-body');
            modalBody.innerHTML = '<div style="text-align:center;padding:32px;"><div style="width:32px;height:32px;border:3px solid #e5e7eb;border-top-color:#2563eb;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto;"></div></div>';

            fetch(`{{ url('admin/budgets') }}/${budgetId}/details`)
                .then(r => r.json())
                .then(d => {
                    if (d && d.html) {
                        modalBody.innerHTML = d.html;
                    } else {
                        modalBody.innerHTML = '<div style="color:red;padding:16px;text-align:center;">Gagal memuat data.</div>';
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    modalBody.innerHTML = '<div style="color:red;padding:16px;text-align:center;">Gagal memuat data.</div>';
                });
        }

        function closeBudgetModal() {
            document.getElementById('budget-modal-overlay').style.display = 'none';
            document.getElementById('budget-modal-panel').style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBudgetModal();
        });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <!-- ApexCharts Rule -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var chartData = @json($chartData);
            
            var options = {
                series: [{
                    name: 'Budget',
                    data: chartData.budget
                }, {
                    name: 'Used',
                    data: chartData.used
                }],
                chart: {
                    type: 'bar',
                    height: 450,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    },
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            var index = config.dataPointIndex;
                            var id = chartData.ids[index];
                            var level = chartData.level;
                            var url = new URL(window.location.href);
                            
                            if (id) {
                                if (level === 'site') {
                                    url.searchParams.set('site_id', id);
                                    window.location.href = url.toString();
                                } else if (level === 'department') {
                                    url.searchParams.set('department_id', id);
                                    window.location.href = url.toString();
                                }
                            }
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                        borderRadius: 6, // Rounded corners
                        borderRadiusApplication: 'end',
                        dataLabels: {
                            position: 'top',
                        },
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: chartData.labels,
                    labels: {
                        style: {
                            colors: '#64748B',
                            fontSize: '12px',
                            fontWeight: 600,
                        },
                        rotate: -45,
                        trim: true,
                        maxHeight: 120,
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748B',
                            fontSize: '12px',
                            fontWeight: 500,
                        },
                        formatter: function (value) {
                            // Shorten large numbers
                            if (value >= 1000000000) return (value / 1000000000).toFixed(1) + 'M';
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'jt';
                            return value;
                        }
                    }
                },
                grid: {
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4,
                    yaxis: {
                        lines: { show: true }
                    },
                    xaxis: {
                        lines: { show: false }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.3,
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.7,
                        stops: [0, 100]
                    }
                },
                colors: ['#3B82F6', '#EF4444'], // Premium Blue & Red
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                        }
                    },
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '13px'
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right', 
                    offsetY: -20,
                    markers: {
                        radius: 12,
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#budgetChart"), options);
            chart.render();
        });
    </script>
</x-prsystem::app-layout>
