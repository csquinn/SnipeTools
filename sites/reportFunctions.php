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
function getK4Errors($rooms, $mysql_arg, $snipe_arg, $cat_arg, $acronym, $buildingID){
	
	echo "<details>";
	echo "<summary>".$cat_arg."</summary>";
	echo "<table border='1'>";
	foreach($rooms as $class){//iterate through each room
		for($x = 0; $x < (int)$class[1]; $x++){//iterate through each CB in each room
			
			//This is the MySQL statement that gets executed for each asset to generate the report
			$mySQLCBS = "select * from assets inner join models on assets.model_id = models.id where assets.asset_tag = '".$acronym." ".$class[0]."-".(($x+1 < 10)?("0".$x+1):($x+1))."' and assets.deleted_at is null;";//ternary operator to add leading 0
		    	
			$result = $mysql_arg -> query($mySQLCBS);
			if($result->num_rows < 1){//didn't find it
				echo "<tr><td id = 'tableElement'>". $acronym." ".$class[0]."-".(($x+1 < 10)?("0".$x+1):($x+1)) ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>Not Found</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $acronym." ".$class[0]."-".(($x+1 < 10)?("0".$x+1):($x+1)) . "' target = '_blank'>Link</a></td></tr>";
			} else if($result->num_rows > 1){//found more than 1, should never happen as asset tags are unique
				echo "<tr><td id = 'tableElement'>You should not be seeing this, asset tag duplication</td></tr>";
			} else if($result->num_rows ===1){//found it
				$row = $result -> fetch_assoc();
				echo "<tr><td id = 'tableElement'>". $row['asset_tag'] ."</td><td id = 'tableElement'>". $row ['name']."</td><td id = 'tableElement' style='". (($row['rtd_location_id'] == $buildingID)?("background-color: green; color: black;"):("background-color: red; color: black;")) ."'>Location</td><td id = 'tableElement' style='". ((($row['status_id'] == 4 and $class[0] != "LNR") or ($row['status_id'] == 2 and $class[0] == "LNR"))?("background-color: green; color: black;"):("background-color: red; color: black;")) ."'>Status</td><td id = 'tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
			}
			// Free result set
      			  $result -> free_result();
		}
	}
	echo"</table>";
        echo "</details>";
}

function get512Errors($students, $mysql_arg, $snipe_arg, $cat_arg){
	echo "<details>";
	echo "<summary>".$cat_arg."</summary>";
	echo "<table border='1'>";

	foreach($students as $s){
		//simple mysql query to determine if asset exists
		$mySQLCBS = "select * from assets inner join users on assets.assigned_to = users.id where users.username = '". $s[2] ."' and assets.deleted_at is null;";
		
		$result = $mysql_arg -> query($mySQLCBS);
		if(($result->num_rows < 1) and ((int)$s[3] >= 5)) { //no cb assigned and is in grade that should have one
			echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>Not Found</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users?page=1&size=20&search=" . $s[2] . "' target = '_blank'>Link</a></td></tr>";
		} else if($result -> num_rows > 1){ //more than 1 cb assigned
			echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>Multiple Chromebooks assigned</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users?page=1&size=20&search=" . $s[2] . "' target = '_blank'>Link</a></td></tr>";
		} else if($result -> num_rows === 1){ //one cb assigned
			$row = $result -> fetch_assoc();
			if($s[3] >= 5){//of age to have cb

				//more advanced query to get finer data, shouldn't be run for every asset
				$mySQLCBS = "select assets.serial, assets.status_id, models.name as modelName, locations.name as locationName, status_labels.name as statusName from assets inner join users on assets.assigned_to = users.id inner join models on assets.model_id = models.id inner join status_labels on assets.status_id = status_labels.id inner join locations on assets.rtd_location_id = locations.id where users.username = '". $s[2] ."' and assets.deleted_at is null;";
				$result -> free_result();
				$result = $mysql_arg -> query($mySQLCBS);
				$row = $result -> fetch_assoc();
				if($result -> num_rows != 1){
					echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>Critical field not set (likely location)</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users?page=1&size=20&search=" . $s[2] . "' target = '_blank'>Link</a></td></tr>";
				} else if ($result -> num_rows ===1){
					echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: green; color: black;'>". $row['serial'] ."</td><td id = 'tableElement' style='". (($row['status_id'] == 4)?("background-color: green; color: black;"):("background-color: red; color: black;")) ."'>". $row['statusName'] ."</td><td id = 'tableElement'>". $row['modelName'] ."</td><td id = 'tableElement'>". $row['locationName'] ."</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users?page=1&size=20&search=" . $s[2] . "' target = '_blank'>Link</a></td></tr>";
				}
			} else { //too young to have cb?
				echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>Shouldn't have Chromebook (too young?)</td><td id = 'tableElement'><a href='" . $snipe_arg . "/users?page=1&size=20&search=" . $s[2] . "' target = '_blank'>Link</a></td></tr>";
			}
			// Free result set
      			$result -> free_result();
		}
	}

	echo"</table>";
        echo "</details>";
}


//finds cbs assigned to students not on the students list (likely graduated or moved)
function getExtraStudentErrors($students, $mysql_arg, $snipe_arg, $cat_arg){
	//create temporary table
	$mysql_arg -> query('drop table if exists tempStudents;');
	$sql = 'create table tempStudents (last_name varchar(255) not null, first_name varchar(255) not null, username varchar(255) not null, grade varchar(255) not null) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
	if($mysql_arg -> query($sql) === FALSE) {
		echo "error creating the students table";
	}

	//add every student to the table
	foreach($students as $s){
		$mysql_arg -> query('insert into tempStudents (last_name, first_name, username, grade) values ("'.$s[0].'","'.$s[1].'","'.$s[2].'","'.$s[3].'");');
	}

	//find students with cbs that aren't in the district
	$sql = 'select * from assets inner join users on assets.assigned_to = users.id where users.username not in (select username from tempStudents) and assets.deleted_at is null';
	$result = $mysql_arg -> query($sql);

	echo "<details>";
        echo "<summary>". $cat_arg . " (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td id = 'tableElement'>Asset Tag</td><td id = 'tableElement'>Serial</td><td id = 'tableElement'>Asset Name</td><td id = 'tableElement'>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td id = 'tableElement'>". $s[0] ."</td><td id = 'tableElement'>". $s[1] ."</td><td id='tableElement'>". $s[2] ."</td><td id = 'tableElement'>Grade ". $s[3] ."</td><td id = 'tableElement' style = 'background-color: red; color: black;'>". $row['serial'] ."</td><td id='tableElement'><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "' target = '_blank'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
        // Free result set
        $result -> free_result();

	//drop table
	$sql = 'drop table tempStudents;';
	if($mysql_arg -> query($sql) === false){
		echo "couldn't drop table";
	}

}
?>







