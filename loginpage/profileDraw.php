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
    <title>Profil</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .table td { text-align: center; }
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

<?php

$sql = "SELECT * FROM images WHERE user_id= " . $_SESSION['id'];

$result = mysqli_query($link, $sql);

if ($result->num_rows === 0) {
    echo "<h3>Meg nem rajzoltal semmit :(</h3>";
    exit;
} else {
    echo "<table class='table'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th class='text-center' scope='col'>#</th>";
    echo "<th class='text-center' scope='col'>Image</th>";
    echo "<th class='text-center' scope='col'>GPX</th>";
    echo "<th class='text-center' scope='col'>Score</th>";
    echo "<th class='text-center' scope='col'>Delete</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $id = 1;

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo "<tr>";
        echo "<th class='text-center' scope='row' style='font-size: 40px; padding-top: 52px'>" . $id++ . "</th>";
        echo "<td class='text-center'><img src='images/orig/" . $row['name'] . ".png" . "' width='150'></td>";

        echo "<td class='text-center'><img src='images/routes/" . $row['name'] . ".png" . "' width='150'></td>";
        echo "<td class='text-center' style='font-size: 40px; padding-top: 52px'>" . $row['score'] . "</td>";
        echo "<td class='text-center'><form method='post'><input class='btn btn-danger' type='submit' id='" . $row['name'] . "' name='deleteBTN' value='Törlés' onclick='deleteSelected(this.id)' style='margin-top: 61px'></form></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
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
    header( "refresh:0.5;url=profileDraw.php" );
}
?>


</body>
</html>
