<?=$this->layout("admin::layout", ["title" => "Veranstaltung erstellen"])?>

<?=$this->start("styles")?>
    <?=$this->insert("admin_inc::event_form_css")?>
<?=$this->end()?>

<form id="event_form" action="create_event.php" method="post">
    <div class="card mb-3">
        <fieldset>
            <legend class="card-header">
                Veranstaltung erstellen
            </legend>
            <div class="card-body">
                <?=$this->insert("admin_inc::event_form", [
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
                    ])?>
            </div>
        </fieldset>
    </div>
    <!-- <h2>Veranstaltungstage</h2>
    <hr>
    <div class="mb-3" id="days">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <h3 class="col-md">Tag 1</h3>
                    <div class="row col-auto">
                        <label for="date_day_1" class="col-auto col-form-label">Datum</label>
                        <div class="col-auto">
                            <input type="date" class="form-control" id="date_day_1" name="date_day_1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <fieldset>
                    <legend><h4>Zeitfenster:</h4></legend>
                    <div class="row form-group">
                        <label for="timewindow_from_1" class="col-md-auto col-form-label pr-0">Von</label>
                        <div class="col-md-auto"><input type="time" class="form-control" id="timewindow_from_1" name="day_1_timewindow_from_1"></div>
                        <label for="timewindow_until_1"  class="col-md-auto col-form-label px-0">Bis</label>
                        <div class="col-md-auto"><input type="time" class="form-control" id="timewindow_until_1" name="day_1_timewindow_until_1"></div>
                        <label for="timewindow_max_participants_1"  class="col-md-auto col-form-label px-0">Max. Teilnehmer</label>
                        <div class="col-md"><input type="number" name="day_1_timewindow_max_participants_1" id="timewindow_max_participants_1" class="form-control" min="1"></div>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-primary"><i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Zeitfenster hinzufügen</button>
                </fieldset>
            </div>
        </div>
        <button type="button" class="btn btn-primary"><i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Tag hinzufügen</button>
    </div>
    <hr> -->
    <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg" style="font-size: 1.1rem;"></i> Veranstaltung Erstellen</button>
</form>

<?=$this->start("scripts")?>
    <?=$this->insert("admin_inc::event_form_scripts", ["minDate" => true])?>
<?=$this->end()?>