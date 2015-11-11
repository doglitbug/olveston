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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;");

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;");

        //Create tbl_item
        array_push($queries, "CREATE TABLE `tbl_item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` text,
  `image` varchar(45) NULL DEFAULT 'blank.png',
  `olveston_id` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_id_UNIQUE` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;");

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;");



///////////////////////////////////////////////////////////////////////////////
//Stuff example data into the database
        //tbl_room
        array_push($queries, "INSERT INTO `tbl_room` VALUES "
                . "(1,'Billiards Room','The Billiards Room','billiards00.png'),"
                . "(2,'Arrons Kitchen','Yup its a mess','kitchen00.png')");

        //tbl_frame
        array_push($queries, "INSERT INTO `tbl_frame` VALUES "
                . "(1,1,1,'billiards01.png'),"
                . "(4,1,2,'billiards02.png'),"
                . "(6,2,1,'kitchen01.png'),"
                . "(7,2,2,'kitchen02.png'),"
                . "(8,2,3,'kitchen03.png'),"
                . "(9,2,4,'kitchen04.png'),"
                . "(10,2,5,'kitchen05.png'),"
                . "(11,2,6,'kitchen06.png'),"
                . "(12,2,7,'kitchen07.png'),"
                . "(13,2,8,'kitchen08.png'),"
                . "(14,1,3,'billiards03.png')");

        //tbl_item
        array_push($queries, "INSERT INTO `tbl_item` VALUES "
                . "(1,'Samuels apron','Its red and has a frog on it, I was bored and added googly eyes','apron.jpg',NULL),"
                . "(2,'Light switch','Its a lightswitch yo','switch.jpg',NULL),"
                . "(7,'Majong','A majong set','blank.png','5774'),"
                . "(8,'Picture','Large picture','blank.png','1'),"
                . "(9,'Chair','A fancy chair','blank.png','5825'),"
                . "(10,'Urn','Large urn','blank.png','5758')");

        //tbl_hotspot
        array_push($queries, "INSERT INTO `tbl_hotspot` VALUES "
                . "(1,'234,0,356,371',6,1),"
                . "(2,'922,343,955,401',6,2),"
                . "(3,'339, 228, 364, 254',1,7),"
                . "(4,'632, 39, 693, 128',1,8),"
                . "(5,'548, 213, 548, 324, 599, 341, 635, 312, 630, 236, 598, 227, 591, 203',4,9),"
                . "(6,'346, 244, 348, 398, 479, 399, 475, 285, 440, 267, 437, 235',4,9),"
                . "(7,'83, 174, 12',14,10)");

        do_queries($queries, $dbc);
        echo "All queries finished";
        ?>
    </body>
</html>