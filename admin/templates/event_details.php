<?=$this->layout("admin::layout", ["title" => $title_value])?>

<?=$this->start("styles")?>
    <?=$this->insert("admin_inc::event_form_css")?>
    <?=$this->insert("admin_inc::mails_css")?>
<?=$this->end()?>

<h1 class="mb-4"><?=$this->e($title_value)?> Übersicht</h1>
<div class="row">
    <div class="col-md-3">
        <div class="nav flex-column nav-pills" id="id" role="tablist" aria-orientation="vertical">
            <a
            class="nav-link <?php if(empty($emails_selected)): ?>active<?php endif ?>"
            id="edit-tab"
            data-toggle="pill"
            href="#edit"
            role="tab"
            aria-controls="edit"
            aria-selected="true">
                <i class="bi bi-pencil-square"></i> Veranstaltung bearbeiten
            </a>
            <a
            class="nav-link <?php if(!empty($emails_selected)): ?>active<?php endif ?>"
            id="e-mails-tab"
            data-toggle="pill"
            href="#e-mails"
            role="tab"
            aria-controls="e-mails"
            aria-selected="false">
                <i class="bi bi-envelope-fill"></i> E-Mails
            </a>
        </div>
        <div class="nav flex-column nav-pills">
            <a class="nav-link" href="results.php?event=<?=$this->e($id)?>">
                <i class="bi bi-table"></i> Teilnehmerübersicht
            </a>
        </div>
        <hr class="d-md-none">
    </div>
    <div class="col-md-9">
        <div class="tab-content" id="tabContent">
            <div class="tab-pane fade <?php if(empty($emails_selected)): ?>show active<?php endif ?>" id="edit"
                role="tabpanel" aria-labelledby="edit-tab">
                <form id="event_form" action="event_details.php?event_id=<?=$this->e($id)?>" method="post">
                    <?=$this->insert("admin_inc::event_form", [
                        "title_err" => $errors["title"] ?? false,
                        "description_err" => $errors["description"] ?? false,
                        "email_template_err" => $errors["email_template"] ?? false,
                        "reg_date_err" => $errors["reg_date"] ?? false,
                        "title_value" => $title_value,
                        "description" => $description,
                        "email_template" => $email_template,
                        "stations_val" => $stations_val,
                        "reg_startdate_val" => $reg_startdate_val,
                        "reg_enddate_val" => $reg_enddate_val,
                        ])?>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-pencil-square"></i> Veranstaltung speichern
                    </button>
                </form>
                <hr>
                <div id="days_timewindows">
                    <a href="#days"></a>
                    <h3 class="mb-3">Tage</h3>
                    <div id="days">
                        <?php foreach($days as $day): ?>
                        <?=$this->insert("admin_inc::day_card", [
                                "id" => $day["id"],
                                "date" => $day["date"],
                                "timewindows" => $day["timewindows"] ?? null,
                                "extra_field" => "update",
                                "event_id" => $id,
                            ])?>
                        <?php endforeach ?>
                    </div>
                    <hr>
                    <form class="row needs-validation add align-items-center" id="add_day" action="days.php?event_id=<?=$this->e($id)?>"
                        method="post" data-empty="day" data-parent=".card-header" data-target="days">
                        <div class="col-auto pr-0">
                            <?=$this->insert("admin_inc::day", [
                                "id" => "add",
                                "extra_field" => "add"
                            ])?>
                        </div>
                        <div class="col-auto pl-0 ml-auto">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Tag hinzufügen
                            </button>
                        </div>
                    </form>
                    <div id="empty_day" hidden="hidden">
                        <?=$this->insert("admin_inc::day_card", [
                            "id" => "{day_id}",
                            "date" => "{day_date}",
                            "extra_field" => "update",
                            "event_id" => $id,
                        ])?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?php if(!empty($emails_selected)): ?>show active<?php endif ?>" id="e-mails"
                role="tabpanel" aria-labelledby="e-mails-tab">
                <?=$this->insert("admin_inc::mails", [
                    "data_participants" => $data_participants,
                    "emails_selected" => $emails_selected,
                    "id" => $id,
                    "errors" => $errors ?? array(),
                ])?>
            </div>
        </div>
    </div>
</div>

<?=$this->start("scripts")?>
    <?=$this->insert("admin_inc::event_form_scripts")?>
    <?=$this->insert("admin_inc::mails_scripts")?>
    <script src="js/day_timewindow.js"></script>
<?=$this->end()?>