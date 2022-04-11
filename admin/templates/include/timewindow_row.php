<div class="form-row">
    <form action="timewindows.php?day_id=<?=$this->e($day_id)?>" class="form-group update col-xl-10"
        autocomplete="off" method="POST">
        <?=$this->insert("admin_inc::timewindow", [
            "id" => $timewindow_id,
            "from" => $timewindow_from,
            "until" => $timewindow_until,
            "max_participants" => $timewindow_max_participants,
            "day_id" => $day_id,
            "extra_field" => "update",
        ])?>
    </form>
    <form action="timewindows.php?day_id=<?=$this->e($day_id)?>" class="delete col-xl-auto ml-xl-auto" method="post" data-parent="timewindows_<?=$this->e($day_id)?>">
        <input type="hidden" name="delete" value="true">
        <input type="hidden" name="timewindow_id" value="<?=$this->e($timewindow_id)?>">
        <button class="btn btn-danger" type="submit">
            <i class="bi bi-trash-fill"></i> Löschen
        </button>
    </form>
</div>