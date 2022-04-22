<?=$this->layout("admin::index", ["no_logout" => "true", "title" => "Anmelden"])?>

<div>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Benutzername</label>
            <input name="registration_username" class="form-control <?php if($failed): ?>is-invalid<?php endif ?>" placeholder="Benutzername" type="text" aria-describedby="usernameFeedback">
            <div class="invalid-feedback" id="usernameFeedback">
                Benutzername oder Passwort sind flasch.
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control <?php if($failed): ?>is-invalid<?php endif ?>" name="registration_password" placeholder="******" type="password" aria-describedby="passwordFeedback">
            <div class="invalid-feedback" id="passwordFeedback">
                Benutzername oder Passwort sind flasch.
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-success"><i class="bi bi-box-arrow-in-right"></i> Anmelden</button>
        </div>
    </form>
</div>

