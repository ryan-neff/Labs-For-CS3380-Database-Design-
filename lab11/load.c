/*
 * Name: Ryan Neff
 * pawprint: rcn6f4
 * Lab 11
*/
#include <stdio.h>
#include <sqlite3.h>
#include <string.h>

int main(int argc, char** argv)
{
  if(argc != 4)
    {
      fprintf(stderr, "USAGE: %s <database file> <table name> <CSV file>\n", argv[0]);
      return 1;
    }
   
   //initialize variables needed
    sqlite3 *db;
    int rc,i;
    sqlite3_stmt *stmt;
    char sqlstmt[140];
    rc = sqlite3_open(argv[1], &db);
    FILE *fp;
          fp = fopen(argv[3],"r"); //get csv file

    if(rc){
       fprintf(stderr, "Cant Open Database %s \n.", sqlite3_errmsg(db));
       return(0);
    }else{
         fprintf(stderr,"Opened Successfully");
    }
 
      strcpy(sqlstmt,"DELETE FROM ");
      strcat(sqlstmt,argv[2]);
      strcat(sqlstmt,";");


      rc = sqlite3_prepare_v2(db,sqlstmt, -1, &stmt, NULL); //truncate table
  int result = sqlite3_step(stmt); 
  char line[500];
  //rc = sqlite3_prepare_v2(db,"INSERT INTO mytable VALUES (?,?,?)",-1,&stmt,NULL); //prepare statement to query db
 
   while(fscanf(fp, "%[^\n]\n",line)){
        
           strcat(sqlstmt, "INSERT INTO ");
           strcat(sqlstmt,argv[2]);
	   strcat(sqlstmt,"VALUES (");
           strcat(sqlstmt,line);
           strcat(sqlstmt,");");            
           rc = sqlite3_prepare_v2(db,sqlstmt,-1,&stmt,NULL);
         
         if(rc != SQLITE_DONE){
            
          printf("Problem with insert %s" ,sqlite3_errmsg(db));
         }

         sqlite3_finalize(stmt);
       for(i=0;i<100;i++){
        sqlstmt[i] = '\0';
         } 
     }   

  fclose(fp);
  sqlite3_close(db);
  return 0;
}
