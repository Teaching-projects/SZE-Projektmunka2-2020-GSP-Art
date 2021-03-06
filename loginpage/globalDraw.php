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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplista</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .table td { text-align: center; }
    </style>
</head>
<body>
<div class="page-header">
    <h1>Kedves <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b> üdvözöllek az oldalunkon!</h1>
    <p>
        <a href="draw.php" class="btn btn-primary">Rajzolás</a>
        <a href="globalDraw.php" class='btn btn-primary'>Toplista</a>
        <a href="profileDraw.php" class="btn btn-primary">Mentett rajzaim</a>
        <a href="profileSettings.php" class="btn btn-primary">Profil beállítások</a>
        <a href="logout.php" class="btn btn-danger">Kijelentkezés</a>
    </p>
</div>

<?php

$sql = "SELECT images.name AS 'name', ROUND(SUM(images.score), 3) AS score, users.username AS username FROM images INNER JOIN users ON users.id = images.user_id GROUP BY users.username ORDER BY score DESC;";

$result = mysqli_query($link, $sql);

if ($result->num_rows === 0) {
    echo "<h3>Sajnos még nem rajzoltál semmit :(</h3>";
    exit;
} else {
    echo "<div class='w-50 mx-auto'>";
    echo "<table class='table table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th class='text-center' scope='col'>#</th>";
    echo "<th class='text-center' scope='col'>Felhasználónév</th>";
    echo "<th class='text-center' scope='col'>Összpontszám</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $id = 1;

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo "<tr>";
        echo "<th class='text-center' scope='row' style='font-size: 40px; padding-top: 52px'>" . $id++ . "</th>";
        echo "<th class='text-center' scope='row' style='font-size: 40px; padding-top: 52px'>" . $row['username'] . "</th>";
        echo "<td class='text-center' style='font-size: 40px; padding-top: 52px'>" . $row['score'] . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
?>

<script>
    function deleteSelected(selectedId) {
        console.log(selectedId);
        createCookie('test', selectedId, 1);
    }

    function createCookie(name, value, min) {
        var expires;
        if (min) {
            var date = new Date();
            date.setTime(date.getTime() + (min * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else {
            expires = "";
        }
        document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
    }
</script>

<?php
$filename = "";

if (array_key_exists('deleteBTN', $_POST)) {
    $filename = $_COOKIE['test'];
    unlink("gpx/" . $filename . ".gpx");
    unlink("images/edit/" . $filename . ".png");
    unlink("images/orig/" . $filename . ".png");
    unlink("images/filled/" . $filename . ".png");
    unlink("images/routes/" . $filename . ".png");

    mysqli_query($link, "DELETE FROM images WHERE name = '" . $filename . "'");

    mysqli_close($link);
    header( "refresh:1;url=profileDraw.php" );
}
?>


</body>
</html>
