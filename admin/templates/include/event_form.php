<div class="form-group">
    <label for="title">Titel</label>
    <input
        class="form-control<?php if(!empty($title_err)):?> is-invalid<?php endif ?>"
        type="text"
        name="title"
        id="title"
        placeholder="Schnuppertag"
        <?php if(!empty($title_value)):?>value="<?=$this->e($title_value)?>"<?php endif?>
        required
    >
    <div class="invalid-feedback">
        Titel ist nicht korrekt.
    </div>
</div>
<hr>
<div class="form-group">
    <label for="description">Beschreibung</label>
    <input type="hidden" name="description" id="description">
    <div id="description_editor">
        <?php if(!empty($description)):?>
            <?=parse_delta($description)?>
        <?php endif?>
    </div>
    <div class="invalid-feedback">
        Beschreibung
    </div>
</div>
<div class="form-group">
    <label for="email_template">Bestätigungs E-Mail Vorlage</label>
    <input type="hidden" name="email_template" id="email_template">
    <div class="mb-3" id="email_template_editor">
        <?php if(!empty($email_template)):?>
            <?=parse_delta($email_template)?>
        <?php else: ?>
            <p>Hallo ${Vorname} ${Nachname},</p>
            <p>Sie haben erfolgreich einen Termin für ${Veranstaltung} gebucht.</p>
            <p>Dieser ist am <b>${Tag}</b> im folgendem Zeitraum: <b>${Zeitfenster}</b>.</p>
            <p>Bitte kommen Sie zu <b>Station ${Station}</b>.</p>
            <p>Sie haben <b>${Anzahl} Teilnehmende</b> angemeldet.</p>
            <p>Mit freundlichen Grüßen</p>
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
            <dt class="user-select-all">${Veranstaltung}</dt>
                <dd>Titel der Veranstaltung</dd>
            <dt class="user-select-all">${Tag}</dt>
                <dd>Datum für das man sich registriert hat</dd>
            <dt class="user-select-all">${Zeitfenster}</dt>
                <dd>Uhrzeit für die man sich registriert hat</dd>
            <dt class="user-select-all">${Nachname}</dt>
                <dd>angemeldeter Nachname</dd>
            <dt class="user-select-all">${Vorname}</dt>
                <dd>angemeldeter Vorname</dd>
            <dt class="user-select-all">${Station}</dt>
                <dd>Station an der man sich angemeldet hat</dd>
            <dt class="user-select-all">${Anzahl}</dt>
                <dd>Anzahl an angemeldeten Teilnehmern</dd>
        </dl>
    </div>
</div>
<hr>
<div class="form-group">
    <label for="station">Stationen (optional)</label>
    <input class="form-control" type="number" name="stations" id="stations" min="0" value="<?=$this->e($stations_val)?>">
</div>
<hr>
<div class="form-row">
    <div class="col-md-6" id="datetime-div">
        <div class="form-group">
            <label for="reg_startdate">Anmeldestart</label>
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
                    <?php if(!empty($reg_startdate_val)): ?>value="<?=$this->e($reg_startdate_val)?>"<?php endif?>
                    data-input
                >
            </div>
        </div>
    </div>
    <div class="col-md-6" id="datetime-div">
        <div class="form-group">
            <label for="reg_enddate">Anmeldeende</label>
            <div class="input-group has-validation">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="reg_enddate_icon"><i class="bi bi-calendar-week"></i></span>
                </div>
                <input
                    class="form-control<?php if(!empty($reg_date_err) or !empty($event_reg_date_err)):?> is-invalid<?php endif ?>"
                    type="datetime-local"
                    id="reg_enddate"
                    name="reg_enddate"
                    aria-describedby="reg_enddate_icon"
                    <?php if(!empty($reg_enddate_val)): ?>value="<?=$this->e($reg_enddate_val)?>"<?php endif?>
                    data-input
                >
                <div class="invalid-feedback">
                    <?php if(!empty($reg_date_err)):?>Anmeldeende darf nicht vor Anmeldestart liegen.<?php endif?>
                </div>
            </div>
        </div>
    </div>
</div>