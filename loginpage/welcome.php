<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Üdvözöllek</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. udvozollek az oldalamon.</h1>
        <p>
            <a href="draw.php" class="btn btn-primary">Rajzolas</a>
            <a href="profileDraw.php" class="btn btn-primary">Rajzaim</a>
            <a href="profileSettings.php" class="btn btn-primary">Profil beállítások</a>
            <a href="logout.php" class="btn btn-danger">Kijelentkezes</a>
        </p>
    </div>
</body>
</html>