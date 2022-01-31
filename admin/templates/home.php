<?=$this->layout("admin::index", ["title" => "Veranstaltungen", "messages" => $messages ?? null])?>

<?=$this->start("description")?>
    <p>Hier ist eine Liste aller Veranstaltungen.</p>
    <p>Aktuell gibt es
        <?php if($events):?>
            <?php if(count($events) === 1):?>
                eine
            <?php elseif(count($events) === 0):?>
                keine
            <?php else:?>
                <?=$this->e(count($events))?>
            <?php endif?>
        <?php endif?>
        Veranstaltungen.
    </p>
<?=$this->end()?>

<?=$this->start("navbar_items")?>
    <?=$this->insert("main::navbar_links_li", ["nav_items" => array(
        ["Admin hinzufügen", "add_admin.php"]
    )])?>
<?=$this->end()?>

<?php if($events): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col">Optionen</th>
                    <th scope="col">Plätze</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($events as $event): ?>
                    <tr>
                        <td>
                            <a href="event_details.php?event_id=<?=$this->e($event["id"])?>"><?=$this->e($event["titel"])?></a>
                        </td>
                        <td>
                            <button
                            class="btn btn-danger btn-sm delete"
                            data-id="<?=$this->e($event["id"])?>"
                            data-replacement="<?=$this->e($event["titel"])?>"
                            data-toggle="modal"
                            data-target="#confirmDelete">
                                <i class="bi bi-trash-fill"></i> Löschen
                            </button>
                        </td>
                        <td>
                            <?php if($event["max_participants"] > 0):?>
                                <div class="progress font-weight-bold" style="height: 30px; font-size: 1rem;">
                                    <div class="progress-bar bg-danger progress-bar-striped"
                                    role="progressbar"
                                    title="<?=$this->e($event["participants"])?> Plätze belegt"
                                    style="width: <?=$this->e($event["participants"])/$this->e($event["max_participants"])*100?>%">
                                        <?=$this->e($event["participants"])?> Anmeldungen
                                    </div>
                                    <div class="progress-bar bg-success progress-bar-striped"
                                    role="progressbar"
                                    title="<?=$this->e($event["max_participants"])-$this->e($event["participants"])?> Plätze frei"
                                    style="width: <?=($this->e($event["max_participants"])-$this->e($event["participants"]))/$this->e($event["max_participants"])*100?>%">
                                        <?=$this->e($event["max_participants"])-$this->e($event["participants"])?> freie Plätze
                                    </div>
                                </div>
                            <?php else:?>
                                Keine Zeitfenster definiert.
                            <?php endif?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <?=$this->insert("admin::delete_modal", [
        "title" => "{title} löschen?",
        "content" => "Die Veranstaltung, \"{title}\", wirklich endgültig löschen?"
        ])
    ?>

<?php else:?>
    <hr>
    <div class="alert alert-info" role="alert">
        Aktuell gibt es keine Veranstaltungen.
    </div>
<?php endif?>

<div class="mt-3">
    <a class="btn btn-success" role="button" href="create_event.php">
        <i class="bi bi-calendar-plus" style="font-size: 1.1rem;"></i> Veranstaltung hinzufügen
    </a>
</div>

<?=$this->start("scripts")?>
<script src="js/delete_modal.js"></script>
<script>
    delete_init("title", "delete_event.php", "event_id")
</script>
<?=$this->end()?>
