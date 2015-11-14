<!--
File:    index.php
Purpose: n/a
Bugs:    n/a
Author:  Arron Dick(dickaj1)
Date:    8/10/2015 - 08:27:29 AM
-->
<?php
require_once("scripts/header.php");

require_once("scripts/navigation.php");

require_once("scripts/hotspots.php");
?>

<div id="content">
    <div id="room">
        <?php
        //Place frame image
        echo "<img src='images/rooms/";
        echo $_SESSION['frame_image'];
        echo "' alt='";
        echo $_SESSION['room_name'] . "'";
        echo " usemap='#roomimagemap' id='roomImg'>";

        //Place map and hotspots
        getHotspots($dbc);
        ?>

    </div><!-- /room -->  

    <span class="black-arrow left gallery-left" onclick="prevFrame()"></span>
    <span class="black-arrow right gallery-right" onClick="nextFrame()"></span>

</div><!-- /content -->

		<div class="container">
            <ul class="nav nav-tabs">
                <li role="presentation"><a href="index.php">Make Hotspot</a></li>
                <li role="presentation"><a href="createItem.php">Create Item</a></li>
                <li role="presentation" class="active"><a href="editItem.php">Edit Item</a></li>
            </ul>
			<div class="tab-content tab-content-outter">
                <div class="tab-content tab-content-inner">
					<div class="col-lg-6 margTop">
						<div class="col-md-3">
							<ul>
								<?php
								getFrames($dbc);
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>	 
		</div>	

<div class=tabOuter>
    <div class=tabs>
        <div id=tab1> <a href="#tab1">Sections</a>
            <div id="outer">
                <div class="wrap">
                    <ul>
                        <?php
                        getFrames($dbc);
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div id=tab2> <a href="#tab2">Rooms</a>
            <div id="outer">
                <div class="wrap">
                    <ul>
                        <?php
                        getRooms($dbc);
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- used for dimming the background -->
<div id="overlay"></div>
<!-- div for the item pop window -->
<div id="ajaxDiv"></div>

<?php
require_once("scripts/footer.php");
?>