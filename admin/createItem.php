<?php
require_once("../scripts/connectvars.php");
//Connect to database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database");

////////////////// Data submission /////////////////////
//if the create an item button has been pushed, take form inputs, create new item record
if (isset($_POST['createItem'])) {
    $itemName = mysqli_real_escape_string($connection, trim($_POST['form_newItemName']));
    $itemDescription = mysqli_real_escape_string($connection, trim($_POST['form_newItemDescription']));
    $olveston_id = mysqli_real_escape_string($connection, trim($_POST['form_olvestonID']));
    //TODO Check all form data is valid..
    //Check file was successful
    if ($_FILES['form_newItemImage']['error'] == 0) {
        //TODO Check file size is reasonable...
        //TODO Check there isn't a file name conflict with an existing image
        //Grab file name
        $itemImage = $_FILES['form_newItemImage']['name'];

        //Copy file from temporary location to permanant location
        //Using copy instead of move so that file permission are scrubbed...
        copy($_FILES['form_newItemImage']['tmp_name'], "../images/items/" . $itemImage);
    }

    $insertQuery = "INSERT into tbl_item(name, description, image, olveston_id) values ('$itemName','$itemDescription', '$itemImage','$olveston_id')";
    $result = mysqli_query($connection, $insertQuery) or die("Couldn't create new item: " . die($connection));
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

        </header>

        <div class="container">
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="index.php">Make Hotspot</a></li>
                <li role="presentation" class="active"><a href="createItem.php">Create Item</a></li>
                <li role="presentation"><a href="editItem.php">Edit Item</a></li>
            </ul>

            <div class="tab-content tab-content-outter">
                <div class="tab-content tab-content-inner">
                    <fieldset>
                        <form enctype="multipart/form-data" name="pointform" method="post" runat="server">
                            <div class="col-lg-6 margTop">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="control-label"  for="olvestonID">Olveston ID:</label>
                                    </div> 
                                    <input type='text' id="olvestonID" type='text' name='form_olvestonID'></br>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label class="control-label"  for="objectName">Object name:</label>
                                    </div>
                                    <input type="text" id="objectName" type='text' name='form_newItemName' placeholder="" class="input-xlarge input-mysize"></br>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label for="comment">Object Description:</label>
                                    </div>
                                    <textarea class="form-control" rows="5" id="comment" type='text' name='form_newItemDescription'></textarea></br>
                                </div>
                            </div>
                            <div class="paddLeft margTop col-lg-6">
                                <div class="form-group">
                                    <label class="control-label" for="form_newItemImage">Upload an image:</label>
                                    <input type='file' id="form_newItemImage" name="form_newItemImage" onchange="readURL(this);" />

                                    <img id="blah" src="../images/items/blank.png" alt="../images/items/blank.png" width="250" height="250" />
                                </div>
                            </div>
                            <div class="col-lg-12 topBtnsEditItem">
                                <div class="form-group">
                                    <input class="btn btn-primary" type='submit' name='createItem' value='Create Object'>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
            <div class="panel panel-default table margTop">
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
