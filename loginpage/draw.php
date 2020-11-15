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

$sql = "SELECT access_token FROM users WHERE id = " . $_SESSION['id'];
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_row($result);
$strava = "none";

if ($row[0] == "unset"){
    $strava = "none";
} else {
    $strava = "inline";
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rajz</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    <script src="polygonDrawer/fabric.js"></script>

    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .canvas-container { margin: 0 auto; }
        button{
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. udvozollek az oldalamon.</h1>
        <p>
        <a href="draw.php" class="btn btn-primary">Rajzolas</a>
        <a href="globalDraw.php" class='btn btn-primary'>Toplista</a>
        <a href="profileDraw.php" class="btn btn-primary">Rajzaim</a>
        <a href="profileSettings.php" class="btn btn-primary">Profil beállítások</a>
        <a href="logout.php" class="btn btn-danger">Kijelentkezes</a>

        </p>
    </div>

    <h3>Draw a polygon</h3>
    <div class="fabric-canvas" style="text-align: center">
        <canvas id="canvas" width="500" height="500" style="border: 2px solid black"></canvas>
        <canvas id="blank" width="500" height="500" style="display: none"></canvas>
    </div>

    <button class="btn btn-danger" id="deleteObj" title="Delete Selected">Delete selected</button>
    <button class="btn btn-primary" id="poly" title="Draw a polygon">Draw a polygon</button>

    <form method="post" action="uploadGpx.php" enctype="multipart/form-data" id="form">
        <input name="userName" type="hidden" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
        <input name="id" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
        <input id="imgOrig" name="imgOrig" type="hidden">
        <input id="imgEdit" name="imgEdit" type="hidden">
        <label for="fileupload" class="btn btn-primary" style="margin: 10px 0;">
            Select files
            <input id="fileupload" name="gpxFile[]" type="file" style="display: none;" multiple>
        </label>
        <br><span id="fileuploadtext"></span>
    </form>
    <button class="btn btn-primary" id="upload" title="Upload gpx" style="display: inline">Upload gpx</button>
    <button class="btn btn-primary" id="fromStrava" title="Select From Strava" style="display: <?php echo $strava; ?>">Select from Strava</button>

    <form method="post" action="selectFromStrava.php" id="strava">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']?>">
        <input name="userName" type="hidden" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
        <input  id="imgOrigstrava" name="imgOrigstrava" type="hidden">
        <input id="imgEditstrava" name="imgEditstrava" type="hidden">
    </form>

    <script src="polygonDrawer/script.js"></script>
</body>
</html>


