<?php
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
require_once "config.php";
 
$username = $password = "";
$username_err = $password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Add meg a felhasznalo neved.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Add meg a jelszavad.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: welcome.php");
                        } else{
                            $password_err = "A jelszo nem egyezik meg.";
                        }
                    }
                } else{
                    $username_err = "Nem talalhato felhasznalo ilyen nevvel.";
                }
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
    <title>Belepes</title>
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
        <h2>Belepes</h2>
        <p>Tolsd ki az adataidat a belepeshez</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Felhasznalonev</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Jelszo</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Belepes">
            </div>
            <p>Nincs meg fiokod? <a href="register.php">Regisztralj itt</a>.</p>
            <p>Elfelejtetted a jelszot? <a href="reset-password.php">Jelszo visszallitas</a>.</p>
        </form>
    </div>    
</body>
</html>