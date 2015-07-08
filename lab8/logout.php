<?php 
    /* Name: Ryan Neff
    * Date: 4/8/2015
    * Pawprint: rcn6f4
    * LAB 8
    * URL: https://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/index.php
    */

       include("../../secure/database.php");
     $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

     if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
      }

  session_start();
  $date = date('Y-m-d H:i:s');
  //insert values into user log
  $input = pg_prepare($conn, "input", 'INSERT INTO lab8.log (username,ip_address,log_date,action) VALUES ($1,$2,$3,$4)');
  $input = pg_execute($conn, "input", array($_SESSION['username'],$_SERVER['REMOTE_ADDR'],$date,'logout'));
  
  session_destroy(); //end the session
  header("Location: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/index.php");  //relocate to homepage
 ?>