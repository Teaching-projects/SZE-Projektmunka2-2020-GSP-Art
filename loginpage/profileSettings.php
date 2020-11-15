<?php
/**
 * @var mysqli $link
 */

require_once "config.php";

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$username = $email = $pass = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        if (empty(trim($_POST["username"]))) {
            $username_err = "Add meg a felhasznaloneved";
        } else {
            $sql = "SELECT id FROM Users WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                $param_username = trim($_POST["username"]);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $username_err = "A felhasznalonev mar foglalt.";
                    } else {
                        $username = trim($_POST["username"]);
                    }
                } else {
                    echo "Valami hiba tortent, kerlek probalkozz kesobb.";
                }
                mysqli_stmt_close($stmt);
            }
        }

        if (empty($username_err)) {
            $sql = "UPDATE users SET username = ? WHERE id = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_username, $_SESSION['id']);

                $param_username = $username;

                if (mysqli_stmt_execute($stmt)) {
                    /*                    echo '<script language="javascript">';
                                        echo 'alert("Sikeresen megváltoztattad a felhasználóneved")';
                                        echo '</script>';
                    */
                    session_destroy();
                    header("location: login.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                mysqli_stmt_close($stmt);
            }
        }
    } else if (isset($_POST['email'])) {
        if (empty(trim($_POST["email"]))) {
            $email_err = "Add meg az email cimed";
        } else {
            $email = trim($_POST["email"]);
        }

        if (empty($email_err)) {
            $sql = "UPDATE users SET email = ? WHERE id = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_email, $_SESSION['id']);

                $param_email = $email;

                if (mysqli_stmt_execute($stmt)) {
                    echo '<script language="javascript">';
                    echo 'alert("Sikeresen megváltoztattad az e-mail címed")';
                    echo '</script>';
                    header("refresh:0.5;url=profileSettings.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                mysqli_stmt_close($stmt);
            }
        }
    } else if (isset($_POST['password']) && isset($_POST['confirm-password'])) {
        if (empty(trim($_POST["password"]))) {
            $password_err = "Add meg a jelszavad";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Jelszonak minimum 6 karakternek kell lennie";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty(trim($_POST["confirm-password"]))) {
            $confirm_password_err = "Erosisd meg ajelszavad.";
        } else {
            $confirm_password = trim($_POST["confirm-password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Jelszo nem egyezik.";
            }
        }

        if (empty($password_err) && empty($confirm_password_err)) {
            $sql = "UPDATE users SET password = ? WHERE id = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_password, $_SESSION['id']);

                $param_password = password_hash($password, PASSWORD_DEFAULT);

                if (mysqli_stmt_execute($stmt)) {
                    session_destroy();
                    header("location: login.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                mysqli_stmt_close($stmt);
            }
        }
    } else if (isset($_POST['code']) && isset($_POST['clientSecret']) && isset($_POST['clientId'])) {
        $code = $_POST['code'];
        $secret = $_POST['clientSecret'];
        $Cid = $_POST['clientId'];

        $sql = "UPDATE users SET code = ?, client_id = ?, client_secret = ?, access_token = 'setted' WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $code, $Cid, $secret, $_SESSION["id"]);

            if (mysqli_stmt_execute($stmt)) {
                echo shell_exec("python setTokens.py " . $_SESSION["id"] . " 2>&1");
                header("location: /draw.php");
            } else {
                echo '<script language="javascript">';
                echo 'alert("Strava beallitasa nem sikerult")';
                echo '</script>';
            }
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile beállításai</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center;}
        .wrapper{ width: 350px; padding: 20px; margin: auto;}
        input[type='submit'] {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="page-header">
    <h1><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b> Profil bállításai</h1>
    <p>
        <a href="draw.php" class="btn btn-primary">Rajzolas</a>
        <a href="globalDraw.php" class='btn btn-primary'>Toplista</a>
        <a href="profileDraw.php" class="btn btn-primary">Rajzaim</a>
        <a href="profileSettings.php" class="btn btn-primary">Profil beállítások</a>
        <a href="logout.php" class="btn btn-danger">Kijelentkezes</a>
    </p>
</div>
<div class="wrapper">
    <form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="reset-username">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label for="setuser">Új felhasználónév: </label>
            <input class="form-control" type="text" id="setuser" name="username">
            <span class="help-block"><?php echo $username_err; ?></span>
            <input class="form-control btn btn-primary" type="submit" value="username váltása" name="setuser">
        </div>
    </form>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="reset-email">
        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
            <label for="setemail">Új e-mail: </label>
            <input class="form-control" type="email" id="setemail" name="email">
            <span class="help-block"><?php echo $email_err; ?></span>
            <input class="form-control btn btn-primary" type="submit" value="E-mail váltása">
        </div>
    </form>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="reset-password">
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label for="password">Új jelszó: </label>
            <input id="password" type="password" class="form-control" name="password">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
            <label for="confirm-password">Új jelszó mégegyszer: </label>
            <input id="confirm_password" type="password" class="form-control" name="confirm-password">
            <span class="help-block"><?php echo $confirm_password_err; ?></span>
            <input class="form-control btn btn-primary" type="submit" value="Jelszó váltása">
        </div>
    </form>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="connect_strava">
        <div class="form-group">
            <label for="Client_id">Strava Client ID: </label>
            <input type="text" id="Client_id" class="form-control" name="clientId">
        </div>
        <div class="form-group">
            <label for="Client_secret">Strava Client secret: </label>
            <input type="text" id="Client_secret" class="form-control" name="clientSecret">
        </div>
        <div id="codeLink" style="display: none">
            <a id="authLink" href="" target="_blank">Kattints ide, majd authentikálj a strava-hoz</a>
        </div>
        <div id="codeForm" class="form-group" style="display: none">
            <label for="code">Code from URL: </label>
            <input type="text" id="code" class="form-control" name="code">
            <span style="padding-top: 10px">Kattints a linkre és másold a URL-ből a code=[teKodod]& (csak maga a számsor kell a 'code=' és '&' az elején és végén nem!!!!!)</span>
        </div>
        <input class="form-control btn btn-primary" id="setStrava" type="submit" value="Set Strava api" style="display: none">
    </form>
</div>
</body>
<script language="JavaScript">
    var idLength = 0;
    var secretLength = 0;
    var codeLength = 0;
    var code = $("#codeForm");
    var help = $("#codeLink");
    var link = $("#authLink");
    var setBtn = $("#setStrava");

    let URL = "http://www.strava.com/oauth/authorize?client_id=clientIdToReplace&response_type=code&redirect_uri=http://localhost/exchange_token&approval_prompt=auto&scope=profile:read_all,activity:read_all";


    $("#Client_id").keyup(function () {
        idLength = $(this).val().length;
        console.log(idLength);
        if (idLength === 5 && secretLength === 40) {
            var tempText = URL.replace("clientIdToReplace", $(this).val());
            link.attr("href", tempText);
            code.show();
            help.show();
            console.log("show");

        } else {
            code.hide();
            help.hide();
            console.log("hide");
        }
    });
    $("#Client_secret").keyup(function () {
        secretLength = $(this).val().length;
        console.log(secretLength);
        if (idLength === 5 && secretLength === 40) {
            var tempText = URL.replace("clientIdToReplace", $("#Client_id").val());
            link.attr("href", tempText);
            code.show();
            help.show();
            console.log("show");
        } else {
            code.hide();
            help.hide();
            console.log("hide");
        }
    });

    $("#code").keyup(function () {
        codeLength = $(this).val().length;
        if (idLength === 5 && secretLength === 40 && codeLength === 40) {
            setBtn.show();
        } else {
            setBtn.hide();
        }
    });
</script>
</html>
