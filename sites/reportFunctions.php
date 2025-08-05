<?php
    /**
     * Gathers asset tag and serial information
     * from an SQL call and displays the information
     * in a table with a link to the asset
     */
    function getTagSerial($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg ." (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Serial</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row['serial'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
	    $result -> free_result();
    }

    /**
     * Gathers asset tag and user 
     * information (LDAP name, First and Last)
     * from an SQL call and displays the
     * information in a table and link
     * to the asset in inventory
     */
    function getTagUserAsset($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg . " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Username/99#</td><td id = 'tableElement'>First Name</td><td id = 'tableElement'>Last Name</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row['username'] ."</td><td id = 'tableElement'>". $row['first_name'] ."</td><td id = 'tableElement'>". $row['last_name'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();
    }

    /**
     * Gathers asset tag and user 
     * information (LDAP name, First and Last)
     * from an SQL call and displays the
     * information in a table and link
     * to the user in inventory
     */
    function getTagUser($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg . " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Amount of Assets</td><td id = 'tableElement'>Username/99#</td><td id = 'tableElement'>First Name</td><td id = 'tableElement'>Last Name</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['num'] ."</td><td id = 'tableElement'>". $row['username'] ."</td><td id = 'tableElement'>". $row['first_name'] ."</td><td id = 'tableElement'>". $row['last_name'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users/" . $row['assigned_to'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();
    }

    /**
     * Gathers asset tag and asset name 
     * information from an SQL call and 
     * displays the information in a table
     * with a link to the asset
     */
    function getTagName($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg . " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Serial</td><td id = 'tableElement'>Asset Name</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row['serial'] ."</td><td id = 'tableElement'>". $row['name'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();
    }

    /**
     * Gathers asset tag and status 
     * information from an SQL call and 
     * displays the information in a table
     * with a link to the asset
     */
    function getTagStatus($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg . " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Serial</td><td id = 'tableElement'>Location</td><td id = 'tableElement'>Status</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row['serial'] ."</td><td id = 'tableElement'>". $row['name'] ."</td><td id = 'tableElement'>".(($row['status_id'] == 2)?("Ready to Deploy"):("Deprovisioned"))."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();
    }


    /**
     * Gathers asset tag and location 
     * information from an SQL call and 
     * displays the information in a table
     * with a link to the asset
     */
    function getTagLocation($sql_arg, $mysql_arg, $snipe_arg, $cat_arg){
        $result = $mysql_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>". $cat_arg. " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Serial</td><td id = 'tableElement'>Location</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row['serial'] ."</td><td id = 'tableElement'>". $row['name'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();
    }

/**
 * Gathers asset info from 
 * multiple SQL calls and
 * compiles it into a report
 * capturing every K-4 Chromebook
 * in the district
 * $rooms is a 2d array of every
 * room to check as well as the amount
 * of CBs supposed to be in the room
*/
function getK4Errors($rooms, $mysql_arg, $snipe_arg, $cat_arg, $acronym){
	
	echo "<details>";
	echo "<summary>".$cat_arg."</summary>";
	echo "<table border='1'>";
	foreach($rooms as $class){//iterate through each room
		for($x = 0; $x < (int)$class[1]; $x++){//iterate through each CB in each room
			
			//This is the MySQL statement that gets executed for each asset to generate the report
			$mySQLCBS = "select * from assets where asset_tag = '".$acronym." ".$class[0]."-".(($x+1 < 10)?("0".$x+1):($x+1))."';";//ternary operator to add leading 0
		    	$result = $mysql_arg -> query($mySQLCBS);
			if($result->num_rows < 1){//didn't find it
				echo "<tr><td id = 'tableElement'>". $acronym." ".$class[0]."-".(($x < 10)?("0".$x):($x)) ."</td><td id = 'tableElement'>Not Found</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $acronym." ".$class[0]."-".(($x < 10)?("0".$x):($x)) . "' target = '_blank'>Link</a></td></tr>";
			} else if($result->num_rows > 1){//found more than 1, should never happen as asset tags are unique
				echo "<tr><td id = 'tableElement'>You should not be seeing this</td></tr>";
			} else if($result->num_rows ===1){//found it
				$row = $result -> fetch_assoc();
				echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>Found</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
			}
			// Free result set
      			  $result -> free_result();
		}
	}
	echo"</table>";
        echo "</details>";
}

?>



