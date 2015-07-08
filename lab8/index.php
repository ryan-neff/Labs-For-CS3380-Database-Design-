
<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
</head>
<body>
	<div align = "center">               
				<div id = "login">
		  <p>Please login
		  <form action="index.php" method='post'>
			  <label for="username">username:</label>
			  <input type="text" name="username" id="username">
			  <label for="password">password:</label>
			  <input type="password" name="password" id="password">
			  <br>
			  <input type="submit" name="submit" value="submit">
		  </form> 
		  <p>Register <a href="registration.php">here</a></p>
		  </p>
		</div>
</div>
</form>

<?php 
   /* Name: Ryan Neff
    * Date: 4/8/2015
    * Pawprint: rcn6f4
    * LAB 8
    * URL: https://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/index.php
    */
   
   if($_SERVER['SERVER_PORT'] != 443 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')){  //relocate to secure protocall if not already
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
    }
    
   session_start();
   
   //if user is already logged in, relocate to home page
   if(isset($_SESSION['username'])){
      header("Location: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/home.php");
    }

    elseif(isset($_POST['submit'])){
     
     include("../../secure/database.php");
     $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

     if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
      }
       
       $username = htmlspecialchars($_POST['username']);  //get user name
       $password = htmlspecialchars($_POST['password']);  //..and password 
      
      //execute query to get info from inputted username
     $result = pg_prepare($conn, "check", "SELECT * FROM lab8.authentication WHERE (username = $1)"); 
     $result = pg_execute($conn,"check", array($username));

     $user = pg_fetch_array($result,NULL,PGSQL_ASSOC);
     $salt= $user['salt']; 
     $sha = sha1($password . $salt); //attempt to match the password hash
     
     
     if( ($user != NULL) && ( $sha == $user['password_hash'])){  //if the array is empty or the hash does not match, the username or password is incorrect
     	 $_SESSION['username'] = $username; //create session variable for username
     	 $date = date('Y-m-d H:i:s');
         
         $input = pg_prepare($conn, "input", 'INSERT INTO lab8.log (username,ip_address,log_date,action) VALUES ($1,$2,$3,$4)');
         $input = pg_execute($conn, "input", array($_SESSION['username'],$_SERVER['REMOTE_ADDR'],$date,'login'));
     	 header("Location: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab8/home.php"); //relocate to home
     }else{
      echo "<p align = center> Please Enter Your Log In Information Again </p>";
      }
     
     }





 ?>
</body>
</html>