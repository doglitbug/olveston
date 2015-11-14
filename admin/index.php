<?php
//Start session
session_start();

//Grab database connection varibles
require_once("../scripts/connectvars.php");
//Connect to database
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

//***************************** Function for debugging *************************
function debug($value) {
    echo "<pre class='debug'>";
    print_r($value);
    echo "</pre>";
}

/////////////////////////// Room and frame selection ///////////////////////////
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

function createHotspotRecord($x, $y, $item_id, $dbc) {
//Concat x and y to create co-ords field
    $coords = $x . ", " . $y;
    $insertQuery = "INSERT into tbl_hotspot(coords, frame_id, item_id) values ('$coords','{$_SESSION['frame_id']}', '$item_id')";
//TODO: Check result was successful
    debug($insertQuery);
    $result = mysqli_query($dbc, $insertQuery) or die("Couldn't add hotspot to the database: " . mysqli_error($dbc));
}

function deleteHotspotRecord($hotspot_id, $dbc) {
//find the corresponding id for the given hotspot. delete.
    $deleteQuery = "DELETE FROM tbl_hotspot WHERE hotspot_id = $hotspot_id";
//TODO: Check result was successful
    $result = mysqli_query($dbc, $deleteQuery);
}

function editHotspotRecord($hotspot_id, $x, $y, $item_id, $dbc) {
//Find the corresponding id for the given hotspot. delete
//Concat x and y to create co-ords field
    $coords = $x . ", " . $y;

    $updateQuery = "UPDATE tbl_hotspot SET coords='$coords', item_id = $item_id WHERE hotspot_id = $hotspot_id";
    $result = mysqli_query($dbc, $updateQuery) or die("Couldn't update hotspot: " . mysqli_error($dbc));
}

//***************************** Generate left/right frame functions ************
//Get next frame
$frameQuery = "SELECT frame FROM tbl_frame WHERE room_id=" . $_SESSION['room_id']
        . " ORDER BY frame > " . $_SESSION['frame'] . " DESC, frame ASC";
$result = mysqli_query($dbc, $frameQuery) or die("Error: " . mysqli_error($dbc));
$row = mysqli_fetch_row($result);
$nextFrame = $row[0];

//Get previous frame
$frameQuery = "SELECT frame FROM tbl_frame WHERE room_id=" . $_SESSION['room_id']
        . " ORDER BY frame >= " . $_SESSION['frame'] . " ASC, frame DESC";
$result = mysqli_query($dbc, $frameQuery) or die("Error: " . mysqli_error($dbc));

$row = mysqli_fetch_row($result);
$prevFrame = $row[0];

//Insert html for room/frame selection
function generateNavigation($dbc, $nextFrame, $prevFrame) {
//Do rooms first
    echo "<div class='miniNav'>";
    echo "<form action='none'>";
//Get list of rooms from database
    $selectQuery = "SELECT room_id, name FROM tbl_room";
    $result = mysqli_query($dbc, $selectQuery) or die("Couldn't get a list of rooms: " . mysqli_error($dbc));
    //TODO Check there are actually some rooms...
    echo "<select name='roomSelector' onchange='changeRoom(this);'>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value ='{$row['room_id']}'";
        //Check for current room and mark selected
        if ($row['room_id'] == $_SESSION['room_id']) {
            echo " selected='selected' ";
        }
        echo ">{$row['name']}</option>";
    }
    echo "</select></form>";

    //Link for previous frave
    echo "<a href='";
    echo $_SERVER['PHP_SELF'];
    echo "?frame=" . $prevFrame . "'";
    echo ">Prev frame</a>";

    //Link for next frame    
    echo "<a href='";
    echo $_SERVER['PHP_SELF'];
    echo "?frame=" . $nextFrame . "'";
    echo ">Next frame</a>";


    echo "</div>";
}

////////////////////////   Form submission   /////////////////////////////
//if the create a hotspot button has been pushed, take form inputs, create new hotspot record
if (isset($_POST['createHotspot'])) {
    $x = $_POST['form_x'];
    $y = $_POST['form_y'];
    $itemID = $_POST['form_itemID'];
    createHotspotRecord($x, $y, $itemID, $dbc);
} else if (isset($_POST['deleteHotspot'])) {
    $hotspotID = $_POST['form_hotspotID'];
    deleteHotspotRecord($hotspotID, $dbc);
} else if (isset($_POST['editHotspot'])) {
    $x = $_POST['form_x'];
    $y = $_POST['form_y'];
    $itemID = $_POST['form_itemID'];
    $hotspotID = $_POST['form_hotspotID'];
    editHotspotRecord($hotspotID, $x, $y, $itemID, $dbc);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset = "utf-8" />
        <title>Olveston Historic Home</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>
        <link rel = "stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"/>
        <link type = "text/css" rel = "stylesheet" href = "css/stylesheet.css" />
        <link type = "text/css" rel = "stylesheet" href = "css/tabs.css" />
        <script type = "text/javascript" src = "scripts/getItem.js"></script>
        <script language="JavaScript">
            function point_it(event) { //On click event for the div containing the room frame
                pos_x = event.offsetX ? (event.offsetX) : event.pageX - document.getElementById("pointer_div").offsetLeft;
                pos_y = event.offsetY ? (event.offsetY) : event.pageY - document.getElementById("pointer_div").offsetTop;
                document.getElementById("cross").style.left = "" + pos_x + "px";
                document.getElementById("cross").style.top = "" + pos_y + "px";
                document.getElementById("cross").style.visibility = "visible";
                document.pointform.form_x.value = pos_x;
                document.pointform.form_y.value = pos_y;
            }
            function selectHotspot(event, $x, $y, $hotspotID, $itemID) { //On click event for the existing hotspots
                event.stopPropagation(); //prevents the room div onclick event from running when this one has triggered
                document.pointform.form_hotspotID.value = $hotspotID;
                document.pointform.form_itemID.value = $itemID;
                document.pointform.form_x.value = $x;
                document.pointform.form_y.value = $y;
                event.preventDefault(); //prevents a page reload
            }

            //////////// Used to change room ///////////
            function changeRoom(e) {
                document.location.href = window.location.pathname + "?room_id=" + e.value;
            }
        </script>
    </head>
    <body>

        <header>
            <div class="headerBack col-md-12">
                <div class="headCont col-md-12">
                </div>
            </div>

            <div class="navBar col-md-12">
                <img src="../images/links.PNG" alt="nav" >
            </div>
        </header>

        <div class="container">
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="index.php">Make Hotspot</a></li>
                <li role="presentation"><a href="createItem.php">Create Item</a></li>
                <li role="presentation"><a href="editItem.php">Edit Item</a></li>
                <?php generateNavigation($dbc, $nextFrame, $prevFrame); ?>
            </ul>

            <div class="tab-content-outter">
                <div class="tab-content-inner">
                    <div id="home" class="tab-pane fade in active">
                        <fieldset>
                            <form name="pointform" method="post" runat="server">
                                <div class="hotspotArea">	
                                    <div class="col-lg-12 margBot margTop" id="pointer_div" onclick="point_it(event)" style = "position: relative; background-image:url('../images/rooms/<?php echo $_SESSION['frame_image']; ?>');width:931px;height:400px;">
                                        <img src="../images/glassPlusPlus.png" id="cross" style="position:absolute;visibility:hidden;z-index:2;width:40px;height:40px;">
                                        <?php
                                        //draw all the hotspots from the database
                                        //TODO Where frame
                                        echo "<!-- Doing existing hotspots -->\n";
                                        $selectString = "SELECT * FROM tbl_hotspot WHERE frame_id='{$_SESSION['frame_id']}'";
                                        $result = mysqli_query($dbc, $selectString);
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            //Check it is a hotspot and split to x,y
                                            if (substr_count($row['coords'], ", ") == 1) {
                                                $hotspotID = $row['hotspot_id'];
                                                //Split string based on comma
                                                $coords = explode(", ", $row['coords']);
                                                $x = $coords[0];
                                                $y = $coords[1];
                                                $item_id = $row['item_id'];
                                                echo "<div style='width:40px; height:40px; position: absolute; top: {$y}px; left:{$x}px;'>\n";
                                                echo("<img src='../images/glass.png' width='40px' height='40px' onclick='selectHotspot(event, $x, $y, $hotspotID, $item_id)' />\n");
                                                echo "</div>\n";
                                            }
                                        }
                                        echo "<!-- End existing hotspots -->\n";
                                        ?>
                                    </div>
                                </div>
                                <div class="col-lg-5 margLeft">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <label for="itemNum">Item ID number: </label>
                                        </div>
                                        <input id="itemNum" type='text' name='form_itemID' value=''></br>
                                    </div> 
                                </div>	
                                <div class="col-lg-5 margLeft">
                                    <label class="margLeft"for="theX">You pointed on</label>
                                    <div class="form-group ">
                                        <input type='hidden' name='form_hotspotID'>
                                        <div class="col-md-2">
                                            <label for="theX">x = </label>
                                            <label for="theY">y = </label>
                                        </div>  
                                        <div class="col-md-2">
                                            <input id="theX" type='text' name='form_x' size='4' />
                                            <input id="theY" type='text' name='form_y' size='4' /></br>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12 topBtnsEditItem margTop">
                                    <div class="form-group">
                                        <input class="btn btn-primary" type='submit' name='createHotspot' value='Create hotspot'>
                                        <input class="btn btn-primary margLeft" type='submit' name='editHotspot' value='Edit hotspot'>
                                        <input class="btn btn-primary margLeft" type='submit' name='deleteHotspot' value='Delete hotspot'>
                                    </div>
                                </div>   
                            </form>
                        </fieldset>			   
                    </div>
                </div>
            </div>
            <div class="anel panel-default table margTop">
                <?php
                $selectString = "SELECT * from tbl_item ORDER BY item_id DESC";
                $result = mysqli_query($dbc, $selectString);
                echo("<table class='tableHead table-striped table-bordered table-condensed'>");
                echo("<thead><tr><th>item ID</th><th>olveston ID</th><th>item name</th><th>item description</th><th>image</th></tr></thead></table>");
                ?>
                <div class="div-table-content">
                    <table class="table table-striped table-bordered table-condensed">
                        <?php
                        echo("<tbody>");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo("<tr>");
                            foreach ($row as $index => $value) {
                                echo("<td>$value</td>");
                            }
                            echo("</tr>");
                        }
                        echo("<tbody></table>");
                        ?>
                </div>
            </div>
        </div>
        <div id="footer">
            <div class="footInfo">
            </div>
        </div>
        <script language="JavaScript">
            function selectItem($itemID, $itemName, $itemDescription, $itemImage) {
                document.pointform.form_itemID.value = $itemID;
                document.pointform.form_itemName.value = $itemName;
                document.pointform.form_itemDescription.value = $itemDescription;
                document.pointform.form_itemImage.value = $itemImage;
            }
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </body>
</html>