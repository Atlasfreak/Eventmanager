<?php
function passwordVerifizieren($username,$pass_input,$mysql) {
    $sql = "SELECT pass, username FROM admin WHERE username=?";
    $qry = $mysql->prepare($sql);
    $qry->execute(Array($username));
    if($data = $qry->fetch()) {
        $pass = $data["pass"];
        $datregistration_username = $data["username"];
        if($username !== $datregistration_username) {
            return false;
        }
        elseif(password_verify($pass_input, $pass)){
            $err_msg = "";
            return true;
        }
        else{
            session_destroy();
            return false;
            $err_msg = 'Passwort falsch!'; //nachträglich natürlich durch ID ODER PW ersetzen!
        }
    }
    else {
        return false;
        $err_msg = 'User ID falsch!'; //nachträglich natürlich durch ID ODER PW ersetzen!
    }
}

$failed = false;
if(isset($_POST["registration_username"],$_POST["registration_password"])) {
    if(passwordVerifizieren(htmlspecialchars($_POST["registration_username"]),htmlspecialchars($_POST["registration_password"]),$db->mysql)) {
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
elseif(!isset($_SESSION["registration_password"],$_SESSION["registration_username"])) {
    session_destroy();
}

echo $templates->render("admin::login", ["failed" => $failed, "xyz" => "sdfjkhasdjkhf"]);

?>