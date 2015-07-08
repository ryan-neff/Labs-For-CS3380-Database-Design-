/*

  * Name: Ryan Neff       *
  * Paw Print: rcn6f4     *
  * Lab: 7                *
  * Due Date: 3/15/15     *



  1.) Run the following EXPLAIN command and look at the query plan that is shown. Even though we
haven’t explicitly created an index, describe why the query plan shows than an index will be scanned.
Where did this index come from?

    Because the query is searching on a specific condition using the primary key (id), 
    an index scan using the primary key (id) as an index is more efficient as the search becomes a search of a binary tree rather 
    than a sequential scan which would be more costly because you are only looking for one item. 
    The index came from the table's primary key contraint.

_____________________________________________________________
 
 


 2.) First, write the query that returns all banks in the state of Missouri. Show this query and it’s query
plan (from the EXPLAIN command) in your answer. Then, write the command that creates an index
for the “state” field in table and execute it. Next, rerun your query for all Missouri banks again.
Finally, indicate how much faster this search is with the new index now in place in milliseconds and in
percent form. Also, record the new query plan. In summary, your answer should contain 5 things: (1)
the query for Missouri banks, (2) the query plan for this query, (3) the index creation command, (4)
the query plan now that an index has been added, and (5) the speed up in ms and % form.

*/
     SELECT * from lab7.banks WHERE state = 'Missouri';
     EXPLAIN ANALYZE SELECT * from lab7.banks WHERE state = 'Missouri';
/*                                              

										 QUERY PLAN                                               
--------------------------------------------------------------------------------------------------------
 Seq Scan on banks  (cost=0.00..894.98 rows=996 width=124) (actual time=0.366..13.747 rows=996 loops=1)
   Filter: ((state)::text = 'Missouri'::text)
 Total runtime: 14.775 ms
(3 rows)
*/
     CREATE INDEX ON lab7.banks (state);
     EXPLAIN ANALYZE SELECT * from lab7.banks WHERE state = 'Missouri';
 /*

                                                          QUERY PLAN                                                          
-----------------------------------------------------------------------------------------------------------------------------
 Bitmap Heap Scan on banks  (cost=23.97..598.42 rows=996 width=124) (actual time=0.466..4.623 rows=996 loops=1)
   Recheck Cond: ((state)::text = 'Missouri'::text)
   ->  Bitmap Index Scan on banks_state_idx  (cost=0.00..23.72 rows=996 width=0) (actual time=0.365..0.365 rows=996 loops=1)
         Index Cond: ((state)::text = 'Missouri'::text)
 Total runtime: 5.737 ms

*******  Speed up: 9.038ms  88.124% difference  ********
______________________________________________________




3.) Now, write a query that returns all banks ordered by their names. Include the query plan for this
command in your answer. Then, create an index on the name field in the table and show the command
in your answer. Finally, re-run the statement that returns all banks sorted by their name. Include
the query plan in your answer and the speedup. Your answer for this should contain the same items
required for question 2.
*/
     SELECT * FROM lab7.banks ORDER BY name;
     EXPLAIN ANALYZE SELECT * FROM lab7.banks ORDER BY name;

/*   
                                                    QUERY PLAN                                                   
---------------------------------------------------------------------------------------------------------------
---
 Sort  (cost=4657.15..4726.14 rows=27598 width=124) (actual time=302.805..439.674 rows=27598 loops=1)
   Sort Key: name
   Sort Method: external merge  Disk: 3760kB
   ->  Seq Scan on banks  (cost=0.00..825.98 rows=27598 width=124) (actual time=0.048..34.215 rows=27598 loops=
1)
 Total runtime: 467.766 ms
 */
     CREATE INDEX ON lab7.banks (name);
     EXPLAIN ANALYZE SELECT * FROM lab7.banks ORDER BY name;

 /*
                                                                 QUERY PLAN                                                             
------------------------------------------------------------------------------------------------------------------------------------
 Index Scan using banks_name_idx on banks  (cost=0.00..3294.27 rows=27598 width=124) (actual time=0.101..47.772 rows=27598 loops=1)
 Total runtime: 74.502 ms

 ********* Speed Up: 393.264ms   145.0441% difference **********

 ____________________________________________________ 

  

  4.)  Perhaps we want to be able to filter our searches based on whether or not a bank is “active”. Create
an index on the data in that field as well.
  */
     CREATE INDEX ON lab7.banks (is_active);
  /*
  _____________________________________________________

  

  5.) After creating the index in the previous question, which of the following two queries uses an index?
Which not? Use EXPLAIN to determine the answer. Also, describe reason that an index is used when
executing one of these but not the other, even though they are almost identical.
SELECT * FROM banks WHERE is_active = TRUE;
SELECT * FROM banks WHERE is_active = FALSE;

   The index is used on the query where is_active = TRUE and is not when is_active = FALSE.
   This is becasue there are many more entries where is_active equals false (20822) as opposed to true (6776). Due to
   this, it is a better option to just preform a table scan through the whole table instead of a Bitmap Heap scan like the other
   query.

  _____________________________________________________
  
  

  6.) Write a query that returns all banks with an “insured” date on or after 2000-01-01. Add this query
to your SQL file and also generate it’s query plan and add that to your file. Recall from history class,
that the federal government insuring bank deposits during the Great Depression. This means that our
dataset will show many banks with an insured data of 1934-01-01. Create an index on the “insured”
field, but make your index exclude all records with a value of 1934-01-01 in that field. Finally, re-run
your search (the one with the insured date on or after 2000-01-01) such the new index is used. Record
the query plan and note the speedup. Your answer for this should contain the same items required for
question 2.  

  */

     SELECT * FROM lab7.banks WHERE (insured >= '2000-01-01');

  /*
                                                  QUERY PLAN                                                
----------------------------------------------------------------------------------------------------------
 Seq Scan on banks  (cost=0.00..894.98 rows=1450 width=124) (actual time=2.553..10.369 rows=1451 loops=1)
   Filter: (insured >= '2000-01-01'::date)
 Total runtime: 11.833 ms
 */
     CREATE INDEX ON lab7.banks (insured) WHERE (insured != '1934-01-01');
     EXPLAIN ANALYZE SELECT * FROM lab7.banks WHERE (insured >= '2000-01-01');
   /*
                                                            QUERY PLAN                                                             
-----------------------------------------------------------------------------------------------------------------------------------
 Index Scan using banks_insured_idx on banks  (cost=0.00..573.89 rows=1450 width=124) (actual time=0.045..2.388 rows=1451 loops=1)
   Index Cond: (insured >= '2000-01-01'::date)
 Total runtime: 3.866 ms

  *********** Speed Up: 7.967ms  101.4969% Difference **************

  __________________________________________________________________________

  

  7.)  The federal government has an interest in tracking which banks have a low asset to deposit ratio. Write
a query that returns the id, name, city, state, assets, and deposits for all banks with an asset/deposit
ratio less than 0.5. (Note, that you will have to exclude records that have a 0 value in the deposit field.)
Include the query plan for this command in your answer. Then, create an index on this asset/deposit
expression, again excluding records with a 0 value in the deposit field. Include your index creation
command in your answer. Then, re-run your query that finds banks with an asset/deposit ratio of less
than 0.5. Include its new query plan and speedup. Your answer for this should contain the same items
required for question 2.
   */
     
     SELECT id,name,city,state,assets,deposits FROM lab7.banks WHERE (assets/deposits < .5) AND (deposits != 0);

     EXPLAIN ANALYZE SELECT id,name,city,state,assets,deposits FROM lab7.banks WHERE (assets/deposits < .5) AND (deposits != 0);
 
 /*                                              QUERY PLAN                                                
---------------------------------------------------------------------------------------------------------
 Seq Scan on banks  (cost=0.00..1032.97 rows=9166 width=63) (actual time=33.766..45.559 rows=46 loops=1)
   Filter: ((deposits <> 0::numeric) AND ((assets / deposits) < 0.5))
 Total runtime: 45.643 ms
*/

     CREATE INDEX ratio ON lab7.banks ((assets/deposits)) WHERE (deposits !=0);
   
     EXPLAIN ANALYZE SELECT id,name,city,state,assets,deposits FROM lab7.banks WHERE (assets/deposits < .5) AND (deposits != 0);
/*
                                                     QUERY PLAN                                                     
--------------------------------------------------------------------------------------------------------------------
 Bitmap Heap Scan on banks  (cost=215.54..925.95 rows=9166 width=63) (actual time=0.066..0.341 rows=46 loops=1)
   Recheck Cond: (((assets / deposits) < 0.5) AND (deposits <> 0::numeric))
   ->  Bitmap Index Scan on ratio  (cost=0.00..213.25 rows=9166 width=0) (actual time=0.044..0.044 rows=46 loops=1)
         Index Cond: ((assets / deposits) < 0.5)
 Total runtime: 0.427 ms
 
   
   ********* Speed Up: 45.216   196.2926% difference **************

 */







