<?php
$messages = array_filter(['success' => flash('success'), 'error' => flash('error')]);
?>
<?php if ($messages): ?>
    <div class="toast-stack" data-toast-stack>
        <?php foreach ($messages as $type => $message): ?>
            <div class="toast toast--<?= $type ?>" role="<?= $type === 'error' ? 'alert' : 'status' ?>" data-toast>
                <span class="toast__icon"><?= icon($type === 'error' ? 'close' : 'check', 16) ?></span>
                <p><?= e($message) ?></p>
                <button type="button" class="icon-btn icon-btn--sm" data-toast-close aria-label="Đóng"><?= icon('close', 16) ?></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
