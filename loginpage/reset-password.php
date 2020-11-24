<?php
session_start();

/*
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
*/
 
require_once "config.php";
 
$new_password = $confirm_password = $email = "";
$new_password_err = $confirm_password_err = $email_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["email"]))) {
        $email_err = "adj meg email cimet";
    } else {
        $email = trim($_POST["email"]);
    }
 
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Add meg az uj jelszavad.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "A jelszonak legalabb 6 karakternek kell lennie.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Erosisd meg a jelszavad.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "A jelszo nem egyezik.";
        }
    }
        
    if(empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $param_password, $param_email);
            
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Valami hiba tortent, probald ujra kesobb.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jelszo visszallitas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Jelszo visszallitas</h2>
        <p>Tolsd ki az adatokat a jelszo visszallitashoz.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email cim</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>Uj jelszo</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Jelszo megerositese</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Kuldes">
                <a class="btn btn-danger" href="welcome.php">Megse</a>
            </div>
        </form>
    </div>    
</body>
</html>