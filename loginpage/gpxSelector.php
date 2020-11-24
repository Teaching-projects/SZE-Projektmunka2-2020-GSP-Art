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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Back'])) {
        $userLocal = $_POST['userNameToPhp'];
        echo shell_exec("python clearTmps.py " . $userLocal . " onlyUserName 2>&1");
        header("Location: draw.php");
    }
}
?>

<html>
<head>
    <title>Select your gpx file to save</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
            echo shell_exec("python3 clearTmps.py " . $filename . " 2>&1");
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

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method='post' name='backForm' id='backFrom'>
    <input type='hidden' name='userNameToPhp' value='<?php echo htmlspecialchars($_SESSION['username']); ?>'>
    <input type='submit' name='Back' id='Back' value='Mégse' class='btn btn-danger float-right mr-5'>
</form>

</body>
</html>




