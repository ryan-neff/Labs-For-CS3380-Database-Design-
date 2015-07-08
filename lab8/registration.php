<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
</head>
<body>
<!-- Jump out of PHP and create a form to hold the inputs-->
<div align = "center">               
	<div id = "login"
	  <p>Please register
	  <form action="registration.php" method='post'>
		  <label for="username">username:</label>
		  <input type="text" name="username" id="username">
		  <label for="password">password:</label>
		  <input type="password" name="password" id="password">
		  <br>
		  <input type="submit" name="submit" value="submit">
	  </form> 
	  </p>
	</div>
</div>
<?php 
   /* Name: Ryan Neff
    * Date: 4/8/2015
    * Pawprint: rcn6f4
    * LAB 8
    * URL: https://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/index.php
    */
   session_start();

   if(isset($_POST['submit'])){
     
     include("../../secure/database.php");
     $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

     if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
      }

      mt_srand();
      $salt = sha1(mt_rand()); //create salt
      $password = htmlspecialchars($_POST['password']); //get user password
      $pwHash = sha1($password . $salt); //hash them together
      $username = htmlspecialchars($_POST['username']); 
      $date = date('Y-m-d H:i:s');  //create correct date format
      
     //insert the dates and username into the user_info table and the username, hash, and salt in the authentication table
      $result = pg_prepare($conn, "register",'INSERT INTO lab8.authentication VALUES ($1,$2,$3)');
      $result = pg_prepare($conn, "user_info","INSERT INTO lab8.user_info (username, registration_date) VALUES ($1,$2)");
      $result = pg_execute($conn, "user_info",array($username,$date)) or die('Query Failed'.pg_last_error());
      $result = pg_execute($conn,"register", array($username,$pwHash,$salt))or die('Query Failed'.pg_last_error());
      
      $_SESSION['username'] = $username; 
      $ip = $_SERVER['REMOTE_ADDR']; 
      $action = 'register';
       
       //insert the uname,ip,date,and action into the log table
       $result = pg_prepare($conn, "log", 'INSERT INTO lab8.log (username,ip_address,log_date,action) VALUES($1,$2,$3,$4)') ;
       $result = pg_execute($conn, "log", array($username,$ip,$date,$action))or die('Query Failed'.pg_last_error());


      if(isset($_SESSION['username'])){ //if the username was set, relocate to the homepage.
        header("Location: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/home.php");
      }
      else{
      	 echo "<p>Return to the <a href = registration.php> registration page</a></p>";
      }



     
    }
 ?>
</body>
</html>