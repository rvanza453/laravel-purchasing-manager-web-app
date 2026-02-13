<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrApproval;
use App\Models\User;
use App\Services\FonnteService;
use App\Enums\PrStatus;
use Illuminate\Support\Facades\Log;

class SendDailyPendingPrNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pr:notify-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily WhatsApp summary of pending PR approvals to relevant approvers';

    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        parent::__construct();
        $this->fonnteService = $fonnteService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily PR Notification Job...');
        
        // 1. Get all pending approvals ensuring PR is active
        $approvals = PrApproval::where('status', PrStatus::PENDING->value)
            ->whereHas('purchaseRequest', function($q) {
                // Ensure PR itself is not rejected or cancelled
                $q->whereNotIn('status', [PrStatus::REJECTED->value, PrStatus::APPROVED->value]);
            })
            ->with(['purchaseRequest', 'approver'])
            ->get();

        $notifications = [];

        foreach ($approvals as $approval) {
            // 2. Check if it is THIS approval's turn
            // Logic: All lower levels for this PR must be APPROVED
            $pr = $approval->purchaseRequest;
            
            $pendingLowerLevels = $pr->approvals()
                ->where('level', '<', $approval->level)
                ->where('status', '!=', PrStatus::APPROVED->value)
                ->exists();

            if (!$pendingLowerLevels) {
                // It is this approver's turn
                $approverId = $approval->approver_id;
                
                if (!isset($notifications[$approverId])) {
                    $notifications[$approverId] = [
                        'user' => $approval->approver,
                        'prs' => []
                    ];
                }

                $notifications[$approverId]['prs'][] = $pr;
            }
        }

        // 3. Send Notifications
        $count = 0;
        foreach ($notifications as $approverId => $data) {
            $user = $data['user'];
            $prs = $data['prs'];
            
            if (!$user || !$user->phone_number) {
                $this->warn("Skipping User ID {$approverId} (No Phone Number)");
                continue;
            }

            $total = count($prs);
            $prList = "";
            $tk = 0;
            foreach ($prs as $pr) {
                if ($tk < 5) { // Limit detailed list to 5
                    $prList .= "- {$pr->pr_number} (Rp " . number_format($pr->total_estimated_cost, 0, ',', '.') . ")\n";
                }
                $tk++;
            }
            if ($total > 5) {
                $prList .= "- ... dan " . ($total - 5) . " lainnya.\n";
            }

            $message = "Halo {$user->name},\n\n" .
                       "🔔 *Reminder Harian (10:00 WIB)*\n" .
                       "Anda memiliki *{$total} PR* yang menunggu persetujuan Anda saat ini:\n\n" .
                       $prList . "\n" .
                       "Mohon segera login untuk memproses https://pr-system.oilpam.my.id/.\n" .
                       "Terima kasih.";

            try {
                $this->fonnteService->sendMessage($user->phone_number, $message);
                $this->info("Sent notification to {$user->name} ({$total} PRs)");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send to {$user->name}: " . $e->getMessage());
                Log::error("Fonnte Daily Job Error: " . $e->getMessage());
            }
        }

        $this->info("Job Finished. Sent {$count} notifications.");
    }
}
