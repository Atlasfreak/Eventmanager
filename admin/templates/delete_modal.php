<div
class="modal fade"
id="confirmDeleteTemplate"
tabindex="-1"
data-link-id="#modalLink"
aria-labelledby="confirmDeleteLabel"
aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel"><?=$this->e($title)?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=$this->e($content)?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="modalLink"><i class="bi bi-trash-fill"></i> Endgültig löschen?</button>
            </div>
        </div>
    </div>
</div>
<div id="modalStore"></div>