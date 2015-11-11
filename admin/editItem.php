<?php
require_once("../scripts/connectvars.php");
//Connect to database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");
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
                <img src="images/links.PNG" alt="nav" >
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
                        <form name="pointform" method="post" runat="server">
                            <div class="col-md-6">
                                <label class="control-label"  for="objectID">Object ID:</label>
                                <input type='text' id="objectID" type='text' name='form_itemID'>
                                <input type='submit' name='searchItem' value='Search for this Object ID'></br>
                                <label class="control-label" for="objectName">Object name:</label>
                                <input type='text' id="objectName" type='text' name='form_itemName'></br>
                                <label for="comment">Object Description:</label>
                                <textarea class="form-control" rows="5" id="comment" type='text' name='form_newItemDescription'></textarea></br>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label" for="form_itemImage">Upload an image:</label>
                                <input type='file' name="form_itemImage" onchange="readURL(this);" />
                                <img id="blah" src="images/blank.png" alt="images/blank.png" width="250" height="250" />
                            </div>
                            <div class="col-md-12">
                                <input type='submit' name='editItem' value='Edit Object'>
                                <input type='submit' name='deleteItem' value='Delete Object'>
                            </div>
                        </form>
                    </fieldset>	
                    <?php
                    //if the create an item button has been pushed, take form inputs, create new item record
                    if (isset($_POST['createItem'])) {
                        //current tab = 2

                        $itemName = $_POST['form_newItemName'];
                        $itemDescription = $_POST['form_newItemDescription'];
                        $itemImage = $_POST['form_newItemImage'];
                        createItemRecord($itemName, $itemDescription, $itemImage, $connection);
                    } else if (isset($_POST['deleteItem'])) {

                        $itemID = $_POST['form_itemID'];
                        deleteItemRecord($itemID, $connection);
                    } else if (isset($_POST['editItem'])) {
                        $itemID = $_POST['form_itemID'];
                        $name = $_POST['form_itemName'];
                        $description = $_POST['form_itemDescription'];
                        $image = $_POST['form_itemImage'];
                        editItemRecord($itemID, $name, $description, $image, $connection);
                    } else if (isset($_POST['searchItem'])) {
                        $itemID = $_POST['form_itemID'];
                        $test = searchItemRecord($itemID, $connection);
                        $itemName = $test['name'];
                        $itemDescription = $test['description'];
                        $itemImage = $test['image'];
                        echo("<script> selectItem('$itemID', '$itemName', '$itemDescription', '$itemImage'); </script>");
                    }

                    function createItemRecord($itemName, $itemDescription, $itemImage, $connection) {
                        $insertQuery = "INSERT into tbl_item(name, description, image) values ('$itemName','$itemDescription', '$itemImage')";
                        $result = mysqli_query($connection, $insertQuery);
                    }

                    function deleteItemRecord($itemID, $connection) {
                        //find the corresponding id for the given hotspot. delete.
                        $deleteQuery = "DELETE FROM tbl_hotspot WHERE item_id = $itemID";
                        $result = mysqli_query($connection, $deleteQuery);

                        $deleteQuery = "DELETE FROM tbl_item WHERE item_id = $itemID";
                        $result = mysqli_query($connection, $deleteQuery);
                    }

                    function editItemRecord($itemID, $name, $description, $image, $connection) {
                        //find the corresponding id for the given hotspot. delete.
                        $updateQuery = "UPDATE tbl_item SET name = '$name', description = '$description', image = '$image' WHERE item_id = $itemID";
                        $result = mysqli_query($connection, $updateQuery);
                    }

                    function searchItemRecord($itemID, $connection) {
                        $selectString = "SELECT * from tbl_item WHERE item_id = $itemID";
                        $result = mysqli_query($connection, $selectString);
                        $row = mysqli_fetch_assoc($result);
                        return $row;
                        //return the row with the given itemID
                    }
                    ?>

                </div>
            </div>
            <div class="anel panel-default table margTop">
                    <?php
                    $selectString = "SELECT * from tbl_item ORDER BY item_id DESC";
                    $result = mysqli_query($connection, $selectString);
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
        <script type="text/javascript">
            function readURL(input) {
                if (input.files && input.files[0]) {
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
