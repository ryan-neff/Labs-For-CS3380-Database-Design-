
/* Name : Ryan Neff
   pawprint: rcn6f4
   date: 4/19/15
   LAB 10
*/


--Write a SQL statement that drops the lab10 schema if it exists.
DROP SCHEMA IF EXISTS lab10 CASCADE;

-- Then write a SQL statement that creates a schema named lab10
CREATE SCHEMA lab10;

-- Add a statement to your file that sets the current search path to be lab10. This ensures that all tables
-- and functions created below will exist within the lab10 schema.
SET search_path = lab10;

-- This is the statement I used to import the data. However it was unclear if 
   \copy standings FROM '/facstaff/klaricm/public_cs3380/lab10/lab10_data.csv'  CSV HEADER; 
/*
   Write a CREATE TABLE statement to create a table named group standings that matches the definition
   that follows. Be sure to include the PRIMARY KEY for your table and any NOT NULL constraints. Also,
   include CHECK constraints that enforce the range of possible values.
*/
CREATE TABLE standings(
    team varchar(25),
    wins smallint CHECK (wins >=0) NOT NULL,
    losses smallint CHECK (losses >=0) NOT NULL,
    draws smallint  CHECK(draws >=0) NOT NULL,
    points smallint CHECK(points>=0)  NOT NULL,

     PRIMARY KEY (team)

);

/*
   Now, write a pure SQL (i.e. not a PL/pgSQL function) function named calc points total that takes
two arguments that correspond to the number of wins and draws earned by a team. This function
should return the total number of points earned based on the formula in Equation 1 above.
*/

CREATE OR REPLACE FUNCTION calc_points_total(integer,integer)
RETURNS integer AS $$
           SELECT ($1*3) + $2 AS result;
        $$ LANGUAGE SQL;

 /*
 Create a PL/pgSQL function named update points total that is a trigger. This function should
update the NEW record’s points field using the calc points total function before any INSERT or
UPDATE statement. 
 */
 CREATE OR REPLACE FUNCTION update_points_total() RETURNS TRIGGER AS $$
   BEGIN
       NEW.points := calc_points_total(NEW.wins, NEW.draws);

       RETURN NEW;

       END;
       $$ LANGUAGE plpgsql;

/*
   Attach this function to the table as a trigger named tr update points total.
Test this trigger with a few UPDATE and/or INSERT statements.
*/
  CREATE TRIGGER tr_update_points_total BEFORE INSERT OR UPDATE OF wins, draws ON standings 
  FOR EACH ROW EXECUTE PROCEDURE update_points_total();

   /*
    Next, write a trigger function named disallow team name update that compares the OLD and NEW
records team fields. If they are different raise an exception that states that changing the team name is
not allowed.
*/
  CREATE OR REPLACE FUNCTION disallow_team_name_update() RETURNS TRIGGER AS $$
     BEGIN
        IF (OLD.team <> NEW.team) THEN 
             RAISE EXCEPTION 'Changing the team name is not allowed';

             END IF;
       END;
       $$ LANGUAGE plpgsql;

   
/*
  Then, attach this trigger to the table with the name tr disallow team name update and specify that
it fires before any potential update of the team field in the table. Test this trigger with a few UPDATE
statements to prove that it works.
*/
   CREATE TRIGGER tr_disallow_team_name_update BEFORE UPDATE of team ON standings 
   FOR EACH ROW EXECUTE PROCEDURE disallow_team_name_update(); 

