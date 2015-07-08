<!DOCTYPE html>
<html>
	<head>
		<title>CS3380 Lab 3</title>
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
	<option value="10">Query 10</option>
	<option value="11">Query 11</option>
	<option value="12">Query 12</option>
</select>
<input type="submit" name="submit" value="Execute">
</form>	
<br>
<hr>
<br>



<?php
   
   /*  Name: Ryan Neff 													*
    *  PawPrint: Rcn6f4		    										*
    *  Date Due: 2/15/15 												*
    *  LAB 3 															*
    *  URL: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab3/lab3.php *
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
     	
     	/*	1. Find the district and population of all cities named Springfield. 
     	Sort results from most populous to least populous. (3 results)	
     	*/	
     	    case 1:
     		$query = "SELECT district, population FROM lab3.city 
     			WHERE (name = 'Springfield') ORDER BY population DESC";
     		
     		break;
        
        /*2. Find the name, district, and population of each city in Brazil (country code BRA). 
        Order results by city name alphabetically. (250 results)
     	*/	
     		case 2:
     		$query = "SELECT name,district,population FROM lab3.city 
     			WHERE (country_code = 'BRA') ORDER BY name ASC";
     		
     		break;
        
        /*3. Find the name, continent, and surface area of the smallest countries by surface area. 
        Order by surface area with smallest first. Return only 20 countries. (20 results)
     	*/
     		case 3:
     		 $query = "SELECT name, continent, surface_area FROM lab3.country 
     		 	ORDER BY surface_area ASC LIMIT 20";
     		
     		break;

        /*4. Find the name, continent, form of government, and GNP of all countries having a GNP greater than 200,000. 
             Sort the output by the name of the country in alphabetical order from A to Z. (23 results)
     	*/
     		case 4:
     		 $query ="SELECT name, continent, government_form, gnp FROM lab3.country 
     		 	WHERE (gnp > 200000) ORDER BY name ASC";
     			
     		break;
     	
     	/*5. Find the 10 countries with the 10th through 19th best life expectancy rates. 
     	You should use WHERE ife expectancy IS NOT NULL to remove null values when querying this table. (10 results)
     	*/
     		case 5:
              $query ="SELECT name, life_expectancy FROM lab3.country 
              	WHERE (life_expectancy IS NOT NULL) 
              	ORDER BY life_expectancy DESC OFFSET 10 LIMIT 10";
     		
     		break;
     	
     	/*6. Find all city names that start with the letter B and ends in the letter s. 
     	Results should be ordered from largest to smallest population, but do not display the population field. (12 results)
     	*/
     		case 6:
     		 $query ="SELECT name FROM lab3.city 
     		 	WHERE (name LIKE 'B%') AND (name LIKE '%s') 
     		 	ORDER BY population DESC";
     		
     		break;
     		
     	/*7. Return the name, name of the country, and city population of each city in the world 
     	having population greater than 6,000,000. Order results by the city population with the most populous first. (20 results)
         */
     		case 7:
     		 $query = "SELECT ci.name, co.name AS country, ci.population FROM lab3.city AS ci, lab3.country AS co 
     		 	WHERE (ci.country_code = co.country_code) AND (ci.population > 6000000) 
     		 	ORDER BY ci.population DESC";
     		
     		break;
        
        /*8. Find the country name, language name and percent of speakers of all unofficial languages spoken in
             countries of population greater than 50,000,000 population. Order results by percent of speakers with
             the most spoken language first. (165 results)
     	*/	
     		case 8:
     		 $query = "SELECT name, language, percentage 
     		 	FROM lab3.country as co, lab3.country_language as la 
     		 	WHERE (co.country_code = la.country_code) 
     		 	AND (la.is_official = FALSE) AND (co.population > 50000000) 
     		 	ORDER BY percentage DESC";
     		
     		break;

     	/*9. Find the name, independence year, and region of all countries where English is an official language.
             Order results by region ascending and alphabetize the results within each region by country name. (44 results)
        */
     		case 9:
     		$query = "SELECT name, indep_year, region FROM lab3.country AS co 
     			INNER JOIN lab3.country_language AS la USING (country_code) 
     			WHERE (la.is_official = TRUE) AND (la.language = 'English') 
     			ORDER BY region ASC, co.name ASC";
     		
     		break;
     	
     	/*10. For each country display the capital city name and the percentage of the population that lives in the
			  capital for each country. Sort the results from largest percentage to smallest percentage. 
			  (232 results)	
     	*/	
     		case 10:
     		$query = "SELECT ci.name, co.name AS country, 
     			round((CAST(ci.population AS float)/CAST(co.population AS float))*100) AS population_percent 
     			FROM lab3.city AS ci INNER JOIN lab3.country AS co USING (country_code) 
     			Where(ci.id = co.capital)
     			ORDER BY population_percent DESC";
     		
     		break;
     	
     	/*11. Find all official languages, the country for which it is spoken, and the percentage of speakers (percentage
			  of speakers is calculated as percentage spoken times country population divided by 100). Order results
			  by the total number of speakers with the most popular language first. (238 results)	
     	*/	
     		case 11:
     		$query = "SELECT name, language, round((la.percentage*co.population)/100) AS percent_speakers 
     			FROM lab3.country AS co 
     			INNER JOIN lab3.country_language AS la USING (country_code) 
     			WHERE (la.is_official = TRUE) 
     			ORDER BY percent_speakers DESC";
     		
     		break;
     	
     	/*12. Find the name, region, GNP, old GNP, and real change in GNP for the countries who have most
			  improved their relative wealth. the real change in GNP is defined as (gnp - gnp old)/gnp old. Order
			  results by real change with the most improved country first. Also, this data is missing some entries for
			  gnp and gnp old. Filter these missing entries out by only returning countries where gnp IS NOT NULL
			  and gnp old IS NOT NULL. (178 results)	
     	*/	
			case 12:
     		$query = "SELECT name, region, gnp, gnp_old, (gnp-gnp_old)/gnp_old AS real_change FROM lab3.country 
     				WHERE (gnp_old IS NOT NULL) ORDER BY real_change DESC";
     		break;
     	}
     	$result = pg_query($query) or die('Query Failed'.pg_last_error());

     	return $result;
     }
 
?>

</body>
</html>