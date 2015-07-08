<!DOCTYPE html>
<html>
	<head>
		<title>CS3380 Lab 6</title>
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
</select>
<input type="submit" name="submit" value="Execute">
</form>	
<br>
<hr>
<br>



<?php
   
   /*  Name: Ryan Neff 												                         	*
    *  PawPrint: Rcn6f4		    										                      *
    *  Date Due: 3/8/15 												                        *
    *  LAB 6															                              *
    *  URL: http://babbage.cs.missouri.edu/~rcn6f4/cs3380/lab6/lab6.php *
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
     	
        /* 1. List the minimum, maximum and average surface area of all countries in the database
        */
     	    case 1:
     	      
              $query = "SELECT MIN(surface_area),MAX(surface_area),AVG(surface_area) FROM lab6.country";
     		break;
        
     	
        /* 2. List the total population, total surface area and total GNP by region; order the results from largest to
              smallest GNP.
        */  
            case 2:
     	      
               $query = "SELECT region, SUM(population) AS total_pop,SUM(surface_area) AS total_area,SUM(gnp) AS total_gnp 
                         FROM lab6.country 
                         GROUP BY region 
                         ORDER BY SUM(gnp) DESC";
     		
     		break;
      
     	
        /* 3. Generate a list of all forms of government with the count of how many countries have that form of
              government. Also, list the most recent year in which any country became independent with that form
              of government. The results should be ordered by decreasing count. For situations when multiple
              forms of government have the same count, sort these in descending order by the most recent year of
              independence.
        */	
            case 3:
     		   $query = "SELECT government_form, COUNT(*) AS count, MAX(indep_year) AS most_recent_indep_year 
                     FROM lab6.country 
                     GROUP BY government_form 
                     HAVING MAX(indep_year) IS NOT NULL 
                     ORDER BY count(*) DESC,MAX(indep_year) DESC";
     		
     		break;

        
        /* 4.  For each country with at least one hundred cities in the database, list the total number of cities it
               contains. Order the results in ascending order of the number of cities.
     	*/	
            case 4:
     		    $query = "SELECT co.name, count(*) 
                      FROM lab6.country AS co 
                      INNER JOIN lab6.city USING (country_code) 
                      GROUP BY country_code 
                      HAVING count(*)>99 
                      ORDER BY count ASC";
     		break;
     	
        
        /* 5. List the country name, it’s population, and the sum of the populations of all cities in that country.
              Add a fourth field to your query that calculates the percent of urban population for each country. (For
              the purposes of this example, assume that the sum of the populations of all cities listed for a country
              represent that country’s entire urban population.) Order the results of this query in increasing order
              of urban population percentage.
     	*/
        	case 5:
                $query = "SELECT co.name, co.population AS country_population, sum(ci.population)AS urban_population,
                         (sum(ci.population::float)/co.population::float)*100 AS urban_pct 
                         FROM lab6.country as co 
                         INNER JOIN lab6.city as ci USING (country_code) 
                         GROUP BY country_code 
                         ORDER BY urban_pct ASC";
     		break;
     	
     	
        
        /*  6. For each country, list the largest population of any of its cities and the name of that city. Order the
               results in decreasing order of city populations.
        */
        	case 6:
                $query = "SELECT co.name,ci.name AS largest_city,max 
                          FROM (SELECT name,country_code, population,MAX(population) OVER(PARTITION BY country_code) max 
                                FROM lab6.city AS ci)ci 
                          INNER JOIN lab6.country AS co USING(country_code) 
                          WHERE ci.population = max 
                          ORDER by max DESC";
     		
     		break;
     	
     	
        /*  7. List the countries in descending order beginning with the country with the largest number of cities in
               the database and ending with the country with the smallest number of cities in the database. Cities
               that have the same number of cities should be sorted alphabetically from A to Z.
        */
        	case 7:
     	       $query = "SELECT co.name, count(*) FROM lab6.country as co JOIN lab6.city USING (country_code) GROUP BY (country_code) ORDER BY count(*) DESC,co.name ASC";
     		
     		break;
        
        
        /*  8.  For each country with 8-12 languages, list the number of languages spoken, in descending order by
                number of languages as well as the name of the capital for that country.      
     	*/
        	case 8:
     	        $query = "SELECT co.name,ci.name AS capital,count AS lang_count FROM (
                                          SELECT name, capital, count(*) AS count,country_code 
                                          FROM lab6.country JOIN lab6.country_language USING(country_code) 
                                          GROUP BY country_code 
                                          HAVING count(*)<13 AND count(*)>7)co 
                        INNER JOIN lab6.city as ci USING (country_code) 
                        WHERE ci.id = co.capital 
                        ORDER BY count DESC, ci.name DESC";
     		
     		break;

     	
        /* 9. Using SQL window functions, write a query that calculates a running total of the sum of all city
              populations with each country. This running total should be calculated by accumulating the city
              populations from largest to smallest. The resulting output should be sorted first by country name and
              secondarily by the running total column. Also display the city name and city population in each row
        */
            case 9:
     		   $query = "SELECT co.name AS country, ci.name AS city, ci.pop AS population, ci.running_total 
                     FROM( SELECT ci.name AS name, ci.country_code,ci.population AS pop, 
                           SUM(population) OVER (PARTITION BY ci.country_code 
                                                 ORDER BY ci.population DESC) running_total 
                            FROM lab6.city AS ci)AS ci 
                      INNER JOIN lab6.country AS co USING(country_code) 
                      ORDER BY co.name,ci.running_total";
     		   
     		break;

        /* 10. Again, using window functions rank the popularity of each language within each country. We’ll assume
               that the percent of speakers of a language in the country is a measure of it’s popularity. For each record,
               show the name of the country, the name of the language and it’s popularity rank. The most popular
               language should be ranked 1, the second most popular 2, etc.
        */
            case 10:
           $query = "SELECT co.name, la.language, la.popularity_rank 
                     FROM( SELECT language,country_code,rank() OVER( PARTITION BY country_code 
                                           ORDER BY percentage DESC) popularity_rank 
                            FROM lab6.country_language) AS la 
                      INNER JOIN lab6.country AS co USING (country_code) 
                      ORDER BY co.name,la.popularity_rank";
           
        break;
     	
  
     	}

     	$result = pg_query($query) or die('Query Failed'.pg_last_error()); //execute the query

     	return $result;  //return the query
     }
 
?>




</body>
</html>