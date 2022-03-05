<form id="mail_form" method="POST">
    <input type="checkbox" name="send_email" id="send_email" value="1" hidden checked required>
    <div class="form-group">
        <label for="email_addresses">Email Adressen</label>
        <select class="custom-select <?=$this->e($errors["email_addresses"])?>" name="email_addresses[]" id="email_addresses" data-placeholder="E-Mails auswählen" data-allow-clear="1" multiple required>
            <?php foreach($data_participants as $participant): ?>
                <option value="<?= $this->e($participant["id"])?>" <?php if(in_array($participant["id"], $emails_selected)): ?>selected<?php endif ?>>
                    <?=$this->e($participant["email"])?> - <?=$this->e($participant["firstname"])?> <?=$this->e($participant["lastname"])?>
                </option>
            <?php endforeach ?>
        </select>
        <div class="invalid-feedback">
            Es muss mindestens eine E-Mail Adresse ausgewählt sein
        </div>
    </div>
    <div class="form-group custom-control custom-switch">
        <input type="checkbox" name="auto_email" id="auto_email" class="custom-control-input">
        <label for="auto_email" class="custom-control-label">automatische Email senden <b>(Achtung dauert sehr lange!)</b></label>
    </div>
    <div id="email_msg">
        <div class="form-group">
            <label for="email_subject">Betreff</label>
            <input type="text" class="form-control <?=$this->e($errors["email_subject"])?>" id="email_subject" placeholder="Betreff" name="email_subject" required>
            <div class="invalid-feedback">
                Betreff darf nicht leer sein
            </div>
        </div>
        <div class="form-group">
            <label for="email">E-Mail Nachricht</label>
            <input type="hidden" name="email" id="email" required>
            <div id="email_editor">
                <?php if(!empty($email)):?>
                    <?=parse_delta($email)?>
                <?php endif?>
            </div>
            <div class="invalid-feedback">
                E-Mail
            </div>
        </div>
    </div>
    <button class="btn btn-success" type="submit"><i class="bi bi-send"></i> Senden</button>
</form>