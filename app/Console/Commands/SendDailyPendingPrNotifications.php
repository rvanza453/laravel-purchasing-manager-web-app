<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrApproval;
use App\Services\FonnteService;
use App\Enums\PrStatus;
use Illuminate\Support\Facades\Log;

class SendDailyPendingPrNotifications extends Command
{
    protected $signature = 'pr:notify-pending';
    protected $description = 'Send daily WhatsApp summary of pending PR approvals (lower level first, staggered delay)';

    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        parent::__construct();
        $this->fonnteService = $fonnteService;
    }

    public function handle()
    {
        $this->info('Starting Daily PR Notification Job...');

        // 1. Get all pending approvals ensuring PR is active
        $approvals = PrApproval::whereIn('status', [PrStatus::PENDING->value, PrStatus::ON_HOLD->value])
            ->whereHas('purchaseRequest', function ($q) {
                $q->whereNotIn('status', [PrStatus::REJECTED->value, PrStatus::APPROVED->value]);
            })
            ->with(['purchaseRequest', 'approver'])
            ->orderBy('level', 'asc') // Lower level approvers notified first
            ->get();

        // 2. Build per-approver notification map, preserving level order
        $notifications = [];

        foreach ($approvals as $approval) {
            $pr = $approval->purchaseRequest;

            // Only notify if it is truly this approver's turn
            $pendingLowerLevels = $pr->approvals()
                ->where('level', '<', $approval->level)
                ->where('status', '!=', PrStatus::APPROVED->value)
                ->exists();

            if ($pendingLowerLevels) continue;

            $approverId = $approval->approver_id;

            if (!isset($notifications[$approverId])) {
                $notifications[$approverId] = [
                    'user'  => $approval->approver,
                    'level' => $approval->level,
                    'prs'   => [],
                ];
            }

            $notifications[$approverId]['prs'][] = $pr;
        }

        // 3. Sort by level ascending so lowest-level approver is contacted first
        uasort($notifications, fn($a, $b) => $a['level'] <=> $b['level']);

        // 4. Send with staggered delay (10 minutes between each send to avoid WA spam ban)
        $count = 0;
        $isFirst = true;

        foreach ($notifications as $approverId => $data) {
            $user = $data['user'];
            $prs  = $data['prs'];

            if (!$user || !$user->phone_number) {
                $this->warn("Skipping User ID {$approverId} (No Phone Number)");
                continue;
            }

            $total  = count($prs);
            $prList = '';
            $tk     = 0;
            foreach ($prs as $pr) {
                if ($tk < 5) {
                    $prList .= '- ' . $pr->pr_number . ' (Rp ' . number_format($pr->total_estimated_cost, 0, ',', '.') . ")\n";
                }
                $tk++;
            }
            if ($total > 5) {
                $prList .= '- ... dan ' . ($total - 5) . " lainnya.\n";
            }

            $message = $this->buildMessage($user->name, $total, $prList);

            try {
                // Wait 10 seconds between sends to avoid WA spam
                if (!$isFirst) {
                    $this->info("Waiting 10 seconds before next send...");
                    sleep(20);
                }

                $this->fonnteService->sendMessage($user->phone_number, $message);
                $this->info("Sent notification to {$user->name} (Level {$data['level']}, {$total} PRs)");
                $count++;
                $isFirst = false;
            } catch (\Exception $e) {
                $this->error("Failed to send to {$user->name}: " . $e->getMessage());
                Log::error('Fonnte Daily Job Error: ' . $e->getMessage());
                $isFirst = false;
            }
        }

        $this->info("Job Finished. Queued {$count} notifications.");
    }

    /**
     * Build one of 6 varied message templates, chosen by day-of-week
     * to ensure different wording each day without being fully random.
     */
    private function buildMessage(string $name, int $total, string $prList): string
    {
        $callToAction = "Mohon jawab pesan ini apabila telah membaca atau telah melakukan approval.";
        $prWord       = $total > 1 ? "{$total} PR" : "1 PR";
        $url          = 'https://pr-system.oilpam.my.id/';

        $variants = [
            // Variant 0
            "Halo {$name},\n\n" .
            "🔔 *Pengingat Harian — Approval PR*\n" .
            "Anda memiliki *{$prWord}* yang menunggu persetujuan:\n\n" .
            $prList . "\n" .
            "Silakan login dan proses di: {$url}\n\n" .
            $callToAction,

            // Variant 1
            "Selamat pagi, {$name}!\n\n" .
            "📋 Ada *{$prWord}* yang memerlukan persetujuan Anda hari ini:\n\n" .
            $prList . "\n" .
            "Mohon segera diproses melalui sistem: {$url}\n\n" .
            $callToAction,

            // Variant 2
            "Halo {$name},\n\n" .
            "⏳ Kami mengingatkan bahwa *{$prWord}* berikut masih menunggu tindakan Anda:\n\n" .
            $prList . "\n" .
            "Akses sistem PR di: {$url}\n\n" .
            $callToAction,

            // Variant 3
            "Yth. {$name},\n\n" .
            "📌 Terdapat *{$prWord}* yang perlu segera Anda tinjau dan setujui:\n\n" .
            $prList . "\n" .
            "Silakan login: {$url}\n\n" .
            $callToAction,

            // Variant 4
            "Halo {$name},\n\n" .
            "🗒 *Reminder: {$prWord} Pending Approval*\n" .
            "Berikut daftar PR yang menunggu keputusan Anda:\n\n" .
            $prList . "\n" .
            "Proses di sistem kami: {$url}\n\n" .
            $callToAction,

            // Variant 5
            "Selamat bekerja, {$name}!\n\n" .
            "🔔 Jangan lupa, ada *{$prWord}* yang masih menunggu approval Anda:\n\n" .
            $prList . "\n" .
            "Login dan tinjau melalui: {$url}\n\n" .
            $callToAction,
        ];

        // Pick variant based on day of year (rotates daily, predictably)
        $index = (int) date('z') % count($variants);
        return $variants[$index];
    }
}
