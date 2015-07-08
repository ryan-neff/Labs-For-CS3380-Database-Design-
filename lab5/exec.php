<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 5</title>
</head>
<body>
<?php  

  /*  Name: Ryan Neff 													*
    *  PawPrint: Rcn6f4		    										*
    *  Date Due: 3/4/15 												*
    *  LAB 5															*
    *  URL: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab5/lab5.php *
    */
  
    include("../../secure/database.php");
   $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

   
   if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
   }

  //create global varables for the hidden form elements of the "index" page
  $tbl = $_GET['tbl'];
  $pk = $_GET['pk'];
  $action = $_GET['action'];
  

 //if the actoion was to insert
  if($action == 'insert'){
     
     //do a query that will retrieve all countries for a drop down menu in the table
  	 $countries = pg_prepare($conn,"countries","SELECT country_code, name FROM lab5.country")or die('Query Failed'.pg_last_error());
  	 $countries = pg_execute($conn, "countries", array());
  	 ?>
  	  		
			<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
  	  		<input type="hidden" name="action" value="save_insert" />
				Enter data for the city to be added: <br />
			<table border="1">
			<tr><td>Name</td><td><input type="text" name="name" /></td></tr>
			<tr><td>Country Code</td><td><select name="country_code">
			
			<?php
			  //this creates the dropdown menu for the user to choose a given country
			   while($line = pg_fetch_array($countries,null,PGSQL_ASSOC)){
			   		echo "<option value =$line[country_code]>$line[name]</option>";
			   }
			 ?>
			   
  			 </select></td></tr>
			<tr><td>District</td><td><input type="text" name="district" /></td></tr>
			<tr><td>Population</td><td><input type="text" name="population" /></td></tr>
     		</table>
			<input type="submit" name = "submit" value="Save" />
			<input type="button" value="Cancel" onclick="top.location.href='lab5.php';" />
  	       </form>

  	

  	<?php
       }
  	    
         //once the user hits save
  	     if(isset($_POST['submit'])){
  	     
  	     //grab all nessecary variables from the form table
  	     $name = htmlspecialchars($_POST['name']);
  	     $country_code = htmlspecialchars($_POST['country_code']);
  	     $district = htmlspecialchars($_POST['district']);
  	     $population = intval(htmlspecialchars($_POST['population']));
            
  	     if($population  > 0 ){
  	     	//create the insert query to the database with the variables 
  	     	$insert = pg_prepare($conn, "insert", 'INSERT INTO lab5.city (name, country_code, district, population) VALUES ($1,$2,$3,$4);');
  	     	$insert = pg_execute($conn,"insert", array($name,$country_code,$district,$population))or die('Query Failed'.pg_last_error());
            
          //direct user back to search page if query was successful
            echo "<p> Insert was successful       
                  Click <a href='lab5.php'>here</a> to return.";
  	  	   }else{
  	  	   //direct user back to searchpage if query was unsuccessful
  	     	 echo "You did not enter an Integer (greater than 0) for population. Click <a href = 'lab5.php'>here</a> to return.";
  	         }
         }
  	 
      //if the user chose to remove a row
       if($action == 'remove'){

         //do the correct query to the database depending on the table that was 
       	//chosen by the user
          if($tbl == 'country'){
          	 $delete = pg_prepare($conn,"delete", "DELETE FROM lab5.country WHERE country_code = $1" );
          	 $delete = pg_execute($conn,"delete", array($pk))or die('Query Failed'.pg_last_error());
          }elseif ($tbl == 'city') {
          	 $delete = pg_prepare($conn,"delete", "DELETE FROM lab5.city WHERE id = $1" );
          	 $delete = pg_execute($conn,"delete", array($pk))or die('Query Failed'.pg_last_error());
          }elseif ($tbl == 'language') {
          	$values = explode(':', $pk);
          	$delete = pg_prepare($conn,"delete", "DELETE FROM lab5.country_language WHERE country_code = $1 AND language = $2" );
          	$delete = pg_execute($conn,"delete", array($values[0],$values[1]))or die('Query Failed'.pg_last_error());
          }
          //let the user know if the removal was successful and direct back to the homepage
       echo "<p> Removal was successful
                  Click <a href='lab5.php'>here</a> to return.";
       }	

       //if the user chose to edit a row.. 
       if ($action == 'edit') {
       //create a form that will pass the changes the user makes
       ?>
        	<form method="GET" action="<?=$_SERVER['PHP_SELF']?>">
       <?php 
        //make hidden forms to pass the global  variables of the current form 
           echo "<input type ='hidden' name = 'pk' value = $pk>
                 <input type = 'hidden' name = 'tbl' value = $tbl>";
         
         //preform the correct query given the table the user has chosen
       	 if($tbl == 'country'){
       	 	$table = pg_prepare($conn,"edit_table", "SELECT * FROM lab5.country WHERE country_code = $1");
       	 	$table = pg_execute($conn,"edit_table", array($pk))or die('Query Failed'.pg_last_error());
           }elseif($tbl == 'city'){
       	 	$table = pg_prepare($conn,"edit_table", "SELECT * FROM lab5.city WHERE id = $1");
       	 	$table = pg_execute($conn,"edit_table", array($pk))or die('Query Failed'.pg_last_error());
           }elseif($tbl == 'language'){
           	$values = explode(':', $pk); //splits the primary key into 2 strings (country code, and language)
       	 	$table = pg_prepare($conn,"edit_table", "SELECT * FROM lab5.country_language WHERE country_code = $1 AND language =$2");
       	 	$table = pg_execute($conn,"edit_table", array($values[0],$values[1]))or die('Query Failed'.pg_last_error());
           }
           
           //create the table
           echo"<table border =1>";
       	  while($line = pg_fetch_array($table,null,PGSQL_ASSOC)){
       	  	$i=0;
 		   echo "\t<tr>\n";
 		   	foreach($line as $col_value){	
 		    //get the name for each field..
 		    $name = pg_field_name($table, $i);
 		          
 		          //..if the name of the field will be accessible for change by the user, then check for it..
 		          //..once found it will become a text area instead of a plain <td> row
 		          if(($tbl == 'country' && ($name == 'indep_year' || $name == 'population' || $name == 'local_name' || $name == 'government_form'))
 		          	  ||($tbl == 'city' &&($name == 'district' || $name == 'population'))
 		          	  ||($tbl == 'language' &&($name=='is_official' || $name =='percentage'))){
                     
                     echo"\t\t<td><strong>$name</strong></td>
                              <td><input type='text' name='$name' value='$col_value' /></td>\n";
     		       }
     		           else{  //else make it a plain row
     		           	echo"\t\t<td>$name</td><td>$col_value</td>\n";
     	               }


     	    echo "\t</tr>\n";
     	    $i++;
     	   }  
           }
           echo "</table>\n";
         
         
         ?>  
           <input type="submit" name = "save" value="Save" />
           <input type="button" value="Cancel" onclick="top.location.href='lab5.php';" />
           </form>
       	
    <?php
        
       }
       
       	if(isset($_GET['save'])){  //if the user has chosen save
            $tbl = $_GET['tbl'];  //grab the right table from the form
                                
        if($tbl == 'country'){   //if the user has a country table
        						//grab all nessecary values from the form
       	 	$pk = $_GET['pk'];
       	 	$indep_year= intval(htmlspecialchars($_GET['indep_year']));
       	 	$population= htmlspecialchars($_GET['population']);
       	 	$local_name= htmlspecialchars($_GET['local_name']);
       	 	$government_form= htmlspecialchars($_GET['government_form']);
            
            //preform the update query with the variables grabbed from the form 
       	 	$update = pg_prepare($conn,"update_table", "UPDATE lab5.country SET indep_year = $1, population =$2,local_name=$3, government_form=$4 WHERE country_code=$5");
       	 	$update = pg_execute($conn,"update_table",array($indep_year, $population, $local_name, $government_form, $pk))or die('Query Failed'.pg_last_error());
       	   
       	    }

           
           elseif($tbl == 'city'){   //if the user chose the city table
           							//grab all nessecary values from the form
             $district = htmlspecialchars($_GET['district']);
             $population = htmlspecialchars($_GET['population']);

              //preform the update query with the variables grabbed from the form 
       	 	 $update = pg_prepare($conn,"update_table", "UPDATE lab5.city SET district = $1, population = $2 WHERE id = $3");
       	 	 $update = pg_execute($conn,"update_table", array($district,$population,$pk))or die('Query Failed'.pg_last_error());
            
              }
            

           elseif($tbl == 'language'){   //if the user chose the language table
           								//grab all nessecary values from the form
           	$values = explode(':', $pk); //splits the primary key into 2 strings (country code, and language)
           	$is_official = htmlspecialchars($_GET['is_official']);
           	$percentage = intval(htmlspecialchars($GET['percentage']));
       	 	 //preform the update query with the variables grabbed from the form 
       	 	$update = pg_prepare($conn,"update_table", "UPDATE lab5.country_language SET is_official = $1, percentage = $2 WHERE country_code = $3 AND language = $4");
       	 	$update = pg_execute($conn,"update_table", array($is_official,$percentage,$values[0],$values[1]))or die('Query Failed'.pg_last_error());
           }


           if($update){
           	//let user know th eedit was successful and direct to index page
           echo "<p> Edit was successful!
                  Click <a href='lab5.php'>here</a> to return.";
       	    }else{
       	    	echo "Update was not successful 
       	    	      Click <a href='lab5.php'>here</a> to return.";
       	    }
           } 
       
    ?>
</body>

</html>
