<?=$this->layout("main::layout_base", ["title" => "Veranstaltungen", "messages" => $messages ?? null, "path" => "../"])?>

<?=$this->start("header")?>
    <?=$this->insert("admin_inc::navbar", [
        "title" => "Veranstaltungen",
        "navbar_items" => [
            $this->fetch("main::navbar_links_li", ["nav_items" => [
                ["Admin hinzufügen", "add_admin.php", "person-plus"]
            ]])
        ]
    ])?>
<?=$this->end()?>

<div class="row gx-4">
    <div class="col-md-9">
        <div class="shadow p-3 bg-white">
            <h1>Veranstaltungen</h1>
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
            <hr>
            <?php if($events): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Veranstaltung</th>
                                <th scope="col">Optionen</th>
                                <th scope="col">Links</th>
                                <th scope="col">Plätze</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($events as $event): ?>
                                <tr>
                                    <td class="position-relative">
                                        <a class="stretched-link" href="event_details.php?event_id=<?=$this->e($event["id"])?>"><?=$this->e($event["titel"])?></a>
                                    </td>
                                    <td>
                                        <button
                                        class="btn btn-danger btn-sm delete"
                                        data-id="<?=$this->e($event["id"])?>"
                                        data-replacement="<?=$this->e($event["titel"])?>">
                                            <i class="bi bi-trash-fill"></i> Löschen
                                        </button>
                                    </td>
                                    <td>
                                        <a href="../?event=<?=$this->e($event["id"])?>" target="_blank" rel="noopener noreferrer">Anmeldelink</a>
                                    </td>
                                    <td class="position-relative">
                                        <?php if($event["max_participants"] > 0):?>
                                            <a class="stretched-link" href="results.php?event=<?=$this->e($event["id"])?>" title="Teilnehmerliste">
                                            </a>
                                            <div class="progress fw-bold" style="height: 30px; font-size: 1rem;">
                                                <div class="progress-bar bg-danger progress-bar-striped"
                                                role="progressbar"
                                                style="width: <?=$this->e($event["participants"])/$this->e($event["max_participants"])*100?>%">
                                                    <?=$this->e($event["participants"])?> Anmeldungen
                                                </div>
                                                <div class="progress-bar bg-success progress-bar-striped"
                                                role="progressbar"
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
        </div>
    </div>
    <div class="col-md-3 mt-md-0 mt-4">
        <div id="auto-update" class="shadow p-3 bg-white <?php if($new || $version === false): ?>important-danger<?php endif ?>">
            <?php if($version !== false): ?>
                <h1>Auto Update</h1>
                <p>
                    <?php if($new): ?>
                        Eine neue Version des Eventmanagers ist verfügbar.
                    <?php else: ?>
                        Sie haben bereits die neuste Version des Eventmanagers.
                    <?php endif ?>
                </p>
                <p>
                    Version <?=$this->e($version)?> <a href="https://github.com/Atlasfreak/Eventmanager/releases/tag/<?=$this->e($version)?>" target="_blank" rel="noopener noreferrer">Changelog</a>
                </p>
                <button class="btn btn-success" <?php if(!$new): ?>disabled<?php endif ?>>
                    <span class="spinner-border spinner-border-sm" style="display: none" role="status"></span>
                    <i class="bi bi-download"></i>
                    Installieren
                </button>
            <?php else: ?>
                <h3>Git ist nicht installiert!</h3>
            <?php endif ?>
        </div>
    </div>
</div>

<?=$this->start("scripts")?>
<script src="js/delete_modal.js"></script>
<script>
    delete_init("title", "delete_event.php", "event_id")
</script>
<script>
    let install_button = $("#auto-update button");
    if (!install_button.prop("disabled")) {
        install_button.click(
            function () {
                install_button.find(".spinner-border").show();
                install_button.find(".bi").hide();
                $.get("update.php?func=update", function () {
                    install_button.find(".spinner-border").hide();
                    install_button.find(".bi").show();
                    install_button.prop("disabled", true);
                    $("#auto-update").removeClass("important-danger");
                });
            }
        );
    }
</script>
<?=$this->end()?>
