<?php
// Info cards for the Mikrotik secret pages.
// Set $mkInfoCards = [[id, title, icon, color class], ...] before including;
// the page fills each value with $('#<id>').text(...)
?>
<div class="row gy-3 mb-24">
    <?php foreach ($mkInfoCards as [$cardId, $cardTitle, $cardIcon, $cardColor]): ?>
        <div class="col-xxl-3 col-sm-6">
            <div class="card px-16 py-12 shadow-none radius-8 border h-100">
                <div class="card-body p-0 d-flex align-items-center gap-12">
                    <span class="w-48-px h-48-px <?= $cardColor ?> flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8">
                        <iconify-icon icon="<?= $cardIcon ?>" class="text-2xl"></iconify-icon>
                    </span>
                    <div>
                        <p class="text-sm text-secondary-light fw-medium mb-1"><?= $cardTitle ?></p>
                        <h6 class="fw-semibold mb-0" id="<?= $cardId ?>">0</h6>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
