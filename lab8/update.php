<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
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

    echo "<p align= center>Username: $_SESSION[username]</p>";  
 ?>
 
 <form method="POST" action="<? $_SERVER['PHP_SELF']?>">
    <table align = "center" border="1">
     <tr><td><strong>Description</strong></td>
         <td><input type="text" name="description" id = "description"></input></td>
    </tr>
    </table>
    <div id = "button" align="center">
    <button type = "submit" name ="submit"> Save </button>
    <p><a href="logout.php">Click here to logout</a></p>
    </div>
 
 <?php 

   if(isset($_POST['submit'])){
     $description  = htmlspecialchars($_POST['description']); //get desired desciption update from user
     $date = date('Y-m-d H:i:s'); //format date correctly
    
     //insert the descritption into the info table
    $result = pg_prepare($conn, "update", "UPDATE lab8.user_info SET description = $1 WHERE username = $2");
    $result = pg_execute($conn,"update", array($description,$_SESSION['username']));

    //input the log info into the log table 
    $input = pg_prepare($conn, "input", 'INSERT INTO lab8.log (username,ip_address,log_date,action) VALUES ($1,$2,$3,$4)');
    $input = pg_execute($conn, "input", array($_SESSION['username'],$_SERVER['REMOTE_ADDR'],$date,'update'));
     
    //relocate to the homepage 
    header("Location: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/home.php");
   }


  ?>
</div>
</body>
</html>