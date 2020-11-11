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
    <title>Rajz</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="polygonDrawer/fabric.js"></script>

    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .canvas-container { margin: 0 auto; }
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
        <input name="gpxFile[]" type="file" style="text-align: center; margin: auto" multiple>
    </form>
    <button class="btn btn-primary" id="upload" title="Upload gpx">Upload gpx</button>

    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>

    <script src="polygonDrawer/script.js"></script>
</body>
</html>


