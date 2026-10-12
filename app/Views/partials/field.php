<?php
/**
 * Ô nhập dùng chung. Biến vào: $f = ['name', 'label', 'type' => 'text', 'value' => '', 'attrs' => '', 'hint' => '', 'full' => false]
 * Lỗi từ server (error()) được hiển thị sẵn; JS kiểm tra inline thêm khi rời ô.
 */
$f += ['type' => 'text', 'value' => '', 'attrs' => '', 'hint' => '', 'full' => false];
$fieldId = 'f-' . $f['name'];
$fieldError = error($f['name']);
?>
<div class="field <?= $f['full'] ? 'field--full' : '' ?> <?= $fieldError ? 'has-error' : '' ?>">
    <label for="<?= $fieldId ?>"><?= e($f['label']) ?></label>
    <?php if ($f['type'] === 'textarea'): ?>
        <textarea id="<?= $fieldId ?>" name="<?= $f['name'] ?>" <?= $f['attrs'] ?>><?= e(old($f['name'], $f['value'])) ?></textarea>
    <?php else: ?>
        <input type="<?= $f['type'] ?>" id="<?= $fieldId ?>" name="<?= $f['name'] ?>"
               value="<?= $f['type'] === 'password' ? '' : e(old($f['name'], $f['value'])) ?>"
               <?= $f['attrs'] ?> <?= $fieldError ? 'aria-invalid="true" aria-describedby="' . $fieldId . '-error"' : '' ?>>
    <?php endif; ?>
    <?php if ($f['hint']): ?><p class="hint"><?= e($f['hint']) ?></p><?php endif; ?>
    <?php if ($fieldError): ?><p class="field-error" id="<?= $fieldId ?>-error"><?= e($fieldError) ?></p><?php endif; ?>
</div>
