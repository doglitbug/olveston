<?php
require_once("../scripts/connectvars.php");
//Connect to database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

//Set up variables for sticky form(used in search)
$itemID = $itemName = $itemDescription = $itemOlveston_id = "";
$itemImage = "blank.png";

if (isset($_POST['deleteItem'])) {
    $itemID = $_POST['form_itemID'];
    deleteItemRecord($itemID, $connection);
    //TODO Confirmation
} else if (isset($_POST['editItem'])) {
    $item_id = $_POST['form_itemID'];
    $olveston_id = $_POST['form_olvestonID'];
    $name = $_POST['form_itemName'];
    $description = $_POST['form_itemDescription'];

    //Get existing item image
    $selectQuery = "SELECT image FROM tbl_item WHERE item_id='$item_id'";
    $result = mysqli_query($connection, $selectQuery) or die("Couldn't find image filename: " . mysqli_error($connection));
    $row = mysqli_fetch_assoc($result);
    $newImage=$image = $row['image'];

    //Check to see if a new image has been uploaded
    if ($_FILES['form_uploadImage']['error'] == 0) {
        //Upload and move new image
        $newImage = $_FILES['form_uploadImage']['name'];

        //Copy file from temporary location to permanant location
        //Using copy instead of move so that file permission are scrubbed...
        //TODO Check file doesn't already exist, or let overwrite?
        copy($_FILES['form_uploadImage']['tmp_name'], "../images/items/" . $newImage);
    }

    editItemRecord($item_id, $olveston_id, $name, $description, $newImage, $connection);
    //If the image has changed, delete the old one
    if($newImage!=$image){
        deleteImage($connection,$image);
    }
} else if (isset($_POST['searchItem'])) {
    $itemID = $_POST['form_itemID'];
    $test = searchItemRecord($itemID, $connection);
    $itemOlveston_id = $test['olveston_id'];
    $itemName = $test['name'];
    $itemDescription = $test['description'];
    $itemImage = $test['image'];
}

function deleteImage($dbc, $imageName) {
    //Delete an image only if no items are using it. This ideally should never happen as all items should have unique images
    $selectQuery = "SELECT * from tbl_item WHERE image='$imageName'";
    $result = mysqli_query($dbc, $selectQuery) or die("Couldn't search database for image: " . mysqli_error($dbc));
    print_r($imageName);
    print_r($result);
    //Check its unused
    if (mysqli_num_rows($result) == 0) {
        unlink("../images/items/" . $imageName);
    }
}

function createItemRecord($itemName, $olvestonID, $itemDescription, $itemImage, $connection) {
    $insertQuery = "INSERT into tbl_item(name, olveston_id, description, image) values ('$itemName', '$olvestonID', '$itemDescription', '$itemImage')";
    $result = mysqli_query($connection, $insertQuery);
}

function deleteItemRecord($itemID, $connection) {
    //Delete any linked hotspots.
    //The SQL server should take care of this due to cascade anyway.
    $deleteQuery = "DELETE FROM tbl_hotspot WHERE item_id = $itemID";
    $result = mysqli_query($connection, $deleteQuery);

    //Get the item image, so that we can delete it from the server if need be
    $selectQuery = "SELECT image from tbl_item WHERE item_id=$itemID";
    $result = mysqli_query($connection, $selectQuery) or die("Couldn't access database to find item: " . mysqli_error($connection));
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $image=$row['image'];
    }

    $deleteQuery = "DELETE FROM tbl_item WHERE item_id = $itemID";
    $result = mysqli_query($connection, $deleteQuery);
    //Flick off to deleteImage to check it can be removed
    deleteImage($connection,$image);
}

function editItemRecord($itemID, $olveston_id, $name, $description, $image, $connection) {
    //find the corresponding id for the given hotspot. delete.
    $updateQuery = "UPDATE tbl_item SET name = '$name', olveston_id = '$olveston_id', description = '$description', image = '$image' WHERE item_id = $itemID";
    $result = mysqli_query($connection, $updateQuery) or die("Couldn't edit item: " . mysqli_error($connection));
}

function searchItemRecord($itemID, $connection) {
    $selectString = "SELECT * from tbl_item WHERE item_id = $itemID";
    $result = mysqli_query($connection, $selectString);
    $row = mysqli_fetch_assoc($result);
    return $row;
    //return the row with the given itemID
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
                <li role="presentation"><a href="index.php">Make Hotspot</a></li>
                <li role="presentation"><a href="createItem.php">Create Item</a></li>
                <li role="presentation" class="active"><a href="editItem.php">Edit Item</a></li>
            </ul>

            <div class="tab-content tab-content-outter">
                <div class="tab-content tab-content-inner">
                    <fieldset>
                        <form enctype="multipart/form-data" name="pointform" method="post" runat="server">
                            <div class="col-lg-6 margTop">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="control-label"  for="objectID">Object ID:</label>
                                    </div>
                                    <input type='text' id="objectID" type='text' name='form_itemID' value='<?php echo $itemID; ?>'>
                                    <input type='submit' name='searchItem' value='Search'></br>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="control-label"  for="olvestonID">Olveston ID:</label>
                                    </div> 
                                    <input type='text' id="olvestonID" type='text' name='form_olvestonID' value='<?php echo $itemOlveston_id; ?>'></br>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="control-label" for="objectName">Object name:</label>
                                    </div>
                                    <input type='text' id="objectName" type='text' name='form_itemName'  value='<?php echo $itemName; ?>'></br>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-5">
                                        <label for="comment">Object Description:</label>
                                    </div>
                                    <textarea class="form-control" rows="5" id="comment" type='text' name='form_itemDescription'><?php echo $itemDescription; ?></textarea></br>
                                </div>
                            </div>
                            <div class="paddLeft margTop col-lg-6">
                                <div class="form-group"> 
                                    <label class="control-label" for="form_uploadImage">Upload an image:</label>
                                    <input type='file' id="form_uploadImage" name="form_uploadImage" onchange="readURL(this);" />
                                    <img id="blah" src="../images/items/<?php echo $itemImage; ?>" alt="../images/items/blank.png" width="250" height="250" />
                                </div>
                            </div>
                            <div class="col-lg-12 topBtnsEditItem">
                                <div class="form-group">
                                    <input class="btn btn-primary" type='submit' name='editItem' value='Edit Object'>
                                    <input class="btn btn-primary margLeft" type='submit' name='deleteItem' value='Delete Object'>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
            <div class="anel panel-default table margTop">
                <?php
                $selectString = "SELECT * from tbl_item ORDER BY item_id DESC";
                $result = mysqli_query($connection, $selectString);
                echo("<table class='tableHead table-striped table-bordered table-condensed'>");
                echo("<thead><tr><th>item ID</th><th>item name</th><th>item description</th><th>image</th><th>olveston ID</th></tr></thead></table>");
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

        <script type="text/javascript">
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var d = document.getElementById('filename');
                    d.value = input.value;

                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#blah').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </body>
</html>
