<?=$this->layout("admin::index", ["no_logout" => "true", "title" => "Account-Erstellen", "messages" => $messages ?? null])?>

<div>
    <form action="add_admin.php" method="post">
        <div class="form-group">
            <label for="username">Benutzername</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="admin" require>
        </div>
        <div class="form-group">
            <label for="password">Passwort</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Passwort" require>
        </div>
        <button type="submit" class="btn btn-success">Erstellen <i class="bi bi-person-plus"></i></button>
    </form>
</div>