<!--
File: createTables.php
Purpose: n/a
Bugs: n/a
Notes:  Room->Frame->Item->Hotspot
        use this order to avoid needing to lock tables
Author: Arron Dick(dickaj1)
Date: 24/10/2015 - 4:31:57 PM
-->
<!DOCTYPE html>
<html>
    <head>
        <title>Create tables</title>
    </head>
    <body>
        <?php

        function do_queries($queries, $dbc) {
            foreach ($queries as $query) {
                echo "$query...<br/>";
                $result = mysqli_query($dbc, $query) or die("Couldn't add informtation to database: " . mysqli_error($dbc));
                echo "done...<br/><br/>";
            }
        }

        //Get connection parameters
        require_once("scripts/connectvars.php");

        //Connect to database
        //Skip selecting schema by default, as it may not exist yet!(First run)
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Couldn't connect to server: " . mysqli_error());

        //Database schema name
        $schema = DB_NAME;

        //Array of queries to execute
        $queries = array();

        //Drop old database
        array_push($queries, "DROP SCHEMA IF EXISTS `$schema`");

        //Create schema
        array_push($queries, "CREATE SCHEMA IF NOT EXISTS `$schema`");

        //Use schema
        array_push($queries, "USE `$schema`");

        //Create tbl_room
        array_push($queries, "CREATE TABLE `tbl_room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text,
  `image` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `room_id_UNIQUE` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;");

        //Create tbl_frame
        array_push($queries, "CREATE TABLE `tbl_frame` (
  `frame_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `frame` int(11) NOT NULL,
  `image` varchar(45) NOT NULL,
  PRIMARY KEY (`frame_id`,`frame`),
  UNIQUE KEY `frame_id_UNIQUE` (`frame_id`),
  KEY `room_frame_idx` (`room_id`),
  CONSTRAINT `room_frame` FOREIGN KEY (`room_id`) REFERENCES `tbl_room` (`room_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

        //Create tbl_item
        array_push($queries, "CREATE TABLE `tbl_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text,
  `image` varchar(45) NULL DEFAULT 'blank.png',
  `olveston_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_id_UNIQUE` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

        //Create tbl_hotspot
        array_push($queries, "CREATE TABLE `tbl_hotspot` (
  `hotspot_id` int(11) NOT NULL AUTO_INCREMENT,
  `coords` text NOT NULL,
  `frame_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`hotspot_id`),
  UNIQUE KEY `hotspot_id_UNIQUE` (`hotspot_id`),
  KEY `hotspot_item_idx` (`item_id`),
  KEY `frame_hotspot_idx` (`frame_id`),
  CONSTRAINT `frame_hotspot` FOREIGN KEY (`frame_id`) REFERENCES `tbl_frame` (`frame_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `hotspot_item` FOREIGN KEY (`item_id`) REFERENCES `tbl_item` (`item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");



///////////////////////////////////////////////////////////////////////////////
//Stuff example data into the database
        //tbl_room
        array_push($queries, "INSERT INTO `tbl_room` VALUES "
                . "(1,'Billiards Room','The Billiards Room','billiards00.png')");

        //tbl_frame
        array_push($queries, "INSERT INTO `tbl_frame` VALUES "
                . "(1,1,1,'billiards01.png'),"
                . "(2,1,2,'billiards02.png'),"
                . "(3,1,3,'billiards03.png')");

        do_queries($queries, $dbc);
        echo "All queries finished";
        ?>
    </body>
</html>