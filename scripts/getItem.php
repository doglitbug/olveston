<?php
//Connect to database
require_once("connectvars.php");
//Connect to database
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

$item = $_REQUEST['item_id'];

//Get item details
$itemQuery = "SELECT name,image,tbl_item.description FROM tbl_item WHERE item_id='$item'";

//TODO do something with olveston_id
//Check item exists
$result = mysqli_query($dbc, $itemQuery) or die("Error:" . mysqli_error($dbc));
if (mysqli_num_rows($result) == 0) {
    echo "Item not found";
//TODO: Deal with this error gracefully
} else {
//Grab frame details
    $item_details = mysqli_fetch_assoc($result);
    $name = $item_details['name'];
    $description = $item_details['description'];
    $image = $item_details['image'];
}

echo "<h2>$name</h2>";
echo "<div class='col-lg-12'><img src='images/items/$image' alt='$name' class='itemImg'></div>";
echo "<div class='col-lg-12 popupMarg'><p>";
echo nl2br($description);
echo "</p></div>";
?>