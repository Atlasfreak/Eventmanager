<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-auto">
                <form action="days.php?event_id=<?=$this->e($event_id)?>" method="POST" class="update"
                    autocomplete="off">
                    <?=$this->insert("admin_inc::day", [
                        "id" => $id,
                        "date" => $date,
                        "extra_field" => "update",
                    ])?>
                </form>
            </div>
            <div class="col-auto ms-auto">
                <form class="delete" action="days.php?event_id=<?=$this->e($event_id)?>" method="POST"
                    data-parent="days" autocomplete="off">
                    <input type="hidden" name="delete" value="true">
                    <input type="hidden" name="day_id" value="<?=$this->e($id)?>">
                    <button class="btn btn-danger" type="submit">
                        <i class="bi bi-trash-fill"></i> Löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="timewindows_<?=$this->e($id)?>">
            <?php if(!empty($timewindows)): ?>
                <?php foreach($timewindows as $timewindow): ?>
                    <?=$this->insert("admin_inc::timewindow_row", [
                        "timewindow_id" => $timewindow["id"],
                        "timewindow_from" => $timewindow["from"],
                        "timewindow_until" => $timewindow["until"],
                        "timewindow_max_participants" => $timewindow["max_participants"],
                        "day_id" => $id,
                    ])?>
                    <?php if($timewindow !== end($timewindows)): ?>
                        <hr class="mb-2 d-xl-none">
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        </div>
        <hr>
        <div id="empty_timewindow_<?=$this->e($id)?>" hidden>
            <?=$this->insert("admin_inc::timewindow_row", [
                "timewindow_id" => "{timewindow_id}",
                "timewindow_from" => "{timewindow_from}",
                "timewindow_until" => "{timewindow_until}",
                "timewindow_max_participants" => "{timewindow_max_participants}",
                "day_id" => $id,
                "extra_field" => "update",
            ])?>
        </div>
        <form action="timewindows.php?day_id=<?=$this->e($id)?>" id="timewindow_add_<?=$this->e($id)?>"
            class="row gx-2 add" data-empty="timewindow_<?=$this->e($id)?>"
            data-target="timewindows_<?=$this->e($id)?>" data-text="Zeitfenster">
            <div class="col-xl-10 mb-3">
                <?=$this->insert("admin_inc::timewindow", [
                    "id" => "add",
                    "extra_field" => "add",
                    "extra_class" => "add",
                    "day_id" => $id,
                ])?>
            </div>
            <div class="col-xl-auto ms-xl-auto px-xl-0">
                <button class="btn btn-primary" type="submit" title="Zeitfenster hinzufügen">
                    <i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Hinzufügen
                </button>
            </div>
        </form>
    </div>
</div>