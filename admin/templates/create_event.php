<?=$this->layout("admin::layout", ["title" => "Veranstaltung erstellen"])?>

<?=$this->start("styles")?>
<link rel="stylesheet" href="../css/flatpickr.min.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .form-control[readonly] {
        background-color: #ffffff;
    }
    .ql-toolbar {
        line-height: normal;
    }
    .ql-clear::after {
        content: "\f38f";
        display: inline-block;
        font-family: bootstrap-icons !important;
        font-style: normal;
        font-weight: normal !important;
        font-variant: normal;
        font-size: 1.1rem;
        text-transform: none;
        line-height: 1;
        vertical-align: -.125em;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .ql-clear:hover::after {
        content: "\f38e";
    }

    dt::after {
        content: ": ";
    }
</style>
<?=$this->end()?>

<form id="create_event_form" action="create_event.php" method="post">
    <div class="card mb-3">
        <fieldset>
            <legend class="card-header">
                Veranstaltung erstellen
            </legend>
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Titel:</label>
                    <input
                        class="form-control<?php if($title_err):?> is-invalid<?php endif ?>"
                        type="text"
                        name="title"
                        id="title"
                        placeholder="Schnuppertag"
                        <?php if($title_value):?>value="<?=$this->e($title_value)?>"<?php endif?>
                        required
                    >
                    <div class="invalid-feedback">
                        Titel ist nicht korrekt.
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Beschreibung:</label>
                    <input type="hidden" name="description" id="description">
                    <div id="description_editor">
                        <?php if($description):?>
                            <?=parse_delta($description)?>
                        <?php endif?>
                    </div>
                    <div class="invalid-feedback">
                        Beschreibung
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_template">Bestätigungs E-Mail Vorlage:</label>
                    <input type="hidden" name="email_template" id="email_template">
                    <div class="mb-3" id="email_template_editor">
                        <?php if($email_template):?>
                            <?=parse_delta($email_template)?>
                        <?php else: ?>
                            <p>Hallo ${Vorname} ${Nachname},</p>
                            <p>Sie haben erfolgreich einen Termin für ${Veranstaltung} gebucht.</p>
                            <p>Dieser ist am <b>${Tag}</b> im folgendem Zeitfraum: <b>${Zeitfenster}</b>.</p>
                            <p>Bitte kommen sie zu <b>Station ${Station}</b>.</p>
                            <p>Sie haben <b>${Anzahl} Teilnehmende</b> angemeldet.</p>
                            <p>Mit freundlichen Grüßen,</p>
                            <p>Die Schulleitung</p>
                        <?php endif?>
                    </div>
                    <div class="invalid-feedback">
                        Bestätigungs E-Mail Vorlage
                    </div>
                    <a
                    class="text-toggle"
                    href="#email_template_notes"
                    data-toggle="collapse"
                    aria-expanded="true"
                    aria-controls="email_template_notes"
                    >
                        Mögliche E-Mail Platzhalter
                        <span class="text-collapsed">ausklappen <i class="bi bi-chevron-down"></i></span>
                        <span class="text-expanded">einklappen <i class="bi bi-chevron-up"></i></span>
                    </a>
                    <div class="collapse show ml-4" id="email_template_notes">
                        <dl>
                            <dt>${Veranstaltung}</dt>
                                <dd>Titel der Veranstaltung</dd>
                            <dt>${Tag}</dt>
                                <dd>Datum für das man sich registriert hat</dd>
                            <dt>${Zeitfenster}</dt>
                                <dd>Uhrzeit für die man sich registriert hat</dd>
                            <dt>${Nachname}</dt>
                                <dd>angemeldeter Nachname</dd>
                            <dt>${Vorname}</dt>
                                <dd>angemeldeter Vorname</dd>
                            <dt>${Station}</dt>
                                <dd>Station an der man sich angemeldet hat</dd>
                            <dt>${Anzahl}</dt>
                                <dd>Anzahl an angemeldeten Teilnehmern</dd>
                        </dl>
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="col-md-6" id="datetime-div">
                        <div class="form-group">
                            <label for="reg_startdate">Anmeldestart:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="reg_startdate_icon"><i class="bi bi-calendar-week"></i></span>
                                </div>
                                <input
                                    class="form-control"
                                    type="datetime-local"
                                    id="reg_startdate"
                                    name="reg_startdate"
                                    aria-describedby="reg_startdate_icon"
                                    <?php if($reg_startdate_val): ?>value="<?=$this->e($reg_startdate_val)?>"<?php endif?>
                                    data-input
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="datetime-div">
                        <div class="form-group">
                            <label for="reg_enddate">Anmeldeende:</label>
                            <div class="input-group has-validation">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="reg_enddate_icon"><i class="bi bi-calendar-week"></i></span>
                                </div>
                                <input
                                    class="form-control<?php if($reg_date_err or $event_reg_date_err):?> is-invalid<?php endif ?>"
                                    type="datetime-local"
                                    id="reg_enddate"
                                    name="reg_enddate"
                                    aria-describedby="reg_enddate_icon"
                                    <?php if($reg_enddate_val): ?>value="<?=$this->e($reg_enddate_val)?>"<?php endif?>
                                    data-input
                                >
                                <div class="invalid-feedback">
                                    <?php if($reg_date_err):?>Anmeldeende darf nicht vor Anmeldestart liegen.<?php endif?>
                                    <?php if($event_reg_date_err):?>Anmeldeende darf nicht nach Veranstaltungsstart liegen.<?php endif?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="../js/flatpickr.js"></script>
<script src="../js/flatpickr.de.js"></script>
<script src="js/editors.js"></script>
<script src="js/quill_tooltips.js"></script>
<script>
    $(function(){
        flatpickr("#datetime-div", {
            enableTime: true,
            altInput: true,
            time_24hr: true,
            wrap: true,
            locale: "de",
            minDate: "today",
            dateFormat: "Y-m-dTH:i",
            altFormat: "D j. F Y H:i",
        });
        showTooltips();
    });
</script>
<?=$this->end()?>