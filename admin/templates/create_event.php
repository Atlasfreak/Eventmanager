<?= $this->layout("admin::layout", ["title" => "Veranstaltung erstellen"]) ?>

<?= $this->start("styles") ?>
    <?= $this->insert("admin_inc::event_form_css") ?>
<?= $this->end() ?>

<form id="event_form" action="create_event.php" method="post">
    <?= $this->insert("main::csrf_token", ["form_name" => "create_event"]) ?>
    <div class="card mb-3">
        <fieldset>
            <legend class="card-header">
                Veranstaltung erstellen
            </legend>
            <div class="card-body">
                <?= $this->insert("admin_inc::event_form", [
                    "title_err" => $title_err,
                    "description_err" => $description_err,
                    "email_template_err" => $email_template_err,
                    "reg_date_err" => $reg_date_err,
                    "title_value" => $title_value,
                    "description" => $description,
                    "email_template" => $email_template,
                    "stations_val" => $stations_val ?? null,
                    "reg_startdate_val" => $reg_startdate_val,
                    "reg_enddate_val" => $reg_enddate_val,
                ]) ?>
            </div>
        </fieldset>
    </div>
    <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Veranstaltung Erstellen</button>
</form>

<?= $this->start("scripts") ?>
    <?= $this->insert("admin_inc::event_form_scripts", ["minDate" => true]) ?>
<?= $this->end() ?>