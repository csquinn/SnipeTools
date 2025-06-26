<?php
    function getTagSerial($sql_arg, $mysql_arg, $snipe_arg){
        $result = $mysqli_arg -> query($sql_arg);
        echo "<details>";
        echo "<summary>Assets with Incorrect Serial Numbers (". $result->num_rows .")</summary>";
        // Associative array
        echo "<table border='1'>";
        echo "<tr><td>Asset Tag</td><td>Serial</td><td>Link</td></tr>";
        while($row = $result -> fetch_assoc()){
            echo "<tr><td>". $row['asset_tag'] ."</td><td>". $row['serial'] ."</td><td><a href='" . $snipe_arg . "/hardware?page=1&size=20&search=" . $row['serial'] . "'>Link</a></td></tr>";
        }
        echo"</table>";
        echo "</details>";
    }
?>