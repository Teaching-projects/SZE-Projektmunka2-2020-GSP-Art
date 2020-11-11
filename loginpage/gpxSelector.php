<?php
/**
 * @var mysqli $link
 */

require_once "config.php";

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}?>

<html>
<head>
    <title>Select your gpx file to save</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>

<?php
$sql = "SELECT * FROM tmp_gpx WHERE user_id= " . $_SESSION['id'];

$result = mysqli_query($link, $sql);

echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th class='text-center' scope='col'>GPX</th>";
echo "<th class='text-center' scope='col'>Score</th>";
echo "<th class='text-center' scope='col'>Select</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td class='text-center'><img src='images/routes/tmp/" . $row['name'] . ".png" . "' width='150'></td>";
    echo "<td class='text-center' style='font-size: 40px; padding-top: 52px'>" . $row['score'] . "</td>";
    echo "<td class='text-center'><form method='post'><input type='hidden' name='score' value='" . $row['score'] . "'><input type='hidden' value='" . $row['name'] .  "' name='filename'><input class='btn btn-primary' type='submit' name='selectBTN' value='Kiválasztás' style='margin-top: 61px'></form></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

$filename = "";

if (array_key_exists('selectBTN', $_POST)) {
    $filename = $_POST['filename'];
    $score = $_POST['score'];
    $userID = $_SESSION['id'];


    $sql = "INSERT INTO images (user_id, name, score) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "isd", $userID, $filename, $score);
        if (mysqli_stmt_execute($stmt)) {
            echo shell_exec("python clearTmps.py " . $filename . " 2>&1");

            header("Location: /profileDraw.php");
        } else {
            echo '<script language="javascript">';
            echo 'alert("Valami hiba tortent")';
            echo '</script>';
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
</body>
</html>




