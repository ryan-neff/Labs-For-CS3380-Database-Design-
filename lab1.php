<html>
<head/>
<body>
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
  <table border="1">
     <tr><td>Number of Rows:</td><td><input type="text" name="rows" /></td></tr>
     <tr><td>Number of Columns:</td><td><select name="columns">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="4">4</option>
    <option value="8">8</option>
    <option value="16">16</option>

  </select>
</td></tr>
   <tr><td>Operation:</td><td><input type="radio" name="operation" value="multiplication" checked="yes">Multiplication</input><br/>
  <input type="radio" name="operation" value="addition">Addition</input>
  </td></tr>
  </tr><td colspan="2" align="center"><input type="submit" name="submit" value="Generate" /></td></tr>
</table>
</form>

<?php
  /*
   Name: Ryan Neff
   Pawprint: rcn6f4
   Date: 2/1/2015
   Lab 1  
  */
   

   if(isset($_POST['submit'])){  //Makes sure the user has pressed the  generate button
       $rows = $_POST['rows'];     //create refrence variables for the users input
       $columns = $_POST['columns'];
       $operation = $_POST['operation'];

 
       if(is_numeric($rows) && $rows > 0 ){   
          echo 'The '.$rows.'  X '. $columns.' '. $operation.'  table.'; //output table information 

         
          echo '<table border= "1"><tr>';  //create table along with the first row
          
        
        for($i=0; $i<=$columns; $i++){       //create a header for the columns and make each value  bold 
           echo'<td><strong>'.$i.' </strong></td>';
          }
           echo '</tr>'; 

           
             for($i=0; $i< $rows; $i++){        //Iterate through the rows and fill the table with the correct products
              echo '<tr><td><strong>'.($i+1).' </strong></td>';  //create a header for the current row
             for($k=0; $k< $columns; $k++){  //iterate through every index in the table
                
              /*Preform the proper action given the operation
               * this is not the best implementtation */
               
               if($operation == 'multiplication'){   
                  echo '<td>'.(($i+1)*($k+1)).'</td>';
                   }
                 
                 elseif($operation == 'addition'){
                  echo '<td>'.(($i+1)+($k+1)).'</td>';
                   }
               
              }
           }

           echo '</tr>';
        }
    elseif(!is_numeric($rows)){  //Error check to make sure the user entered a number
          echo "Rows must be a number";
        }
     elseif($rows <=0){  //Error check to make sure the user entered a number greater than zero
          echo "Rows must be greater than zero.";
       }
  
     echo '</table>'; //close the table
   }
   

?>

</body>
</html>
