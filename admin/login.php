<?php
function verify_password(string $username, string $password, Database $db) {
    $sql = "SELECT pass, username FROM admin WHERE username=?";
    $qry = $db->query($sql);
    if($data = $qry->fetch()) {
        $data_password = $data["pass"];
        $data_username = $data["username"];
        if($username !== $data_username) {
            return false;
        }
        elseif(password_verify($password, $data_password)){
            return true;
        }
        else{
            session_destroy();
            return false;
        }
    }
    else {
        session_destroy();
        return false;
    }
}

$failed = false;
if(isset($_POST["registration_username"], $_POST["registration_password"])) {
    if(verify_password(htmlspecialchars($_POST["registration_username"]), htmlspecialchars($_POST["registration_password"]), $db)) {
        $_SESSION["registration_username"] = $_POST["registration_username"];
        $_SESSION["registration_password"] = $_POST["registration_password"];
        die(header("Location:../admin/"));
    }
    else {
        unset($_POST["registration_username"]);
        unset($_POST["registration_password"]);
        $failed = true;
    }
}
elseif(!isset($_SESSION["registration_password"], $_SESSION["registration_username"])) {
    session_destroy();
}

echo $templates->render("admin::login", ["failed" => $failed]);

?>