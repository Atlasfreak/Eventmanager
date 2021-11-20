<?php
include("../inc/db.php");
require("../config.php");

$query = $db->mysql->prepare("SELECT * FROM admin");
$query->execute();
if($query->rowCount() > 0) {
    die(header("Location:../admin/"));
}

if(isset($_POST["username"], $_POST["password"])) {
    $username = htmlspecialchars($_POST["username"]);
    $passwort = htmlspecialchars($_POST["password"]);

    $options = [
        'cost' => 14, //more cost = more secure hash, but longer hashing time
    ];

    $hash = password_hash($passwort, PASSWORD_BCRYPT, $options);
    $sql = "INSERT INTO admin (id, username, pass) VALUES (NULL, ?, ?);";
    $qry = $db->mysql->prepare($sql);
    $qry->execute(Array($username, $hash));
    if($qry->errorInfo()[0] != 0) throw new Exception($qry->errorInfo()[2]);
    echo "<meta http-equiv='refresh' content='5; url=../admin/'> Admin erfolgreich erstellt.";
} else {

include("inc/header.php");
?>
        <title>Admin erstellen</title>
    </head>
    <body>
        <div class="container">
            <h1 class="text-center">Admin erstellen</h1>
            <form action="adminhinzufuegen.php" method="post">
                <div class="form-group">
                    <label for="username">Benutzername*</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="admin" require>
                </div>
                <div class="form-group">
                    <label for="password">Passwort*</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Passwort" require>
                </div>
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </form>
        </div>
    </body>
</html>
<?php } ?>