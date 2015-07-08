/*
 * Name: Ryan Neff
 * pawprint: rcn6f4
 * Lab 11
 */

#include <stdio.h>
#include <sqlite3.h>

int main(int argc, char** argv)
{
  if(argc != 4)
    {
      fprintf(stderr, "USAGE: %s <database file> <table name> <CSV file>\n", argv[0]);
      return 1;
    }
   //declare/initialize variables
    sqlite3 *db;
    int rc,i;
    sqlite3_stmt *stmt;
    rc = sqlite3_open("mydatabase.db", &db); //open the db
    FILE *fp;
          fp = fopen(argv[3],"w");

    if(rc){
       fprintf(stderr, "Cant Open Database %s \n.", sqlite3_errmsg(db));
       return(0);
    }else{
         fprintf(stderr,"Opened Successfully");
    }
   

       sqlite3_prepare_v2(db,"SELECT * FROM mytable;", -1, &stmt, NULL); //prepare the query that gets everything from the table
     int cols = sqlite3_column_count(stmt); //get the column count
  
       int result = sqlite3_step(stmt); //get a row
     
       while(result == SQLITE_ROW){
         
         //iterate through the columns and print according to the data type
          for( i = 0; i<cols; i++){ 
             if(sqlite3_column_type(stmt,i) == SQLITE_TEXT){
                 fprintf(fp,"%s, ",sqlite3_column_text(stmt,i) );
               }else if(sqlite3_column_type(stmt,i) == SQLITE_INTEGER){
                 fprintf(fp,"%i, ",sqlite3_column_int(stmt,i));
               }
                
          }
               fprintf(fp,"\n"); //get a new line
         result= sqlite3_step(stmt); //go to the next row
        }
       
     

 // printf("Implement me! %i\n", cols);
fclose(fp);  
sqlite3_close(db);
  return 0;
}
