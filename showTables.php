<!--
File: showTables.php
Purpose: n/a
Bugs: n/a
Author: Arron Dick(dickaj1)
Date: 24/10/2015 - 4:45:36 PM
-->
<!DOCTYPE html>
<html>
    <head>
        <title>Show tables</title>
        <style>
            body {
                background-color: linen;
            }

            table,td,th {
                border: 1px solid #ccc;
            }

            th {
                font-weight: bold;
            }

        </style>
    </head>
    <body>

        <?php
        require_once("scripts/connectvars.php");

        //Attempt connection to database
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Couldn't connect to server: " . mysqli_error());

        $query = "SHOW tables";

        $result = mysqli_query($dbc, $query) or die("Couldn't get list of tables: ") . mysqli_error($dbc);

        while ($table = mysqli_fetch_array($result)) {
            echo("<h1>$table[0]</h1>"); //Print table name
            //Get table structure
            echo "<h3>Table structure:</h3>";
            echo "<table><tr><th>Field name</th><th>Field type</th></tr>";
            $describe = mysqli_query($dbc, "DESCRIBE " . $table[0]) or die("Couldn't get description of table: ") . mysqli_error($dbc);

            while ($row = mysqli_fetch_array($describe)) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
            }
            echo "</table>";

            //Get table contents
            echo "<h3>Table contents</h3>";
            //Do table header
            echo "<table><tr>";
            //Go back to the start
            mysqli_data_seek($describe, 0);
            //Create table header
            while ($row = mysqli_fetch_array($describe)) {
                echo "<th>{$row['Field']}</th>";
            }
            echo "</tr>";

            $contents = mysqli_query($dbc, "SELECT * FROM $table[0]") or die("Couldn't get table contents: ") . mysqli_error($dbc);
            while ($row = mysqli_fetch_row($contents)) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </body>
</html>