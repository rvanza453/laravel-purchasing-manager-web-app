<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Detail USPK'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $sasRole = strtolower(trim((string) auth()->user()?->moduleRole('sas')));
        $isLegalRole = $sasRole === 'legal' || auth()->user()?->hasAnyRole(['Legal', 'Super Admin']);
        $isSubmitter = (int) ($uspk->submitted_by ?? 0) === (int) auth()->id();
        $canDownloadFinalSpk = $uspk->hasFinalSpkDocument() && ($isSubmitter || $isLegalRole);
        $canProcessLegal = $isLegalRole && $uspk->status === \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_APPROVED && !$uspk->hasFinalSpkDocument();
    ?>

    <?php $__env->startPush('actions'); ?>
        <div style="display: flex; gap: 8px;">
            <?php if($uspk->isEditable()): ?>
                <a href="<?php echo e(route('sas.uspk.edit', $uspk)); ?>" class="btn btn-outline-primary btn-sm action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="<?php echo e(route('sas.uspk.submit', $uspk)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mensubmit USPK ini?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-success btn-sm action-btn shadow-sm">
                        <i class="fas fa-paper-plane"></i> Submit USPK
                    </button>
                </form>
            <?php endif; ?>
            <?php if($canProcessLegal): ?>
                <a href="<?php echo e(route('sas.uspk-legal.export', $uspk)); ?>" class="btn btn-primary btn-sm action-btn">
                    <i class="fas fa-file-export"></i> Export Draft SPK
                </a>
            <?php endif; ?>
            <?php if($canDownloadFinalSpk): ?>
                <a href="<?php echo e(route('sas.uspk-legal.download', $uspk)); ?>" class="btn btn-success btn-sm action-btn">
                    <i class="fas fa-file-download"></i> Download SPK Final
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-secondary btn-sm action-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    <?php $__env->stopPush(); ?>

    
    <div class="card mb-4 modern-card">
        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 24px;">
            <div>
                <div class="card-title text-primary" style="font-size: 20px; font-weight: 800; letter-spacing: -0.5px;"><?php echo e($uspk->uspk_number); ?></div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-user-circle"></i> <?php echo e($uspk->submitter->name ?? '-'); ?>

                    <span style="opacity: 0.5;">•</span>
                    <i class="far fa-clock"></i> <?php echo e($uspk->created_at->format('d M Y H:i')); ?>

                </div>
            </div>
            <span class="badge badge-<?php echo e($uspk->status); ?> status-badge">
                <?php echo e(ucfirst(str_replace('_', ' ', $uspk->status))); ?>

            </span>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div style="font-size: 22px; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);"><?php echo e($uspk->title); ?></div>

            <?php if($uspk->description): ?>
                <div class="desc-box">
                    <?php echo e($uspk->description); ?>

                </div>
            <?php endif; ?>

            <div class="info-grid mt-4">
                <div class="info-item">
                    <div class="info-label">Site / Department</div>
                    <div class="info-value"><?php echo e($uspk->department->name ?? '-'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Afdeling</div>
                    <div class="info-value"><?php echo e($uspk->subDepartment->name ?? '-'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Blok Area</div>
                    <div class="info-value">
                        <?php if($uspk->block_ids && count($uspk->block_ids) > 0): ?>
                            <?php
                                $blockNames = \Modules\ServiceAgreementSystem\Models\Block::whereIn('id', $uspk->block_ids)->pluck('name');
                            ?>
                            <div class="tags-container">
                                <?php $__currentLoopData = $blockNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blockName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="tag-badge"><?php echo e($blockName); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php elseif($uspk->block): ?>
                            <span class="tag-badge"><?php echo e($uspk->block->name); ?></span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Aktivitas</div>
                    <div class="info-value"><?php echo e($uspk->job->name ?? '-'); ?></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mb-4 modern-card">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="card-title" style="font-size: 16px; font-weight: 700;">
                <i class="fas fa-balance-scale" style="color: var(--warning); margin-right: 8px;"></i> Perbandingan & Voting Tender
            </div>
        </div>
        <div class="card-body" style="padding: 24px; background: rgba(0,0,0,0.01);">
            <?php if($uspk->tenders->count() > 0): ?>
                <div class="tender-scroll-container">
                    <?php $__currentLoopData = $uspk->tenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="tender-wrapper">
                            
                            <label class="tender-card" data-tender-card data-tender-id="<?php echo e($tender->id); ?>">
                                <input type="radio" name="selected_tender_id" value="<?php echo e($tender->id); ?>" class="tender-radio" style="position: absolute; opacity: 0; pointer-events: none;">
                                
                                <div class="tender-header">
                                    <div>
                                        <div class="tender-subtitle">
                                            <?php if($tender->is_selected): ?>
                                                <i class="fas fa-bookmark"></i> Rekomendasi Pengaju
                                            <?php else: ?>
                                                Kandidat
                                            <?php endif; ?>
                                        </div>
                                        <div class="tender-title"><?php echo e($tender->contractor->name ?? '-'); ?></div>
                                        <div class="tender-company"><?php echo e($tender->contractor->company_name ?? '-'); ?></div>
                                    </div>
                                    <div class="tender-radio-indicator">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>

                                <div class="price-duration-grid">
                                    <div class="pd-box price-box">
                                        <div class="pd-label">Harga Penawaran</div>
                                        <div class="pd-value">Rp <?php echo e(number_format($tender->tender_value, 0, ',', '.')); ?></div>
                                    </div>
                                    <div class="pd-box duration-box">
                                        <div class="pd-label">Estimasi Durasi</div>
                                        <div class="pd-value"><?php echo e($tender->tender_duration ? $tender->tender_duration . ' hari' : '-'); ?></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-label">Catatan Spesifikasi / Nego</div>
                                    <textarea class="form-control custom-textarea" rows="2" data-tender-description data-original-value="<?php echo e($tender->description); ?>" readonly><?php echo e($tender->description); ?></textarea>
                                </div>

                                <div class="edit-grid mb-3">
                                    <div>
                                        <label class="input-label">Edit Harga</label>
                                        <input type="number" class="form-control custom-input" data-tender-value data-original-value="<?php echo e($tender->tender_value); ?>" value="<?php echo e($tender->tender_value); ?>" step="0.01" min="0" disabled>
                                    </div>
                                    <div>
                                        <label class="input-label">Edit Durasi</label>
                                        <input type="number" class="form-control custom-input" data-tender-duration data-original-value="<?php echo e($tender->tender_duration); ?>" value="<?php echo e($tender->tender_duration); ?>" min="1" disabled>
                                    </div>
                                </div>

                                <div class="tender-footer">
                                    <div>
                                        <?php if($tender->attachment_path): ?>
                                            <a href="<?php echo e(asset('storage/' . $tender->attachment_path)); ?>" target="_blank" class="attachment-btn">
                                                <i class="fas fa-paperclip"></i> File Lampiran
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 11px; font-style: italic;">Tidak ada lampiran</span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-light btn-sm toggle-edit-btn" data-tender-edit-toggle>
                                        <i class="fas fa-pen"></i> Sesuaikan
                                    </button>
                                </div>
                            </label>

                            
                            <?php
                                $tenderVoters = $uspk->approvals->filter(function($app) use ($tender) {
                                    return $app->voteTender && $app->voteTender->id == $tender->id;
                                });
                            ?>
                            
                            <div class="voter-section">
                                <div class="voter-line"></div>
                                <div class="voter-title">VOTING APPROVER</div>
                                <div class="voters-container">
                                    <?php if($tenderVoters->count() > 0): ?>
                                        <?php $__currentLoopData = $tenderVoters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="voter-tooltip" 
                                                 data-name="<?php echo e($app->approver->name ?? 'Unknown'); ?>" 
                                                 data-level="<?php echo e($app->level); ?>" 
                                                 data-comment="<?php echo e($app->comment ?: 'Tanpa catatan'); ?>">
                                                <div class="voter-avatar">
                                                    <?php echo e(strtoupper(substr($app->approver->name ?? 'U', 0, 1))); ?>

                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span style="font-size: 11px; color: #a1a1aa; font-style: italic;">Belum ada vote</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open empty-icon"></i>
                    <p>Belum ada data tender yang dilampirkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($canProcessLegal || $uspk->hasFinalSpkDocument()): ?>
    <div class="card mb-4 modern-card">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="card-title" style="font-size: 16px; font-weight: 700;">
                <i class="fas fa-gavel" style="color: var(--accent); margin-right: 8px;"></i> Proses Legal SPK
            </div>
        </div>
        <div class="card-body" style="padding: 24px;">
            <?php if($uspk->hasFinalSpkDocument()): ?>
                <div class="alert-card mb-4">
                    <div class="card-body">
                        <i class="fas fa-check-circle info-icon" style="color: var(--success);"></i>
                        <div class="info-text">
                            Dokumen SPK final sudah diunggah oleh <strong><?php echo e($uspk->legalUploader->name ?? 'Legal'); ?></strong>
                            <?php if($uspk->legal_spk_uploaded_at): ?>
                                pada <strong><?php echo e($uspk->legal_spk_uploaded_at->format('d M Y H:i')); ?></strong>
                            <?php endif; ?>.
                            <?php if($uspk->legal_spk_notes): ?>
                                <div class="mt-2"><strong>Catatan Legal:</strong> <?php echo e($uspk->legal_spk_notes); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert-card mb-4">
                    <div class="card-body">
                        <i class="fas fa-info-circle info-icon"></i>
                        <div class="info-text">
                            USPK ini sudah approved final, namun dokumen SPK belum terbit. Export draft SPK, lakukan review/legal negotiation, lalu upload dokumen final yang sudah disepakati.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($canProcessLegal): ?>
            <div class="bottom-split-grid" style="grid-template-columns: 1.2fr 0.8fr; gap: 16px;">
                <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px;">
                    <div class="card-body" style="padding: 16px;">
                        <h4 style="margin: 0 0 12px; font-size: 14px; font-weight: 700;">Upload SPK Final Dari Legal</h4>
                        <form action="<?php echo e(route('sas.uspk-legal.upload', $uspk)); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="form-group mb-3">
                                <label class="input-label">File SPK Final (PDF/DOC/DOCX)</label>
                                <input type="file" name="spk_document" class="form-control" accept=".pdf,.doc,.docx" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="input-label">Catatan Legal (Opsional)</label>
                                <textarea name="legal_spk_notes" class="form-control custom-textarea" rows="3" placeholder="Catatan kesepakatan final dengan kontraktor..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success action-btn">
                                <i class="fas fa-upload"></i> Upload SPK Final
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card" style="border: 1px solid #fecaca; border-radius: 12px;">
                    <div class="card-body" style="padding: 16px;">
                        <h4 style="margin: 0 0 8px; font-size: 14px; font-weight: 700; color: #b91c1c;">Kembalikan ke Pemilihan</h4>
                        <p class="text-muted" style="font-size: 12px; margin-bottom: 12px;">Gunakan jika hasil nego/legal belum final dan perlu voting ulang approver final.</p>
                        <form action="<?php echo e(route('sas.uspk-legal.return', $uspk)); ?>" method="POST" onsubmit="return confirm('Kembalikan proses ke pemilihan kontraktor oleh approver final?')">
                            <?php echo csrf_field(); ?>
                            <div class="form-group mb-3">
                                <label class="input-label">Alasan Pengembalian</label>
                                <textarea name="comment" class="form-control custom-textarea" rows="3" placeholder="Tuliskan alasan wajib..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger action-btn">
                                <i class="fas fa-undo"></i> Kembalikan Proses
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
        $currentApproval = $uspk->approvals->first(function ($approval) {
            return in_array($approval->status, ['pending', 'on_hold'], true);
        });
        $currentStepAssignee = null;
        if ($currentApproval) {
            $step = optional($currentApproval->schema)->steps?->firstWhere('level', $currentApproval->level);
            $currentStepAssignee = $step?->user;
        }
        $currentApproverId = (int) ($currentStepAssignee->id ?? $currentApproval?->user_id ?? 0);
        $actionableApproval = $currentApproval && $currentApproverId === (int) auth()->id() ? $currentApproval : null;
        $maxApprovalLevel = $uspk->approvals->max('level');
        $isFinalApprovalLevel = $actionableApproval && (int) $actionableApproval->level === (int) $maxApprovalLevel;
    ?>

    
    <div class="bottom-split-grid mb-4">
        
        
        <div class="action-column">
            <?php if($uspk->approvals->count() > 0): ?>
                <?php if($actionableApproval): ?>
                <div class="card modern-card highlight-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="card-title mb-0" style="font-size: 18px; font-weight: 700;">
                                <i class="fas fa-gavel text-accent me-2"></i> Form Keputusan Anda
                            </div>
                            <?php if($isFinalApprovalLevel): ?>
                                <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">Level Final</span>
                            <?php else: ?>
                                <span class="badge badge-warning px-3 py-2" style="border-radius: 8px;">Level <?php echo e($actionableApproval->level); ?> Voting</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 20px;">
                            Silakan pilih kartu tender di atas yang menjadi rekomendasi Anda. Keputusan akhir mutlak berada pada approver level tertinggi.
                        </p>

                        <form id="approvalActionForm" action="<?php echo e(route('sas.uspk.approve', $uspk)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="selected_tender_id" id="selectedTenderId" value="">
                            <input type="hidden" name="vote_tender_value" id="voteTenderValue">
                            <input type="hidden" name="vote_tender_duration" id="voteTenderDuration">
                            <input type="hidden" name="vote_tender_description" id="voteTenderDescription">
                            
                            <div class="form-group mb-4">
                                <label class="input-label" style="font-size: 12px;">Alasan & Catatan Keputusan</label>
                                <textarea name="comment" id="approvalComment" class="form-control custom-textarea" rows="4" placeholder="Sebutkan alasan Anda memilih tender tersebut..."></textarea>
                            </div>

                            <div class="action-buttons-group">
                                <button type="submit" class="btn btn-success action-btn" id="approveBtn" formaction="<?php echo e(route('sas.uspk.approve', $uspk)); ?>">
                                    <i class="fas fa-check-circle"></i> <?php echo e($isFinalApprovalLevel ? 'Approve & Finalize' : 'Approve & Vote'); ?>

                                </button>
                                <button type="submit" class="btn btn-warning text-dark action-btn" id="holdBtn" formaction="<?php echo e(route('sas.uspk.hold', $uspk)); ?>">
                                    <i class="fas fa-pause-circle"></i> Hold Review
                                </button>
                                <button type="submit" class="btn btn-danger action-btn" id="rejectBtn" formaction="<?php echo e(route('sas.uspk.reject', $uspk)); ?>">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!$actionableApproval && $currentApproval): ?>
                <div class="card alert-card">
                    <div class="card-body">
                        <i class="fas fa-info-circle info-icon"></i>
                        <div class="info-text">
                            Tahap approval saat ini sedang diproses pada <strong>Level <?php echo e($currentApproval->level); ?></strong> oleh <strong><?php echo e($currentStepAssignee->name ?? $currentApproval->approver->name ?? 'Approver terkait'); ?></strong>.
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card alert-card">
                    <div class="card-body" style="justify-content: center; text-align: center; flex-direction: column;">
                        <i class="fas fa-file-signature info-icon" style="opacity: 0.5; margin-bottom: 8px;"></i>
                        <div class="info-text text-muted">
                            Proses approval akan dimulai setelah USPK disubmit.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="history-column">
            <div class="card modern-card" style="height: 100%;">
                <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div class="card-title" style="font-size: 16px; font-weight: 700;">
                        <i class="fas fa-history text-success" style="margin-right: 8px;"></i> Riwayat Jenjang Approval
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($uspk->approvals->count() > 0): ?>
                        <div class="modern-timeline">
                            <?php $__currentLoopData = $uspk->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item">
                                <div class="timeline-marker <?php echo e($approval->status); ?>"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="timeline-name"><?php echo e($approval->approver->name ?? 'Unknown'); ?></span>
                                            <span class="badge badge-<?php echo e($approval->status); ?> timeline-badge">
                                                <?php echo e(ucfirst($approval->status)); ?>

                                            </span>
                                        </div>
                                        <span class="timeline-date">
                                            <i class="far fa-clock"></i> <?php echo e($approval->approved_at ? $approval->approved_at->format('d M Y H:i') : 'Menunggu'); ?>

                                        </span>
                                    </div>
                                    <div class="timeline-role">Level <?php echo e($approval->level); ?> · <?php echo e($approval->approver->position ?? 'Approver'); ?></div>
                                    
                                    <?php if($approval->voteTender): ?>
                                        <div class="vote-summary-box mt-2">
                                            <div class="vote-title"><i class="fas fa-vote-yea text-primary me-1"></i> Memilih: <strong><?php echo e($approval->voteTender->contractor->name ?? '-'); ?></strong></div>
                                            <div class="vote-details">Nego: Rp <?php echo e(number_format((float) ($approval->vote_tender_value ?? $approval->voteTender->tender_value), 0, ',', '.')); ?> | <?php echo e($approval->vote_tender_duration ?? $approval->voteTender->tender_duration ?? '-'); ?> Hari</div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($approval->comment): ?>
                                        <div class="timeline-comment mt-2">
                                            <i class="fas fa-quote-left quote-icon"></i>
                                            <?php echo e($approval->comment); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="padding: 20px;">
                            <i class="fas fa-project-diagram empty-icon" style="font-size: 30px;"></i>
                            <p style="font-size: 13px;">Belum ada riwayat tercatat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <?php $__env->startPush('scripts'); ?>
    <style>
        /* === GLOBAL VARS & OVERRIDES === */
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --accent: #6366f1;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body { background-color: var(--bg-body); color: var(--text-secondary); }

        /* === MODERN CARD === */
        .modern-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            transition: var(--transition);
        }
        .highlight-card { border: 1px solid var(--accent); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1); }
        .desc-box { background: #f1f5f9; padding: 16px; border-radius: 12px; font-size: 14px; line-height: 1.6; color: var(--text-secondary); border-left: 4px solid var(--text-muted); }

        /* === INFO GRID === */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .info-item { background: #f8fafc; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border-color); }
        .info-label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }
        
        .tags-container { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag-badge { background: var(--primary-light); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .status-badge { padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* === SPLIT LAYOUT GRID === */
        .bottom-split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 992px) {
            .bottom-split-grid { grid-template-columns: 1fr; }
        }

        /* === TENDER COMPARISON === */
        .tender-scroll-container { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px; padding-top: 5px; scroll-snap-type: x proximity; }
        .tender-scroll-container::-webkit-scrollbar { height: 8px; }
        .tender-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .tender-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        .tender-wrapper { display: flex; flex-direction: column; min-width: 340px; max-width: 360px; scroll-snap-align: start; flex: 0 0 auto; }
        
        .tender-card {
            background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 16px; padding: 20px;
            cursor: pointer; transition: var(--transition); position: relative; display: flex; flex-direction: column;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); height: 100%;
        }
        .tender-card:hover { border-color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .tender-card--selected { border-color: var(--success) !important; background: rgba(16, 185, 129, 0.02); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important; }
        .tender-card--selected .tender-radio-indicator { background: var(--success); color: white; border-color: var(--success); }
        
        .tender-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; gap: 12px; }
        .tender-subtitle { font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; letter-spacing: 0.5px; }
        .tender-title { font-size: 18px; font-weight: 800; color: var(--text-primary); line-height: 1.2; }
        .tender-company { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        
        .tender-radio-indicator {
            width: 24px; height: 24px; border-radius: 50%; border: 2px solid var(--border-color); display: flex;
            align-items: center; justify-content: center; color: transparent; transition: var(--transition); flex-shrink: 0;
        }
        .tender-radio-indicator i { font-size: 12px; }

        .price-duration-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .pd-box { padding: 12px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); }
        .price-box { background: rgba(37,99,235,0.04); border-color: rgba(37,99,235,0.1); }
        .duration-box { background: rgba(16,185,129,0.04); border-color: rgba(16,185,129,0.1); }
        .pd-label { font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; }
        .pd-value { font-size: 14px; font-weight: 800; color: var(--text-primary); }

        .input-label { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
        .custom-textarea, .custom-input { font-size: 13px; border-radius: 8px; border: 1px solid var(--border-color); padding: 10px 12px; background: #f8fafc; transition: var(--transition); }
        .custom-textarea:focus, .custom-input:focus { background: white; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); outline: none; }
        .custom-textarea:disabled, .custom-input:disabled, .custom-textarea[readonly] { background: #f1f5f9; cursor: not-allowed; opacity: 0.8; }
        
        .edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .tender-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 16px; border-top: 1px dashed var(--border-color); }
        .attachment-btn { font-size: 12px; font-weight: 600; color: var(--primary); text-decoration: none; padding: 6px 10px; border-radius: 6px; background: var(--primary-light); transition: var(--transition); }
        .attachment-btn:hover { background: var(--primary); color: white; text-decoration: none; }
        
        .toggle-edit-btn { font-size: 12px; font-weight: 600; border-radius: 6px; }

        /* === VOTER SECTION (THE MAGIC SAUCE) === */
        .voter-section { position: relative; margin-top: -10px; display: flex; flex-direction: column; align-items: center; z-index: 5; }
        .voter-line { width: 2px; height: 20px; background: var(--border-color); margin-bottom: 4px; }
        .voter-title { font-size: 9px; font-weight: 800; color: var(--text-muted); letter-spacing: 1px; background: var(--bg-body); padding: 0 8px; margin-bottom: 8px; z-index: 2; }
        .voters-container { display: flex; flex-wrap: wrap; justify-content: center; gap: -8px; background: var(--bg-card); padding: 6px 12px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0,0,0,0.03); min-width: 80px; min-height: 40px; align-items: center;}
        
        .voter-tooltip { position: relative; cursor: pointer; margin-right: -8px; transition: transform 0.2s; }
        .voter-tooltip:hover { transform: translateY(-3px); z-index: 10; }
        .voter-tooltip:last-child { margin-right: 0; }
        
        .voter-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border: 2px solid var(--bg-card); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Tooltip CSS Magic */
        .voter-tooltip::before, .voter-tooltip::after { opacity: 0; visibility: hidden; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%) translateY(10px); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; z-index: 100; }
        .voter-tooltip::before { content: ''; border: 6px solid transparent; border-top-color: #1e293b; margin-bottom: -11px; }
        .voter-tooltip::after {
            content: attr(data-name) " (Level " attr(data-level) ")\A\A" attr(data-comment);
            background: #1e293b; color: #f8fafc; padding: 10px 14px; border-radius: 8px; font-size: 12px;
            white-space: pre-wrap; width: max-content; max-width: 240px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
            text-align: left; line-height: 1.4; margin-bottom: 1px; font-family: inherit; font-weight: 500;
        }
        .voter-tooltip:hover::before, .voter-tooltip:hover::after { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(-5px); }

        /* === ALERT / INFO CARD === */
        .alert-card { background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; height: 100%; }
        .alert-card .card-body { display: flex; align-items: center; gap: 16px; padding: 16px 20px; }
        .info-icon { font-size: 24px; color: #0284c7; }
        .info-text { font-size: 14px; color: #0c4a6e; line-height: 1.5; }

        /* === TIMELINE === */
        .modern-timeline { position: relative; padding-left: 24px; }
        .modern-timeline::before { content: ''; position: absolute; top: 0; bottom: 0; left: 6px; width: 2px; background: var(--border-color); border-radius: 2px; }
        .timeline-item { position: relative; margin-bottom: 24px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-marker { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--text-muted); border: 3px solid var(--bg-card); box-shadow: 0 0 0 2px var(--border-color); }
        .timeline-marker.approved { background: var(--success); box-shadow: 0 0 0 2px rgba(16,185,129,0.2); }
        .timeline-marker.rejected { background: var(--danger); box-shadow: 0 0 0 2px rgba(239,68,68,0.2); }
        .timeline-marker.pending { background: var(--warning); box-shadow: 0 0 0 2px rgba(245,158,11,0.2); }
        
        .timeline-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        .timeline-name { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .timeline-date { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .timeline-badge { font-size: 10px; padding: 4px 8px; border-radius: 6px; }
        .timeline-role { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
        
        .vote-summary-box { background: #f8fafc; border: 1px solid var(--border-color); padding: 10px 14px; border-radius: 8px; font-size: 13px; }
        .vote-title { color: var(--text-secondary); margin-bottom: 2px; }
        .vote-details { font-weight: 600; color: var(--text-primary); }
        
        .timeline-comment { background: rgba(0,0,0,0.03); padding: 12px 16px; border-radius: 0 12px 12px 12px; font-size: 13px; color: var(--text-secondary); position: relative; font-style: italic; border-left: 3px solid var(--accent); }
        .quote-icon { font-size: 10px; opacity: 0.3; margin-right: 6px; vertical-align: super; }

        /* BUTTONS */
        .action-btn { padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: var(--transition); }
        .action-btn:hover { transform: translateY(-1px); }
        .action-buttons-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
        .empty-icon { font-size: 40px; opacity: 0.3; margin-bottom: 16px; }
    </style>

    <script>
        function normalizeValue(value) { return (value ?? '').toString().trim(); }

        function cardHasNegotiationChanges(card) {
            if (!card) return false;
            const valueInput = card.querySelector('[data-tender-value]');
            const durationInput = card.querySelector('[data-tender-duration]');
            const descriptionInput = card.querySelector('[data-tender-description]');

            const valueChanged = normalizeValue(valueInput?.value) !== normalizeValue(valueInput?.dataset.originalValue);
            const durationChanged = normalizeValue(durationInput?.value) !== normalizeValue(durationInput?.dataset.originalValue);
            const descriptionChanged = normalizeValue(descriptionInput?.value) !== normalizeValue(descriptionInput?.dataset.originalValue);

            return valueChanged || durationChanged || descriptionChanged;
        }

        function setCardEditMode(card, editable) {
            const valueInput = card.querySelector('[data-tender-value]');
            const durationInput = card.querySelector('[data-tender-duration]');
            const descriptionInput = card.querySelector('[data-tender-description]');
            const toggleButton = card.querySelector('[data-tender-edit-toggle]');

            if (valueInput) valueInput.disabled = !editable;
            if (durationInput) durationInput.disabled = !editable;
            if (descriptionInput) descriptionInput.readOnly = !editable;

            if (toggleButton) {
                toggleButton.innerHTML = editable ? '<i class="fas fa-lock"></i> Kunci Edit' : '<i class="fas fa-pen"></i> Sesuaikan';
                toggleButton.classList.toggle('btn-warning', editable);
                toggleButton.classList.toggle('btn-light', !editable);
            }
        }

        function getSelectedTenderCard() {
            return document.querySelector('.tender-radio:checked')?.closest('[data-tender-card]') || null;
        }

        function syncTenderVoteFields() {
            const selectedCard = getSelectedTenderCard();
            const tenderValue = document.getElementById('voteTenderValue');
            const tenderDuration = document.getElementById('voteTenderDuration');
            const tenderDescription = document.getElementById('voteTenderDescription');
            const selectedTenderId = document.getElementById('selectedTenderId');

            if (!selectedCard) {
                if(tenderValue) tenderValue.value = '';
                if(tenderDuration) tenderDuration.value = '';
                if(tenderDescription) tenderDescription.value = '';
                if(selectedTenderId) selectedTenderId.value = '';
                return;
            }

            if(tenderValue) tenderValue.value = selectedCard.querySelector('[data-tender-value]')?.value || '';
            if(tenderDuration) tenderDuration.value = selectedCard.querySelector('[data-tender-duration]')?.value || '';
            if(tenderDescription) tenderDescription.value = selectedCard.querySelector('[data-tender-description]')?.value || '';
            if(selectedTenderId) selectedTenderId.value = selectedCard.querySelector('.tender-radio')?.value || '';
        }

        function validateApprovalAction(event) {
            const submitter = event.submitter;
            const decision = submitter ? submitter.id : '';
            const comment = document.getElementById('approvalComment')?.value.trim();
            const selectedTenderId = document.getElementById('selectedTenderId')?.value;

            if (decision === 'rejectBtn' && !comment) {
                event.preventDefault();
                alert('Komentar wajib diisi jika Anda ingin melakukan Reject.');
                return false;
            }

            if ((decision === 'approveBtn' || decision === 'holdBtn') && !selectedTenderId) {
                event.preventDefault();
                alert('Pilih salah satu kartu tender (klik kartunya) sebagai rekomendasi sebelum menyetujui.');
                return false;
            }

            if (decision === 'approveBtn' || decision === 'holdBtn') {
                const selectedCard = getSelectedTenderCard();
                if (cardHasNegotiationChanges(selectedCard)) {
                    if (!confirm('Anda melakukan perubahan angka nego/catatan pada tender ini. Perubahan akan disimpan. Lanjutkan?')) {
                        event.preventDefault(); return false;
                    }
                }
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const approvalForm = document.getElementById('approvalActionForm');
            const tenderCards = document.querySelectorAll('.tender-card');
            const tenderInputs = document.querySelectorAll('[data-tender-value], [data-tender-duration], [data-tender-description]');
            const editToggles = document.querySelectorAll('[data-tender-edit-toggle]');

            tenderCards.forEach(card => setCardEditMode(card, false));

            tenderCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('[data-tender-edit-toggle]') || e.target.closest('input:not([type="radio"])') || e.target.closest('textarea') || e.target.closest('a')) return;

                    const radio = this.querySelector('.tender-radio');
                    if (radio) radio.checked = true;
                    
                    tenderCards.forEach(c => c.classList.remove('tender-card--selected'));
                    this.classList.add('tender-card--selected');
                    syncTenderVoteFields();
                });
            });

            tenderInputs.forEach(input => {
                input.addEventListener('input', syncTenderVoteFields);
                input.addEventListener('change', syncTenderVoteFields);
            });

            editToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const card = this.closest('[data-tender-card]');
                    if (!card) return;
                    
                    const isEditable = this.classList.contains('btn-warning');
                    setCardEditMode(card, !isEditable);
                    if (!isEditable) card.querySelector('[data-tender-value]')?.focus();
                });
            });

            // Set default selected jika ada
            const defaultSelected = document.querySelector('.tender-radio:checked');
            if (!defaultSelected && tenderCards.length > 0) {
                tenderCards[0].querySelector('.tender-radio').checked = true;
                tenderCards[0].classList.add('tender-card--selected');
            } else if (defaultSelected) {
                defaultSelected.closest('.tender-card').classList.add('tender-card--selected');
            }
            syncTenderVoteFields();

            if (approvalForm) approvalForm.addEventListener('submit', validateApprovalAction);
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $attributes = $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $component = $__componentOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk/show.blade.php ENDPATH**/ ?>