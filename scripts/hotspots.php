<?php

function getHotspots($dbc) {
    //Get hot spots for this frame_id
    //TODO Change this to find hotspots based on room_id and frame
    //Instead of frame_id?
    $hotspotQuery = "SELECT coords,item_id,tbl_item.name,tbl_item.description "
            . "FROM tbl_hotspot JOIN tbl_item USING (item_id) "
            . "WHERE frame_id=" . $_SESSION['frame_id'];

    $result = mysqli_query($dbc, $hotspotQuery) or die("Error: " . mysqli_error($dbc));

    //Don't bother with a map of there is no hotspots in this frame/room combo
    if (mysqli_num_rows($result) > 0) {
        echo "<map id='roomimagemap' name='roomimagemap'>\n";

        while ($row = mysqli_fetch_array($result)) {
            //Check for map type
            $count = substr_count($row['coords'], ',');
            $type = "";

            switch ($count) {
                case 2://Circle
                    $type = "circle";
                    break;
                case 3: //Rectangle
                    $type = "rect";
                    break;
                default:
                    $type = "poly";
                    break;
            }

            $coordsStr = $row['coords'];
            $name = $row['name'];
            $item_id = $row['item_id'];

            echo "<area shape='$type' coords='$coordsStr' title='$name' alt='$name' href='javascript:getItem($item_id)' target='_self'>\n";
        }
        echo "</map>";
    }

    //reset result
     $hotspotQuery = "SELECT coords,item_id,tbl_item.name AS name,tbl_item.description "
            . "FROM tbl_hotspot JOIN tbl_item USING (item_id) "
            . "WHERE frame_id=" . $_SESSION['frame_id'];

    $result = mysqli_query($dbc, $hotspotQuery) or die("Error: " . mysqli_error($dbc));
    //Check for 2 number hot spots
    if (mysqli_num_rows($result)>0) {
        echo "<!-- Start 2 number hotspots -->\n";
        //Check each hotspot
        while ($row = mysqli_fetch_array($result)) {
            //Check it only has 2 numbers
            if (substr_count($row['coords'], ", ") == 1) {
                //Get information on hotspot
                $item_id=$row['item_id'];
                $name=$row['name'];
                
                //Split coords into x and y
                $coords = explode(", ", $row['coords']);

                $x = $coords[0];
                $y = $coords[1];

                echo "<div style='width:40px; height:40px; position: absolute; top: {$y}px; left:{$x}px;'>\n";

                echo("<img src='images/glass.png' width='40px' title='$name' height='40px' onclick='javascript:getItem($item_id)' />\n");

                echo "</div>\n";
            }
        }
        echo "<!-- End 2 number hotspots -->\n";
    }
}

?>