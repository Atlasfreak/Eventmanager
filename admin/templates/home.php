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
                            <a href="results.php?event=<?=$this->e($event["id"])?>"><?=$this->e($event["titel"])?></a>
                        </td>
                        <td>
                            <button
                            class="btn btn-danger btn-sm delete"
                            data-event-id="<?=$this->e($event["id"])?>"
                            data-event-title="<?=$this->e($event["titel"])?>"
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
                    <h5 class="modal-title" id="confirmDeleteLabel">{title} löschen?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Die Veranstaltung, "{title}", wirklich endgültig löschen?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="modalLink"><i class="bi bi-trash-fill"></i> Endgültig löschen?</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modalStore"></div>
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
<script>
    $("button.delete").click(function() {
        let event_id = $(this).data("event-id");
        let title = $(this).data("event-title");
        let modal_store = $("div#modalStore");

        modal_store.html($("#confirmDeleteTemplate").clone().attr("id", "confirmDelete"));
        let modal = $("#confirmDelete");
        let modal_btn_selector = "#confirmDelete .modal-footer > button";

        $(modal_btn_selector).data("event-id", event_id);

        modal.html(modal.html().replace(/{title}/gm, title));
        $(modal_btn_selector).click(function() {
            $.get("delete_event.php", {"event_id": event_id}).done(function(data) {
                // location.reload();
            });
        });
    });
</script>
<?=$this->end()?>
