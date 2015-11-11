<?php
require_once("../scripts/connectvars.php");
//Connect to database
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

//TODO room and frame selection
//frame is from tbl_frame...
$frame_id = 1; //Room 1 frame 1
////////////////////////   Functions   /////////////////////////////

function debug($value) {
    echo "<pre class='debug'>";
    print_r($value);
    echo "</pre>";
}

function createHotspotRecord($x, $y, $item_id, $frame_id, $dbc) {
    //Concat x and y to create co-ords field
    $coords = $x . ", " . $y;
    $insertQuery = "INSERT into tbl_hotspot(coords, frame_id, item_id) values ('$coords','$frame_id', '$item_id')";
    debug($insertQuery);
    //TODO: Check result was successful
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

    $updateQuery = "UPDATE tbl_hotspot SET coords=$coords, item_id = $item_id WHERE hotspot_id = $hotspot_id";
    $result = mysqli_query($dbc, $updateQuery);
}

////////////////////////   Form submission   /////////////////////////////
//if the create a hotspot button has been pushed, take form inputs, create new hotspot record
if (isset($_POST['createHotspot'])) {
    $x = $_POST['form_x'];
    $y = $_POST['form_y'];
    $itemID = $_POST['form_itemID'];
    createHotspotRecord($x, $y, $itemID, $frame_id, $dbc);
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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"/>
        <link type = "text/css" rel = "stylesheet" href = "css/stylesheet.css" />
        <link type = "text/css" rel = "stylesheet" href = "css/tabs.css" />
        <script type="text/javascript" src="scripts/getItem.js"></script>
        <script language="JavaScript">
            function point_it(event) { //On click event for the div containing the room frame
                pos_x = event.offsetX ? (event.offsetX) : event.pageX - document.getElementById("pointer_div").offsetLeft;
                pos_y = event.offsetY ? (event.offsetY) : event.pageY - document.getElementById("pointer_div").offsetTop;
                document.getElementById("cross").style.left = ""+pos_x+"px";
                document.getElementById("cross").style.top = ""+pos_y+"px";
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
            </ul>

            <div class="tab-content-outter">
                <div class="tab-content-inner">
                    <div class="hotspotArea">
                        <div id="home" class="tab-pane fade in active">
                            <?php
                            //Get image based off frame_id
                            $selectQuery = "SELECT image FROm tbl_frame WHERE frame_id='$frame_id'";
                            $result = mysqli_query($dbc, $selectQuery) or die("Couldn't get image for this frame: " . mysqli_error($dbc));
                            $xxx = mysqli_fetch_assoc($result);
                            $image = $xxx['image'];
                            ?>
                            <div id="pointer_div" onclick="point_it(event)" style = "position: relative; background-image:url('../images/rooms/<?php echo $image; ?>');width:931px;height:400px;">
                                <img src="../images/glassPlusPlus.png" id="cross" style="position:absolute;visibility:hidden;z-index:2;width:40px;height:40px;">
                                <?php
                                //draw all the hotspots from the database
                                //TODO Where frame
                                echo "<!-- Doing existing hotspots -->\n";
                                $selectString = "SELECT * FROM tbl_hotspot WHERE frame_id='$frame_id'";
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

                                //input fields required for a new hotspot. x and y are generated. itemID needs to be entered by user.-->
                                echo("</div>
                                    <fieldset>
                                    <form name='pointform' method='post'>
                                            <div class='marg col-md-5'>
                                                    <input type='hidden' name='form_hotspotID'>
                                                    </br>You pointed on x = <input type='text' name='form_x' size='4' /> - y = <input type='text' name='form_y' size='4' /></br>
                                                    </br>Item ID number:<input type='text' name='form_itemID' value=''></br>
                                            </div>
                                            <div class='marg col-md-5'>
                                                    </br>
                                                    <input type='submit' name='createHotspot' value='Create hotspot'>
                                                    <input type='submit' name='editHotspot' value='Edit hotspot'>
                                                    <input type='submit' name='deleteHotspot' value='Delete hotspot'>
                                            </div>
                                    </form> 	
                                    </fieldset>");
                                ?>
                            </div>
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