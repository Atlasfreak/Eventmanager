<form id="mail_form" action="post" method="POST">
    <div class="form-group">
        <label for="mail_adresses">Email Adressen</label>
        <select class="custom-select" name="mail_adresses[]" id="mail_adresses" data-placeholder="E-Mails auswählen" data-allow-clear="1" multiple>
            <?php foreach($data_participants as $participant): ?>
                <option value="<?= $this->e($participant["id"])?>" <?php if(in_array($participant["id"], $emails_selected)): ?>selected<?php endif ?>>
                    <?= $this->e($participant["email"])?> - <?= $this->e($participant["firstname"])?> <?= $this->e($participant["lastname"])?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group custom-control custom-switch">
        <input type="checkbox" name="auto_email" id="auto_email" class="custom-control-input">
        <label for="auto_email" class="custom-control-label">automatische Email senden</label>
    </div>
    <div id="email_msg">
        <div class="form-group">
            <label for="email_subject">Betreff</label>
            <input type="text" class="form-control" id="email_subject" placeholder="Betreff">
        </div>
        <div class="form-group">
            <label for="email">E-Mail Nachricht</label>
            <input type="hidden" name="email" id="email">
            <div id="email_editor">
                <?php if(!empty($email)):?>
                    <?=parse_delta($email)?>
                <?php endif?>
            </div>
            <div class="invalid-feedback">
                Beschreibung
            </div>
        </div>
    </div>
    <button class="btn btn-success" type="submit"><i class="bi bi-send"></i> Senden</button>
</form>