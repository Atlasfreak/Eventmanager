<div class="alert alert-<?=$this->e($type)?> alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center gap-2">
        <?php if(!empty($icon)): ?>
            <div class="d-flex align-items-center" style="height: 24px;">
                <i style="font-size: 1.5rem;" class="bi bi-<?=$this->e($icon)?> me-2"></i>
            </div>
            <div class="vr"></div>
        <?php endif ?>
        <div><?=$this->e($msg)?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>