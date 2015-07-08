<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CS3380 Lab 8</title>
</head>
<body>
	<?php 

	   /* Name: Ryan Neff
   	    * Date: 4/8/2015
    	* Pawprint: rcn6f4
    	* LAB 8
    	* URL: https://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/index.php
    	*/

       session_start();

       include("../../secure/database.php");
     $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

     if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
      }
      
      //get all data for the headers of the homepage
      $result = pg_prepare($conn, "user_reg_info" ,"SELECT ip_address, log_date FROM lab8.log WHERE username = $1 AND action = 'register'");
      $result = pg_execute($conn, "user_reg_info", array($_SESSION['username']));
      $user_info = pg_fetch_array($result, NULL, PGSQL_ASSOC);
      
      //get the description for the homepage
      $desc = pg_prepare($conn, "get_descr","SELECT description FROM lab8.user_info WHERE username = $1");
      $desc = pg_execute($conn, "get_descr",array($_SESSION['username']));
      $description = pg_fetch_array($desc, NULL, PGSQL_ASSOC);
      
      //get all data for the tabe log
      $log = pg_prepare($conn, "log_data", "SELECT action,ip_address,log_date FROM lab8.log WHERE username = $1");
      $log = pg_execute($conn, "log_data", array($_SESSION['username']));
      $rows = pg_num_rows($log);
    
      //output all header information
      echo "<p align = center> Username: $_SESSION[username] </p>
            <p align = center> Ip Address: $user_info[ip_address]</p>
            <p align = center> Registration Date: $user_info[log_date]</p>
            <p align = center> Description: $description[description]
            <p align = center> There were <em>$rows</em> rows</p><br>";

        echo "<table align = center border =1><th>Action</th><th>IP address</th><th>Log Date</th>";
        
        while($line = pg_fetch_array($log,null,PGSQL_ASSOC)){  //traverse the array from the log query and create a table

         echo "<tr>";

         foreach ($line as $col_value) {
         	echo" <td>$col_value</td>";  //output all values for the table
         
         }
         echo"</tr>";
        }
        echo "</table>";
        pg_free_result($result);



	 ?>
	 <p align="center"><a href ='update.php'>Click</a> here to update page</p>
	 <p align="center"><a href = 'logout.php'>Click here to log out</a></p>
</body>
</html>