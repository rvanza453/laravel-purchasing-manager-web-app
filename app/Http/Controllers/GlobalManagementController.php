<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class GlobalManagementController extends Controller
{
    public function index(): View
    {
        $items = [
            [
                'title' => 'Manajemen Pengguna',
                'description' => 'Kelola akun aplikasi serta assignment role per modul.',
                'route' => route('admin.users.index'),
                'icon' => 'fa-user-shield',
            ],
            [
                'title' => 'Master Site',
                'description' => 'Kelola daftar site yang dipakai lintas modul.',
                'route' => route('admin.sites.index'),
                'icon' => 'fa-map-location-dot',
            ],
            [
                'title' => 'Master Unit',
                'description' => 'Kelola unit organisasi (master department).',
                'route' => route('admin.master-departments.index'),
                'icon' => 'fa-building',
            ],
            [
                'title' => 'Master Department',
                'description' => 'Kelola department per site untuk operasional modul.',
                'route' => route('admin.departments.index'),
                'icon' => 'fa-diagram-project',
            ],
            [
                'title' => 'Master Sub Department',
                'description' => 'Kelola sub department (afdeling) per department.',
                'route' => route('admin.sub-departments.index'),
                'icon' => 'fa-sitemap',
            ],
            [
                'title' => 'Log Aktivitas',
                'description' => 'Pantau histori aktivitas perubahan data oleh pengguna.',
                'route' => route('admin.activity-logs.index'),
                'icon' => 'fa-clock-rotate-left',
            ],
            [
                'title' => 'Status Sistem (Maintenance)',
                'description' => 'Aktifkan atau matikan modul sistem (misal saat maintenance IT).',
                'route' => route('admin.maintenance.index'),
                'icon' => 'fa-power-off',
            ],
        ];

        return view('management.index', [
            'items' => $items,
        ]);
    }
}
