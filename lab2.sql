/*
  Name: Ryan Nef
  Pawprint: Rcn6f4
  Lab 2
  Due 2/8/2015
*/
DROP SCHEMA IF EXISTS lab2 CASCADE;
--Create Schema--
CREATE SCHEMA lab2;

CREATE TABLE building(
   name 	varchar(50),
   city 	varchar(50),
   state	varchar(15),
   address  varchar(50),
   zipcode  integer,

   PRIMARY KEY(address, zipcode)
);

CREATE TABLE office(
   room_number 			 integer PRIMARY KEY,
   waiting_room_capacity integer, 
   address 	 			 varchar(50), 
   zipcode		         integer,
   
   -- Make proper refrences to building table  
   FOREIGN KEY(address,zipcode) REFERENCES building  
);

CREATE TABLE doctor(
   medical_liscense_num integer PRIMARY KEY,
   first_name     		varchar(20),
   last_name      		varchar(20),
   room_number    		integer,
   FOREIGN KEY(room_number) REFERENCES office
);

CREATE TABLE patient(
  ssn 			varchar(20) PRIMARY KEY,
  first_name 	varchar(20),
  last_name		varchar(20)

);
 
 --Make table for the relationship between doctor and patient
CREATE TABLE doctor_has_appointment_with_patient(
   appt_date 	date,
   appt_time 	time,
   doctor 		integer, 
   patient 		varchar(20), 
   FOREIGN KEY(doctor) REFERENCES doctor(medical_liscense_num),
   FOREIGN KEY(patient) REFERENCES patient(ssn)
);


CREATE TABLE condition(
 icd10  		varchar(4) PRIMARY KEY,
 description 	varchar(150)
 ); 

-- Make table for the m to n relatonship between patient and condition
CREATE TABLE patient_has_condition(
patient 		varchar(20) REFERENCES patient(ssn),
condition 		varchar(4) REFERENCES condition(icd10)
);

CREATE TABLE insurance(
policy_num 		varchar(100),
insurer 		varchar(50),
patient_covered varchar(20) REFERENCES patient(ssn)
);

CREATE TABLE labwork(
test_name 		varchar(50),
test_timestamp  timestamp,
test_value 		integer,
patient 		varchar(20) REFERENCES patient(ssn),
PRIMARY KEY(test_name,test_timestamp)
);

-- Make proper Insert statements 
INSERT INTO building
VALUES ('Jesse Hall','Columbia','Missouri','123 Missouri Rd',65201);

INSERT INTO building
VALUES ('Rush Hospital','Chicago','Illinois','854 Health Dr.',60154);

INSERT INTO building
VALUES ('Sacred Heart Healthcare','New York','New York','405 Broadway Blvd',10001);


INSERT INTO office
VALUES (008,40,'123 Missouri Rd',65201);

INSERT INTO office
VALUES (108,45,'854 Health Dr.',60154);

INSERT INTO office
VALUES (303,80,'405 Broadway Blvd',10001);


INSERT INTO doctor
VALUES(1234567,'Ryan','Neff', 008);

INSERT INTO doctor
VALUES(0987654,'Robert','DiNero', 108);

INSERT INTO doctor
VALUES(1029384,'Alicia','Keys', 303);


INSERT INTO patient
VALUES ('111-22-3333', 'George', 'Washington');

INSERT INTO patient
VALUES ('222-11-4444', 'Christina', 'Applegate');

INSERT INTO patient
VALUES ('555-33-1111', 'Missy', 'Elliot');


INSERT INTO doctor_has_appointment_with_patient
VALUES ('2015-02-15', '12:00:00', 1234567, '111-22-3333');

INSERT INTO doctor_has_appointment_with_patient
VALUES ('2015-02-16', '13:10:00', 0987654, '222-11-4444');

INSERT INTO doctor_has_appointment_with_patient
VALUES ('2015-02-17', '15:15:00', 1029384, '555-33-1111');


INSERT INTO condition
VALUES ('A40', 'Certain infectious and parasitic disease');

INSERT INTO condition
VALUES ('G00', 'Diseases of the nervous system');

INSERT INTO condition
VALUES ('H50', 'Diseases of the eye and adnexa');


INSERT INTO patient_has_condition
VALUES ('111-22-3333','A40');

INSERT INTO patient_has_condition
VALUES ('222-11-4444','G00');

INSERT INTO patient_has_condition
VALUES ('555-33-1111','H50');


INSERT INTO insurance
VALUES('ABC-123-XYZ', 'Blue Cross', '111-22-3333');

INSERT INTO insurance
VALUES('CBA-123-ASQ', 'Liberty Mutual', '222-11-4444');

INSERT INTO insurance
VALUES('WAS-DA2-890', 'American', '555-33-1111');


INSERT INTO labwork
VALUES ('X-ray', CURRENT_TIMESTAMP, 09, '111-22-3333');

INSERT INTO labwork
VALUES ('Blood Work', CURRENT_TIMESTAMP, 123, '222-11-4444');

INSERT INTO labwork
VALUES ('Physical', CURRENT_TIMESTAMP, 01, '555-33-1111');


-- Output tables to view data (commented out as it was not required).
/*
SELECT * FROM building;
SELECT * FROM office;
SELECT * FROM doctor;
SELECT * FROM patient;
SELECT * FROM condition;
SELECT * FROM doctor_has_appointment_with_patient;
SELECT * FROM patient_has_condition;
SELECT * FROM insurance;
SELECT * FROM labwork;
*/


