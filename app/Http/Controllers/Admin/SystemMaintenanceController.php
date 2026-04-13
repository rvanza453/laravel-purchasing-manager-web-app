<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemMaintenanceController extends Controller
{
    private const FILE = 'modules_statuses.json';

    private const MODULE_KEY_MAP = [
        'sas' => 'ServiceAgreementSystem',
        'qc' => 'QcComplaintSystem',
        'ispo' => 'SystemISPO',
        'management' => 'management',
        'pr' => 'PrSystem',
        'systemsupport' => 'SystemSupport',
    ];

    public function index()
    {
        $status = self::getStatus();
        return view('admin.maintenance.index', compact('status'));
    }

    public function toggle(Request $request, $module)
    {
        $status = self::getStatus();
        $storageKey = self::storageKey($module);
        $status[$storageKey] = !($status[$storageKey] ?? true);
        self::persistStatus($status);
        return back()->with('success', 'Status eksekusi sistem berhasil diubah. Sistem yang mati tidak akan bisa diklik/diakses user di Module Hub.');
    }

    public static function getStatus()
    {
        $file = base_path(self::FILE);
        $status = is_file($file) ? json_decode(file_get_contents($file), true) : null;

        if (! is_array($status)) {
            $status = [];
        }

        $status = array_merge(self::defaultStatus(), $status);

        foreach (self::MODULE_KEY_MAP as $alias => $storageKey) {
            $status[$alias] = $status[$storageKey] ?? $status[$alias] ?? true;
        }

        return $status;
    }

    private static function defaultStatus()
    {
        return [
            'ServiceAgreementSystem' => true,
            'QcComplaintSystem' => true,
            'SystemISPO' => true,
            'management' => true,
            'PrSystem' => true,
            'SystemSupport' => true,
        ];
    }

    private static function storageKey(string $module): string
    {
        return self::MODULE_KEY_MAP[$module] ?? $module;
    }

    private static function persistStatus(array $status): void
    {
        $file = base_path(self::FILE);
        $payload = [];

        foreach (self::defaultStatus() as $key => $defaultValue) {
            $payload[$key] = (bool) ($status[$key] ?? $defaultValue);
        }

        file_put_contents(
            $file,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );
    }
}
