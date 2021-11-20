<?=$this->layout("main::layout", ["title" => $title."-Anmeldung", "path" => ""])?>

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
            <select class="custom-select" name="selected_day" id="day_select" required>
                <option value="">Tag auswählen</option>
                <?php foreach($days as $day): ?>
                    <option value="<?=$this->e($day["tagID"])?>"><?=$this->e(strftime("%A %d.%m.%Y", strtotime($day["tagDatum"])))?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group">
            <label for="timewindow_select">Zeitfenster</label>
            <select class="custom-select mb-3" name="selected_timewindow" id="timewindow_select" required disabled>
                <option value="">Bitte Zeitfenster auswählen</option>
            </select>
        </div>
    </div>
    <hr>
    <h4>Ihre Daten:</h4>
    <div class="container">
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-md-6">
                <label for="firstname">Vorname</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Vorname" required>
            </div>
            <div class="form-group col-md-6">
                <label for="lastname">Nachname</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Nachname" required>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-sm-9">
                <label for="street">Straße</label>
                <input type="text" class="form-control" id="street" name="street" placeholder="Straße" required>
            </div>
            <div class="form-group col-sm-3">
                <label for="house_nr">Hausnummer</label>
                <input type="text" class="form-control" id="house_nr" name="house_nr" placeholder="Hausnummer" required>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-sm-3 col-md-2">
                <label for="postal_code">Postleitzahl</label>
                <input type="number" class="form-control" id="postal_code" name="postal_code" placeholder="PLZ" max="99999" required>
            </div>
            <div class="form-group col-sm-9 col-md-10">
                <label for="city">Wohnort</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Wohnort" required>
            </div>
        </div>
        <div class="form-row mb-3 mb-md-0">
            <div class="form-group col-md-6">
                <label for="email">E-Mail Adresse</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="E-Mail Adresse" required>
            </div>
            <div class="form-group col-md-6">
                <label for="phone">Telefonnummer</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telefonnummer" required>
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
            <input type="number" name="captcha" id="captcha" class="form-control" placeholder="12345" required>
        </div>
    </div>
    <hr>
    <br>
    <p>Bei erfolgreicher Anmeldung erhalten Sie eine E-Mail an die von Ihnen eingegebene E-Mail Adresse mit dem Termin und weiteren Informationen!</p>
    <button class="btn btn-primary" type="submit">Weiter</button>
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
                        text = `${from}${until}`
                        timewindow_select.append($("<option>").text(text).attr("value", obj["zeitfensterID"]))
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