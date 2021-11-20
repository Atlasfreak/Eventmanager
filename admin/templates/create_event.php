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
                    <div id="email_template_editor">
                        <?php if($email_template):?>
                            <?=parse_delta($email_template)?>
                        <?php endif?>
                    </div>
                    <div class="invalid-feedback">
                        Bestätigungs E-Mail Vorlage
                    </div>
                </div>
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
                <div class="form-row">
                    <div class="col-md-6" id="datetime-div">
                        <div class="form-group">
                            <label for="event_startdate">Veranstaltungsstart:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="event_startdate_icon"><i class="bi bi-calendar-week"></i></span>
                                </div>
                                <input
                                    class="form-control"
                                    type="datetime-local"
                                    id="event_startdate"
                                    name="event_startdate"
                                    aria-describedby="event_startdate_icon"
                                    <?php if($event_startdate_val): ?>value="<?=$this->e($event_startdate_val)?>"<?php endif?>
                                    data-input
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="datetime-div">
                        <div class="form-group">
                            <label for="event_enddate">Veranstaltungsende:</label>
                            <div class="input-group has-validation">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="event_enddate_icon"><i class="bi bi-calendar-week"></i></span>
                                </div>
                                <input
                                    class="form-control<?php if($event_date_err):?> is-invalid<?php endif ?>"
                                    type="datetime-local"
                                    id="event_enddate"
                                    name="event_enddate"
                                    aria-describedby="event_enddate_icon"
                                    <?php if($event_enddate_val): ?>value="<?=$this->e($event_enddate_val)?>"<?php endif?>
                                    data-input
                                >
                                <div class="invalid-feedback">
                                    Veranstaltungsende darf nicht vor Veranstaltungsstart liegen.
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
<script>
    function isQuillEmpty(quill) {
        if ((quill.getContents()['ops'] || []).length !== 1) { return false; }
        return quill.getText().trim().length === 0;
    }

    $(document).ready(function(){
        flatpickr("#datetime-div", {
            enableTime: true,
            altInput: true,
            time_24hr: true,
            wrap: true,
            locale: "de",
            minDate: "today",
            // defaultDate: new Date(),
            dateFormat: "Y-m-dTH:i",
            altFormat: "D j. F Y H:i",
        });
        let quill_settings = {
            theme: "snow",
            placeholder: "...",
            formats: [
                "background",
                "color",
                "bold",
                "italic",
                "link",
                "size",
                "strike",
                "underline",
                "blockquote",
                "header",
                "list",
                "align"
            ],
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ["bold", "italic", "underline", "strike"],
                    ["link", "blockquote"],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ["clean"],
                ]
            }
        };
        let description_editor = new Quill("#description_editor", quill_settings);
        let email_template_editor = new Quill("#email_template_editor", quill_settings);
        $("#create_event_form").submit(function(e){
            if (isQuillEmpty(description_editor) || isQuillEmpty(email_template_editor)) {
                description_editor_el = $("#description_editor");
                if (isQuillEmpty(description_editor) && !(description_editor_el.hasClass("is-invalid"))) {
                    description_editor_el.addClass("is-invalid");
                    description_editor_el.nextAll(".invalid-feedback").append(" darf nicht leer sein.");
                }

                email_template_editor_el = $("#email_template_editor");
                if (isQuillEmpty(email_template_editor) && !(email_template_editor_el.hasClass("is-invalid"))) {

                    email_template_editor_el.addClass("is-invalid");
                    email_template_editor_el.nextAll(".invalid-feedback").append(" darf nicht leer sein.");
                }
                e.preventDefault();
            }

            let email_input = $("#email_template");
            email_input.val(JSON.stringify(email_template_editor.getContents()));
            let description_input = $("#description");
            description_input.val(JSON.stringify(description_editor.getContents()));
        })
    });
</script>
<?=$this->end()?>