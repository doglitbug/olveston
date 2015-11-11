<?php
	include 'connect.inc.php';
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
			function point_it(event){ //On click event for the div containing the room frame
				pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById("pointer_div").offsetLeft;
				pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById("pointer_div").offsetTop;
				document.getElementById("cross").style.left = (pos_x) ;
				document.getElementById("cross").style.top = (pos_y) ;
				document.getElementById("cross").style.visibility = "visible" ;
				document.pointform.form_x.value = pos_x;
				document.pointform.form_y.value = pos_y;
			}
			function selectHotspot(event, $x, $y, $hotspotID, $itemID){ //On click event for the existing hotspots
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
				<img src="images/links.PNG" alt="nav" >
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
								<div id="pointer_div" onclick="point_it(event)" style = "position: relative; background-image:url('images/room_billiards.png');width:931px;height:400px;">
									<img src="images/glassPlusPlus.png" id="cross" style="position:absolute;visibility:hidden;z-index:2;width:40px;height:40px;">
									<?php
										//if the create a hotspot button has been pushed, take form inputs, create new hotspot record
										if (isset($_POST['createHotspot']))
										{	$x = $_POST['form_x'];
											$y = $_POST['form_y'];
											$itemID = $_POST['form_itemID'];
											$frameID = 1;
											createHotspotRecord($x, $y, $itemID, $frameID, $connection);
										}
										
										else if(isset($_POST['deleteHotspot']))
										{
											$hotspotID = $_POST['form_hotspotID'];
											deleteHotspotRecord($hotspotID, $connection);
										}
										
										else if(isset($_POST['editHotspot']))
										{
											$x = $_POST['form_x'];
											$y = $_POST['form_y'];
											$itemID = $_POST['form_itemID'];
											$hotspotID = $_POST['form_hotspotID'];
											editHotspotRecord($hotspotID, $x, $y, $itemID, $connection);
										}
										
										//draw all the hotspots from the database
										$selectString = "SELECT * FROM tbl_hotspot";
										$result = mysqli_query($connection, $selectString);
											while ($row = mysqli_fetch_assoc($result)) 
											{
												$hotspotID = $row['hotspot_id'];
												$x = $row['x'];
												$y = $row['y'];
												$itemID = $row['item_id'];
												echo("<input type='image' src='images/glass.png' onclick='selectHotspot(event, $x, $y, $hotspotID, $itemID)' style='position: absolute;width:40px; height:40px; left:$x; top: $y;'/>");
											}
											
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
									
									
									function createHotspotRecord($x, $y, $itemID, $frameID, $connection){	
										$insertQuery = "INSERT into tbl_hotspot(x, y, item_id, frame_id) values ('$x','$y','$itemID', '$frameID')";	
										$result = mysqli_query($connection, $insertQuery);
									}
									
									function deleteHotspotRecord($hotspotID, $connection)
									{
										//find the corresponding id for the given hotspot. delete.
										$deleteQuery = "DELETE FROM tbl_hotspot WHERE hotspot_id = $hotspotID";	
										$result = mysqli_query($connection, $deleteQuery);
									}
									
									function editHotspotRecord($hotspotID, $x, $y, $itemID, $connection)
									{
										//find the corresponding id for the given hotspot. delete.
										$updateQuery = "UPDATE tbl_hotspot SET x = $x, y = $y, item_id = $itemID WHERE hotspot_id = $hotspotID";	
										$result = mysqli_query($connection, $updateQuery);
									}		
							?>
							</div>
						</div>
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
							while ($row = mysqli_fetch_assoc($result))
							{
								echo("<tr>");
								foreach($row as $index=>$value)
								{
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
		function selectItem($itemID, $itemName, $itemDescription, $itemImage){
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
