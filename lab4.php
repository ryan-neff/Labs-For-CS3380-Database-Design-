<!DOCTYPE html>
<html>
	<head>
		<title>CS3380 Lab 4</title>
	</head>
	<body>
	
	<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
    <select name="query">

	<option value="1">Query 1</option>
	<option value="2">Query 2</option>
	<option value="3">Query 3</option>
	<option value="4">Query 4</option>
	<option value="5">Query 5</option>
	<option value="6">Query 6</option>
	<option value="7">Query 7</option>
	<option value="8">Query 8</option>
	<option value="9">Query 9</option>
</select>
<input type="submit" name="submit" value="Execute">
</form>	
<br>
<hr>
<br>



<?php
   
   /*  Name: Ryan Neff 													*
    *  PawPrint: Rcn6f4		    										*
    *  Date Due: 2/22/15 												*
    *  LAB 4															*
    *  URL: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab4/lab4.php *
    */

   if(isset($_POST['submit'])){

   include("../../secure/database.php");
   $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

   if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
   }


     $query = $_POST['query'];
     $result = dbQuery($query);
     $num_rows = pg_num_rows($result);


     echo "\n There were <i>$num_rows</i> rows returned<br><br><table border= 1><tr>\n";
     
     for($i = 0; $i < pg_num_fields($result); $i++){
     	$name = pg_field_name($result, $i);
     	echo "<td align = center ><strong>$name</strong></td>";
     }
     echo "</tr>";

     while($line = pg_fetch_array($result,null,PGSQL_ASSOC)){
 		echo "\t<tr>\n";
     	
     	foreach($line as $col_value){
     		echo"\t\t<td>$col_value</td>\n";
     	}
     	echo "\t</tr>\n";
     }
      echo "</table>\n";

    pg_free_result($result);

     pg_close($conn);
    }

    else{
     	echo"<strong>Select a query from the above list</strong>";
     }
     
     

     
     function dbQuery($queryNum){
     	
     	switch($queryNum){
     	
        /* 1. Create a view that shows the person’s id (pid), first name (fname) and last 
              name (lname) for all people who have a body weight above 140. This view should be named “weight” (without the quotes). You must use an INNER JOIN in the views query. Your PHP page should then query the view (i.e. SELECT * FROM lab4.weight). (8 rows)
        */
     	    case 1:
     	      
              $query = "SELECT * FROM lab4.weight";
     		break;
        
     	
        /* 2. Create a view that returns the first name (fname), last name (lname) and BMI for
              people with a weight above 150. This view should be named “BMI”. You must use an INNER JOIN and you must reference the “weight” view created in 3.3.1	
        */  
            case 2:
     	      
               $query = "SELECT * FROM lab4.bmi";
     		
     		break;
      
     	
        /* 3. Write a query that shows returns the name and city of the university that has 
              no people in database that are associated with it. Your query must use EXISTS to achieve. (2 rows)
        */	
            case 3:
     		   $query = "SELECT university_name, city FROM lab4.university AS u 
                         WHERE NOT EXISTS (SELECT 1 FROM lab4.person AS per 
                                           WHERE u.uid = per.uid)";
     		
     		break;

        
        /* 4. Write a query that returns only the uid value for all universities in the city 
              Columbia. Then use that query with an IN sub-query expression to retrieve the first and last names for all people that go to school in Columbia. (4 rows)
     	*/	
            case 4:
     		    $query = "SELECT fname, lname FROM lab4.person 
                          AS p WHERE uid IN (
                          SELECT u.uid FROM lab4.university AS u 
                          WHERE (city = 'Columbia'))";
     		break;
     	
        
        /* 5. Write a query that returns all of the activities with records in the 
              participated in table. Then use that query with a NOT IN sub-query expression to retrieve the activities that are not played by any player in the database. (2 rows)
     	*/
        	case 5:
                $query = "SELECT a.activity_name FROM lab4.activity AS a 
                          WHERE a.activity_name NOT IN (
                          SELECT pi.activity_name FROM lab4.participated_in as pi)";
     		break;
     	
     	
        
        /*  6. Write a query that returns the pid of all people listed in participated in 
               that participate in ‘running’. Then modify your query to use UNION to return all people who run or play racquetball. You must use the UNION operator to accomplish this. You cannot use OR. (5 Rows)
        */
        	case 6:
                $query = "SELECT pid FROM lab4.person as po 
                          INNER JOIN lab4.participated_in AS pi USING (pid) 
                          WHERE (activity_name = 'running') 
                          UNION 
                          SELECT pid FROM lab4.person 
                          INNER JOIN lab4.participated_in USING (pid) 
                          WHERE (activity_name = 'racquetball')";
     		
     		break;
     	
     	
        /*  7. Write a query that returns the first and last name of all people listed in 
               body composition table who are older than 30 years old. Then modify your query to use INTERSECTS to return all people who are older than 30 and are taller than 65 inches. You must use the INTERSECTS operator to accomplish this. You cannot use AND. (3 rows)
        */
        	case 7:
     	       $query = "SELECT fname,lname FROM lab4.person 
                         INNER JOIN lab4.body_composition USING (pid) WHERE (age > 30)
                         INTERSECT 
                         SELECT fname,lname FROM lab4.person 
                         INNER JOIN lab4.body_composition USING (pid) WHERE (height > 65)";
     		
     		break;
        
        
        /*  8. Write a query that returns peoples first and last names weight, height, and 
               age. Records should be ordered first by height in descending (Z-to-A order), then by weight in ascending order, and finally by the person’s last name in ascending order. (12 rows)      
     	*/
        	case 8:
     	        $query = "SELECT fname,lname,weight,height,age FROM lab4.person 
                          INNER JOIN lab4.body_composition using (pid) 
                          ORDER BY height DESC, weight ASC,lname ASC";
     		
     		break;

     	
        /* 9. Write a query using WITH that:
              1. First returns the person’s id (pid), first name (fname) and last name (lname) from all people who are from the people who go to the University of Missouri Columbia as the WITH clause of the query, and

             2. then use that common table expression (CTE) to combine the result with the body composition table via an inner join to get the body compositions for people who attend the University of Missouri Columbia.
     		
        */
            case 9:
     		   $query = "WITH mizzou_students AS (SELECT pid, fname, lname FROM lab4.person
                         INNER JOIN lab4.university USING (uid) 
                         WHERE (university_name = 'University of Missouri Columbia')) 
                         SELECT miz.pid, fname,lname, height, weight, age 
                         FROM mizzou_students AS miz, lab4.body_composition AS bo 
                         WHERE (miz.pid = bo.pid)";
     		   
     		break;
     	
  
     	}
     	$result = pg_query($query) or die('Query Failed'.pg_last_error());

     	return $result;
     }
 
?>

</body>
</html>