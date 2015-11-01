<?php

function getFrames($dbc) {
    //Get the stuff for frame thumbnails
    $room_id =  $_SESSION['room_id'];
    $frameQuery = "SELECT * FROM tbl_frame WHERE room_id='$room_id' ORDER BY frame ASC";

    $result = mysqli_query($dbc, $frameQuery) or die("Error: " . mysqli_error($dbc));


    while ($row = mysqli_fetch_array($result)) {
        $image = $row['image'];
        $frame = $row['frame'];
        $location=$_SERVER['PHP_SELF']."?frame=$frame";
        
        echo "<li><a href='$location'><img class='thumb' src='images/rooms/$image' alt=''/></a></li>\n";
    }
}

function getRooms($dbc){
    //Get the stuff for room thumbnails
    //TODO switch to room thumbs once these are created
    //Get the stuff for frame thumbnails
    $roomsQuery = "SELECT * FROM tbl_room ORDER BY name ASC";

    $result = mysqli_query($dbc, $roomsQuery) or die("Error: " . mysqli_error($dbc));


    while ($row = mysqli_fetch_array($result)) {
        $room_id = $row['room_id'];
        $name = $row['name'];
        $desc=$row['desc'];
        $image=$row['image'];
        
        $location=$_SERVER['PHP_SELF']."?room_id=$room_id";
        
        echo "<li><a href='$location'><img class='thumb' src='images/rooms/$image' alt='$desc'/></a></li>\n";
    }
}
?>