<?=$this->layout("main::layout", ["title" => $title."-Anmeldung", "path" => ""])?>
<?php if($errors): ?>
    <?=$this->insert("main::alert", ["type" => "danger", "msg" => "Es gab Probleme mit ihrer Einsendung."])?>
<?php endif ?>
<div class="text-center">
    <h1><?=$this->e($title)?></h1>
</div>
<p>
    <a
    class="text-toggle"
    href="#description"
    data-toggle="collapse"
    aria-expanded="true"
    aria-controls="description"
    >
        Beschreibung
        <span class="text-collapsed">ausklappen <i class="bi bi-chevron-down"></i></span>
        <span class="text-expanded">einklappen <i class="bi bi-chevron-up"></i></span>
    </a>
</p>
<div id="description" class="collapse show">
    <?=$description // ACHTUNG das wird nicht escaped, da hier HTML erwartet wird! Allerdings wird in der Datenbank KEIN HTML gespeichert!!?>
</div>
<hr>
<h4>Bitte wählen Sie einen der folgenden Termine aus!</h4>
<form action="?event=<?=$this->e($event_id)?>" method="POST">
    <div class="container">
        <div class="form-group">
            <label for="day_select">Tag auswählen</label>
            <select class="custom-select <?php if(check_val($errors, "selected_day")): ?>is-invalid<?php endif ?>" name="selected_day" id="day_select" required>
                <option value="">Tag auswählen</option>
                <?php foreach($days as $day): ?>
                    <option value="<?=$this->e($day["tagID"])?>" <?php if($day["tagID"] == check_val($values, "selected_day")): ?>selected<?php endif ?>><?=$this->e(strftime("%A %d.%m.%Y", strtotime($day["tagDatum"])))?></option>
                <?php endforeach ?>
            </select>
            <div class="invalid-feedback">
                <?php if(check_val($errors, "selected_day") === "empty"): ?>
                    Kein Tag ausgewählt.
                <?php elseif(check_val($errors, "selected_day") === "wrong_window"): ?>
                    Zeitfenster existiert nicht oder ist nicht an gewähltem Tag.
                <?php endif ?>
            </div>
        </div>
        <div class="form-group">
            <label for="timewindow_select">Zeitfenster</label>
            <select class="custom-select mb-3 <?php if(check_val($errors, "selected_timewindow")): ?>is-invalid<?php endif ?>" name="selected_timewindow" id="timewindow_select" required disabled>
                <option value="">Bitte Zeitfenster auswählen</option>
            </select>
            <div class="invalid-feedback">
                <?php if(check_val($errors, "selected_timewindow") === "empty"): ?>
                    Kein Zeitfenster ausgewählt.
                <?php elseif(check_val($errors, "selected_timewindow") === "wrong_window"): ?>
                    Zeitfenster existiert nicht oder ist nicht an gewähltem Tag.
                <?php elseif(check_val($errors, "selected_timewindow") === "already_full"): ?>
                    In diesem Zeitfenster gibt es keine freien Plätze mehr. Bitte ein anderes Zeitfenster wählen.
                <?php elseif(check_val($errors, "selected_timewindow") === "too_many_registered"): ?>
                    Es gibt nicht mehr genügend freie Plätze für alle Anmeldungen. Bitte weniger Personen anmelden oder ein anderes Zeifenster wählen.
                <?php endif ?>
            </div>
        </div>
    </div>
    <hr>
    <h4>Ihre Daten:</h4>
    <div class="container">
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-md-6">
                <label for="firstname">Vorname</label>
                <input type="text" class="form-control <?php if(check_val($errors, "firstname")): ?>is-invalid<?php endif ?>" id="firstname" name="firstname" placeholder="Vorname" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "firstname") === "empty"): ?>
                        Bitte gebe einen Vornamen an.
                    <?php endif ?>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="lastname">Nachname</label>
                <input type="text" class="form-control <?php if(check_val($errors, "lastname")): ?>is-invalid<?php endif ?>" id="lastname" name="lastname" placeholder="Nachname" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "lastname") === "empty"): ?>
                        Bitte gebe einen Nachnamen an.
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-sm-9">
                <label for="street">Straße</label>
                <input type="text" class="form-control <?php if(check_val($errors, "street")): ?>is-invalid<?php endif ?>" id="street" name="street" placeholder="Straße" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "street") === "empty"): ?>
                        Bitte gebe eine Straße an.
                    <?php endif ?>
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="house_nr">Hausnummer</label>
                <input type="text" class="form-control <?php if(check_val($errors, "house_nr")): ?>is-invalid<?php endif ?>" id="house_nr" name="house_nr" placeholder="Hausnummer" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "house_nr") === "empty"): ?>
                        Bitte gebe eine Hausnummer an.
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-sm-3 col-md-2">
                <label for="postal_code">Postleitzahl</label>
                <input type="number" class="form-control <?php if(check_val($errors, "postal_code")): ?>is-invalid<?php endif ?>" id="postal_code" name="postal_code" placeholder="PLZ" max="99999" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "postal_code") === "empty"): ?>
                        Bitte gebe eine Postleitzahl an.
                    <?php endif ?>
                </div>
            </div>
            <div class="form-group col-sm-9 col-md-10">
                <label for="city">Wohnort</label>
                <input type="text" class="form-control <?php if(check_val($errors, "city")): ?>is-invalid<?php endif ?>" id="city" name="city" placeholder="Wohnort" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "city") === "empty"): ?>
                        Bitte gebe eine Stadt an.
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-md-6">
                <label for="email">E-Mail Adresse</label>
                <input type="email" class="form-control <?php if(check_val($errors, "email")): ?>is-invalid<?php endif ?>" id="email" name="email" placeholder="E-Mail Adresse" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "email") === "empty"): ?>
                        Bitte gebe eine E-Mail Adresse an.
                    <?php elseif(check_val($errors, "email") === "invalid"): ?>
                        Die E-Mail Adresse ist ungültig.
                    <?php elseif(check_val($errors, "email") === "already_used"): ?>
                        Die E-Mail wurde bereits verwendet.
                    <?php endif ?>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="phone">Telefonnummer</label>
                <input type="text" class="form-control <?php if(check_val($errors, "phone")): ?>is-invalid<?php endif ?>" id="phone" name="phone" placeholder="Telefonnummer" required>
                <div class="invalid-feedback">
                    <?php if(check_val($errors, "phone") === "empty"): ?>
                        Bitte gebe eine Telefonnummer an.
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <h4>Geben Sie nun zur Verifizierung bitte noch folgende Ziffernfolge ein:</h4>
    <div class="container">
        <div class="mb-3">
            <img src="captcha.php" alt="CAPTCHA" id="captcha" class="captcha-image mr-2">
            <button class="btn btn-outline-info btn-sm" id="newcaptcha" type="button"><i class="bi bi-arrow-clockwise"></i> Neue Ziffernfolge</button>
        </div>
        <div class="form-group mb-3">
            <label for="captcha">Ziffernfolge</label>
            <input type="number" name="captcha" id="captcha" class="form-control <?php if(check_val($errors, "captcha")): ?>is-invalid<?php endif ?>" placeholder="12345" required>
            <div class="invalid-feedback">
                <?php if(check_val($errors, "captcha") === "wrong"): ?>
                    Bitte geben sie die korrekte Ziffernfolge ein.
                <?php endif ?>
            </div>
        </div>
    </div>
    <hr>
    <br>
    <p>Bei erfolgreicher Anmeldung erhalten Sie eine E-Mail an die von Ihnen eingegebene E-Mail Adresse mit dem Termin und weiteren Informationen!</p>
    <button class="btn btn-primary" type="submit">Weiter <i class="bi bi-arrow-right"></i></button>
</form>
<?=$this->push("scripts")?>
    <script src="js/script.js"></script>
    <script src="js/flatpickr.js"></script>
    <script>
        function update_timewindows(){
            day_id = $("#day_select").val();
            timewindow_select = $('#timewindow_select');
            if (day_id) {
                $.getJSON("get_timewindows.php", {"day": day_id}, function(data) {
                    timewindow_select.prop("disabled", false);
                    default_option = $('#timewindow_select option[value=""]');
                    timewindow_select.empty();
                    timewindow_select.append(default_option);
                    $.each(data, function(i, obj){
                        from = flatpickr.formatDate(flatpickr.parseDate(obj["von"],"H:i"), "H:i");
                        until = obj["bis"] ? ` - ${flatpickr.formatDate(flatpickr.parseDate(obj["bis"],"H:i"), "H:i")}`:"";
                        text = `${from}${until}`;
                        option = $("<option>").text(text).attr("value", obj["zeitfensterID"]);
                        if (obj["disabled"]) {
                            option.prop("disabled", true);
                            option.text(`${option.text()} - keine Plätz mehr frei!`);
                        } else {
                            free_space = obj["maxTeilnehmer"] - obj["participants"];
                            free_space_text = free_space > 1 ? "freie Plätze":"freier Platz"
                            option.text(`${option.text()} - ${free_space} ${free_space_text}`);
                        }
                        timewindow_select.append(option);
                    });
                });
            } else {
                timewindow_select.prop("disabled", true);
                default_option = $('#timewindow_select option[value=""]');
                timewindow_select.empty();
                timewindow_select.append(default_option);
            }
        }
        $(function(){
            $("#newcaptcha").click(function() {
                $("#captcha").attr("src", "captcha.php?"+(new Date()).getTime());
            });
            update_timewindows();
            $("#day_select").change(function() {update_timewindows()});
        });
    </script>
<?=$this->end()?>