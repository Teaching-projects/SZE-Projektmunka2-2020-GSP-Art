<?php
/**
 * @var mysqli $link
 */

require_once 'config.php';

define('ORIGINAL', 'images/orig/');
define('EDITED', 'images/edit/');
define('TMP', 'gpx/tmp/');

$username = $_POST['userName'];
$id = $_POST["id"];

$img = $_POST['imgEdit'];
$imgOrig = $_POST['imgOrig'];

$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);

$imgOrig = str_replace('data:image/png;base64,', '', $imgOrig);
$imgOrig = str_replace(' ', '+', $imgOrig);

$data = base64_decode($img);
$dataOrig = base64_decode($imgOrig);

if (count($_FILES['gpxFile']['name']) == 1) {
    $file = $username . "-" . uniqid();

    $success = file_put_contents(EDITED . "tmp/" . $file . ".png", $data);
    $successOrig = file_put_contents(ORIGINAL . "tmp/" . $file . ".png", $dataOrig);

    $tmp_name = $_FILES["gpxFile"]["tmp_name"][0];

    move_uploaded_file($tmp_name, TMP . $file . ".gpx");

    $sql = "INSERT INTO tmp_gpx (user_id, name) VALUES (?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "is", $id, $file);
        if (mysqli_stmt_execute($stmt)) {
            echo shell_exec("python gpxToPng.py " . $username . " 2>&1");

            $sql = "SELECT score FROM tmp_gpx WHERE name = '" . $file . "'";
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_row($result);

            $score = $row[0];


            $sql = "INSERT INTO images (user_id, name, score) VALUES (?, ?, ?)";

            if ($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "isd", $id, $file, $score);
                if (mysqli_stmt_execute($stmt)) {
                    echo shell_exec("python clearTmps.py " . $file . " 2>&1");

                    header("Location: /profileDraw.php");
                } else {
                    echo '<script language="javascript">';
                    echo 'alert("Valami hiba tortent1")';
                    echo '</script>';
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            echo '<script language="javascript">';
            echo 'alert("Valami hiba tortent2")';
            echo '</script>';
            exit;
        }
    }
} else {
    for ($i = 0; $i < count($_FILES['gpxFile']['name']); $i++) {
        $file = $username . "-" . uniqid();

        $success = file_put_contents(EDITED . "tmp/" . $file . ".png", $data);
        $successOrig = file_put_contents(ORIGINAL . "tmp/" . $file . ".png", $dataOrig);

        $tmp_name = $_FILES["gpxFile"]["tmp_name"][$i];

        move_uploaded_file($tmp_name, TMP . $file . ".gpx");

        $sql = "INSERT INTO tmp_gpx (user_id, name) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "is", $id, $file);
            if (mysqli_stmt_execute($stmt)) {
                echo shell_exec("python gpxToPng.py " . $username . " 2>&1");

                header("Location: /gpxSelector.php");
            } else {
                echo '<script language="javascript">';
                echo 'alert("Valami hiba tortent3")';
                echo '</script>';
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($link);