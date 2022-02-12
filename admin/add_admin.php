<?php
include("inc/header.php");
include("../inc/db.php");

$query = $db->query("SELECT * FROM admin");
if($query->rowCount() > 0 and !is_logged_in()) redirect("../admin");

if(!isset($_POST["username"], $_POST["password"])) {
    echo $templates->render("admin::add_admin");
    exit;
}

$username = htmlspecialchars($_POST["username"]);
$password = htmlspecialchars($_POST["password"]);

$query_username = $db->query("SELECT * FROM admin WHERE username = ?", array($username));
if ($query_username->rowCount() > 0) {
    echo $templates->render("admin::add_admin", array(
        "messages" => add_type_to_msgs(["Dieser Nutzer existiert bereits!"], "danger")
        ));
    exit;
}

$options = [
    'cost' => 14, //more cost = more secure pass$password_hash, but longer hashing time
];

$pwd_peppered = hash_hmac("sha256", $password, CONFIG_DATA["general"]["secret"]);
$password_hash = password_hash($pwd_peppered, PASSWORD_BCRYPT, $options);
$data = array(
    "username" => $username,
    "pass" => $password_hash
);
$db->insert("admin", $data);
echo "<meta http-equiv='refresh' content='5; url=../admin/'> Admin erfolgreich erstellt.";

?>