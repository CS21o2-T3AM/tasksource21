/* tables */

CREATE TABLE users (
  email VARCHAR(64) PRIMARY KEY,
  password_hash CHARACTER(256) NOT NULL,
  name VARCHAR(50) NOT NULL,
  phone CHARACTER(8) NOT NULL, /* Singapore hand phone */
  is_admin BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE task_categories (
  name VARCHAR(32) PRIMARY KEY,
  description VARCHAR(100) NOT NULL
);
/*Minor Repair, House Cleaning, Home Improvement, Furniture Assembly, Moving and Packing*/

/* This enum will fall under "Types" in pgAdmin window */
CREATE TYPE TASK_STATUS AS ENUM (
  'open', 'bidding_closed', 'assigned', 'closed'
);

CREATE TABLE tasks (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  owner_email VARCHAR(64) NOT NULL,
  description VARCHAR(1024) NOT NULL,
  category VARCHAR(32) NOT NULL,

  postal_code CHARACTER(6) NOT NULL, /* Singapore postal code */
  address VARCHAR(100) NOT NULL,

  start_datetime TIMESTAMP WITH TIME ZONE NOT NULL,
  end_datetime TIMESTAMP WITH TIME ZONE NOT NULL,

  suggested_price MONEY NOT NULL, /* suggested, that is, the owner sets up a price for user reference */
  status TASK_STATUS NOT NULL DEFAULT 'open',
  bidding_deadline TIMESTAMP WITH TIME ZONE NOT NULL,

  datetime_updated TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

  FOREIGN KEY (category) REFERENCES task_categories(name) ON DELETE CASCADE ,
  FOREIGN KEY (owner_email) REFERENCES users(email) ON DELETE CASCADE ,
  CHECK (bidding_deadline < start_datetime),
  CHECK (start_datetime < end_datetime),
  CHECK (suggested_price >= cast(0 as MONEY))
);

CREATE TABLE task_ratings (
  task_id INTEGER,
  user_email VARCHAR(64),
  rating INTEGER,
  role VARCHAR(6),
  CHECK (rating in (1, 2, 3, 4, 5)),
  CHECK (role in ('tasker', 'doer')),
  FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  PRIMARY KEY(task_id, user_email)
);

CREATE TABLE bid_task (
  bidder_email VARCHAR(64),
  task_id INTEGER,
  bid_amount MONEY NOT NULL,
  bid_time TIMESTAMP WITH TIME ZONE NOT NULL,
  is_winner BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (bidder_email) REFERENCES users(email) ON DELETE CASCADE ,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  PRIMARY KEY (task_id, bidder_email),
  CHECK(bid_amount > cast(0 as money))
);

/* VIEWS */

/* User ratings as task doers. Use WHERE clause to filter out users by email */
CREATE VIEW tasker_avg_ratings AS
  SELECT AVG(t.rating) AS avg, COUNT(t.rating) AS count, t.user_email
  FROM task_ratings t
  WHERE role = 'tasker'
  GROUP BY t.user_email;

CREATE VIEW doer_avg_ratings AS
  SELECT AVG(t.rating) AS avg, COUNT(t.rating) AS count, t.user_email
  FROM task_ratings t
  WHERE role = 'doer'
  GROUP BY t.user_email;
