/*User logs*/
CREATE TABLE ulog(
id SERIAL PRIMARY KEY,
email VARCHAR(64) NOT NULL,
operation VARCHAR(16) NOT NULL,
date TIMESTAMP WITH TIME ZONE NOT NULL);

CREATE OR REPLACE FUNCTION userlog()
RETURNS TRIGGER AS $$
DECLARE email VARCHAR(64);
DECLARE operation VARCHAR(16);
DECLARE now DATE;
BEGIN
now := now();
operation = 'Delete';
IF TG_OP ='INSERT' THEN
operation:='Insert'; 
email = NEW.email;

ELSIF TG_OP ='UPDATE' THEN
operation = 'Update';
email = OLD.email;

ELSE email:=OLD.email;
END IF;
INSERT INTO ulog(email,operation,date)  VALUES (email,operation, now);
RETURN NULL; 
END; $$
LANGUAGE PLPGSQL;


CREATE TRIGGER user_log
AFTER INSERT OR UPDATE OR DELETE
ON users
FOR EACH ROW
EXECUTE PROCEDURE userlog();

/*Task logs*/
CREATE TABLE tlog(
id SERIAL PRIMARY KEY,
taskid INTEGER NOT NULL,
operation VARCHAR(16) NOT NULL,
date TIMESTAMP WITH TIME ZONE NOT NULL);

CREATE OR REPLACE FUNCTION tasklog()
RETURNS TRIGGER AS $$
DECLARE taskid INTEGER;
DECLARE operation VARCHAR(16);
DECLARE now DATE;
BEGIN
now := now();
operation = 'Delete';

IF TG_OP ='INSERT' THEN
operation:='Insert'; 
taskid = NEW.id;
ELSIF TG_OP ='UPDATE' THEN
operation = 'Update';
taskid = OLD.id;
ELSE taskid:=OLD.id;
END IF;
INSERT INTO tlog(taskid,operation,date)  VALUES (taskid,operation, now);
RETURN NULL;
END; $$
LANGUAGE PLPGSQL;


CREATE TRIGGER task_log
AFTER INSERT OR UPDATE OR DELETE
ON tasks
FOR EACH ROW
EXECUTE PROCEDURE tasklog();


/*Bid logs*/
CREATE TABLE blog(
id SERIAL PRIMARY KEY,
taskid INTEGER NOT NULL,
email VARCHAR(64) NOT NULL,
operation VARCHAR(16) NOT NULL,
date TIMESTAMP WITH TIME ZONE NOT NULL);

CREATE OR REPLACE FUNCTION bidlog()
RETURNS TRIGGER AS $$
DECLARE taskid INTEGER;
DECLARE email VARCHAR(64);
DECLARE operation VARCHAR(16);
DECLARE now DATE;
BEGIN
now := now();
operation = 'Delete';

IF TG_OP ='INSERT' THEN
operation:='Insert'; 
taskid = NEW.task_id;
email = NEW.bidder_email;
ELSIF TG_OP ='UPDATE' THEN
operation = 'Update';
taskid = OLD.task_id;
email = OLD.bidder_email;
ELSE
taskid:=OLD.task_id;
email = OLD.bidder_email;
END IF;
INSERT INTO blog (taskid,email,operation,date) VALUES (taskid,email,operation, now);
RETURN NULL;
END; $$
LANGUAGE PLPGSQL;


CREATE TRIGGER bid_log
AFTER INSERT OR UPDATE OR DELETE
ON bid_task
FOR EACH ROW
EXECUTE PROCEDURE bidlog();


