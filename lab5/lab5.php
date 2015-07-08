<!DOCTYPE html>
<html>
<head>
	<title>CS 3380 LAB 5</title>

<script>
//Insert element's attributes at a given row where a 'edit' or 'remove' button was hit
//submit the data to the form
function clickAction(form, pk, tbl, action)
{
  document.forms[form].elements['pk'].value = pk;
  document.forms[form].elements['action'].value = action;
  document.forms[form].elements['tbl'].value = tbl;
  document.getElementById(form).submit();
}
</script>

</head>
<body>
   <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
    Search for a :
    <input type="radio" name="search_by" checked="true" value="country">Country 
    <input type="radio" name="search_by" value="city">City
    <input type="radio" name="search_by" value="language">Language <br><br>
    That begins with: <input type="text" name="query_string" value=""> <br><br>
    <input type="submit" name="submit" value="Submit">
    </form>
<hr />
 Or insert a new city by clicking this <a href="exec.php?action=insert">link</a>

 <?php
   
   /*  Name: Ryan Neff 													*
    *  PawPrint: Rcn6f4		    										*
    *  Date Due: 3/4/15 												*
    *  LAB 5															*
    *  URL: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab5/lab5.php *
    */

   if(isset($_POST['submit'])){

   include("../../secure/database.php");
   $conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

    
   if(!$conn){
   	    echo "<p>Failed to connect to DB</p>";
   }
    $search_by = htmlspecialchars($_POST['search_by']);
    $query_string = $_POST['query_string'];
    

    //prepare given statements to search the database
    $result = pg_prepare($conn, "country", 'SELECT * FROM lab5.country AS co WHERE name ILIKE $1 ORDER BY name ASC'); 
    $result = pg_prepare($conn, "city", 'SELECT * FROM lab5.city AS ci WHERE name ILIKE $1 ORDER BY name ASC'); 
    $result = pg_prepare($conn, "language", 'SELECT * FROM lab5.country_language AS la WHERE language ILIKE $1 ORDER BY language ASC'); 
    
    //execute the correct query depending on the radio button
    switch ($search_by) {
    	case "country":
    		$result = pg_execute($conn, "country", array($query_string."%"));
    		break;
    	case "city":
    		$result = pg_execute($conn, "city", array($query_string."%"));
    		break;
    	default:
    		$result = pg_execute($conn, "language", array($query_string."%"));
    		break;
    }
      
     $num_rows = pg_num_rows($result); 
   echo "<br><br>\n There were <i>$num_rows</i> rows returned<br><br><table border= 1><tr>\n";
  
  //make the hidden form to send accross urls
    echo "
     <form id ='action_form' method='GET' action='exec.php'>
     <input type = 'hidden' name = 'pk'>
     <input type = 'hidden' name = 'tbl'>
     <input type = 'hidden' name = 'action'> ";
   
   
    echo "<td>Action</td>";
    for($i = 0; $i < pg_num_fields($result); $i++){
     	$name = pg_field_name($result, $i);
     	echo "<td align = center ><strong>$name</strong></td>";
     }
     echo "</tr>";

     while($line = pg_fetch_array($result,null,PGSQL_ASSOC)){
 		echo "\t<tr>\n";
     	
     //depending on what the user is searching by,
 	//send th eappropriate primary key for the element
     if($search_by == 'country'){
     echo "<td> <input type = 'button' value='Edit' onclick= clickAction('action_form','$line[country_code]','$search_by','edit'); />	
     	        <input type = 'button' value='Remove' onclick= clickAction('action_form','$line[country_code]','$search_by','remove'); />
           </td>";
       }else if($search_by == 'city'){
       	 echo "<td> <input type = 'button' value='Edit' onclick= clickAction('action_form','$line[id]','$search_by','edit'); />	
     	        <input type = 'button' value='Remove' onclick= clickAction('action_form','$line[id]','$search_by','remove'); />
           </td>";
       }else if($search_by == 'language'){
       	echo "<td> <input type = 'button' value='Edit' onclick= clickAction('action_form','$line[country_code]:$line[language]','$search_by','edit'); />	
     	        <input type = 'button' value='Remove' onclick= clickAction('action_form','$line[country_code]:$line[language]','$search_by','remove'); />
           </td>";
       }

     	
     	foreach($line as $col_value){
     		echo"\t\t<td>$col_value</td>\n";
     	}
     	echo "\t</tr>\n";
     }
      echo "</table>\n";

   echo "</form>";

 pg_free_result($result);

     pg_close($conn);
   
   }
 ?>
</body>
</html>