<?php
/**
 * @var mysqli $link
 */

require_once 'config.php';

define('ORIGINAL', 'images/orig/');
define('EDITED', 'images/edit/');
define('TMP', 'gpx/tmp');

$username = $_POST['userName'];
$id = $_POST["user_id"];

$img = $_POST['imgEditstrava'];
$imgOrig = $_POST['imgOrigstrava'];

$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);

$imgOrig = str_replace('data:image/png;base64,', '', $imgOrig);
$imgOrig = str_replace(' ', '+', $imgOrig);

$data = base64_decode($img);
$dataOrig = base64_decode($imgOrig);

$file = $username . "-" . uniqid();

$success = file_put_contents(EDITED . "tmp/" . $file . ".png", $data);
$successOrig = file_put_contents(ORIGINAL . "tmp/" . $file . ".png", $dataOrig);

echo shell_exec("python3 StravaApi.py " . $id . " 2>&1");
mysqli_close($link);
header("Location: /gpxSelector.php");