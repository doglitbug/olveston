<?php
//Start session
session_start();

//Grab database connection varibles
require_once("connectvars.php");
//Connect to database
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

//***************************** Function for debugging *************************
function debug($value) {
    echo "<pre class='debug'>";
    print_r($value);
    echo "</pre>";
}

//***************************** Check for room_id ******************************
//Make sure we have a room id
//Reset frame so we don't get the last rooms frames
$resetFrame = false;

//Check for room id in request
if (isset($_REQUEST['room_id'])) {
//TODO: strip tags ect for SQL injection
    $roomQuery = "SELECT * FROM tbl_room WHERE room_id=" . $_REQUEST['room_id'];
    $resetFrame = true;
//else check session
} else if (isset($_SESSION['room_id'])) {
    $roomQuery = "SELECT * FROM tbl_room WHERE room_id=" . $_SESSION['room_id'];
} else {
//Grab first room from database
    $roomQuery = "SELECT * FROM tbl_room LIMIT 1";
    $resetFrame = true;
}

//Grab room details and check it is a valid room
$result = mysqli_query($dbc, $roomQuery) or die("Error:" . mysqli_error($dbc));
if (mysqli_num_rows($result) == 0) {
    echo "Room not found";
//TODO: Deal with this error gracefully
} else {
//Grab room details
    $room_details = mysqli_fetch_assoc($result);
    $_SESSION['room_id'] = $room_details['room_id'];
    $_SESSION['room_name'] = $room_details['name'];
    $_SESSION['room_desc'] = $room_details['desc'];
}

//***************************** Check for frame ********************************
//Make sure we have a frame
//Check for frame in request
if (isset($_REQUEST['frame'])) {
//TODO: strip tags ect for SQL injection
    $frameQuery = "SELECT * FROM tbl_frame WHERE room_id=" . $_SESSION['room_id'] . " AND frame=" . $_REQUEST['frame'];
//else check session
} else if (isset($_SESSION['frame'])) {
    $frameQuery = "SELECT * FROM tbl_frame WHERE room_id=" . $_SESSION['room_id'] . " AND frame=" . $_SESSION['frame'];
} else {
//Grab first frame from database
    $frameQuery = "SELECT * FROM tbl_frame WHERE room_id=" . $_SESSION['room_id'] . " LIMIT 1";
}

if ($resetFrame == true) {
    $frameQuery = "SELECT * FROM tbl_frame WHERE room_id=" . $_SESSION['room_id'] . " LIMIT 1";
}

//Grab room details and check it is a valid room
$result = mysqli_query($dbc, $frameQuery) or die("Error:" . mysqli_error($dbc));
if (mysqli_num_rows($result) == 0) {
    echo "Frame not found";
//TODO: Deal with this error gracefully
} else {
//Grab frame details
    $frame_details = mysqli_fetch_assoc($result);
    $_SESSION['frame'] = $frame_details['frame'];
    $_SESSION['frame_id'] = $frame_details['frame_id'];
    $_SESSION['frame_image'] = $frame_details['image'];
}

//***************************** Generate left/right frame functions ************
//Get next frame
$frameQuery = "SELECT frame FROM tbl_frame WHERE room_id=" . $_SESSION['room_id']
        . " ORDER BY frame > " . $_SESSION['frame'] . " DESC, frame ASC";
$result = mysqli_query($dbc, $frameQuery) or die("Error: " . mysqli_error($dbc));

$nextFrame = mysqli_fetch_row($result);

//Get previous frame
$frameQuery = "SELECT frame FROM tbl_frame WHERE room_id=" . $_SESSION['room_id']
        . " ORDER BY frame >= " . $_SESSION['frame'] . " ASC, frame DESC";
$result = mysqli_query($dbc, $frameQuery) or die("Error: " . mysqli_error($dbc));

$prevFrame = mysqli_fetch_row($result);

function generateLeftRight($nextFrame, $prevFrame) {

    echo "<script type='text/javascript'>";

    echo "function prevFrame(){";
    echo "document.location.href='";
    echo $_SERVER['PHP_SELF'] . "?frame=$prevFrame'";
    echo ";}";

    echo "function nextFrame(){";
    echo "document.location.href='";
    echo $_SERVER['PHP_SELF'] . "?frame=$nextFrame'";
    echo ";}";

    echo "</script>";
}

function showInfo($dbc, $item_id) {
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "utf-8" />
        <title>Olveston Historic Home</title>
        <link type = "text/css" rel = "stylesheet" href = "scripts/stylesheet.css" />
        <script type="text/javascript" src="scripts/getItem.js"></script>
        <?php generateLeftRight($nextFrame[0], $prevFrame[0]); ?>
    </head>
    <body>
        <div id="header"></div>
		<div class="headCont"></div>
		<div class="navBar"></div>
		
        <div id="page">