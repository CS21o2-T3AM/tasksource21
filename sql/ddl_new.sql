/* Create tables
  @author Yoshi
*/

/*TODO: need to specify CASCADING, password hashing */

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(64) UNIQUE NOT NULL,
  password CHARACTER(128) NOT NULL, /*hashed pass*/
  name VARCHAR(50) NOT NULL,
  phone VARCHAR(10) NOT NULL,
  is_admin BOOLEAN
);

CREATE TABLE task_categories (
  id INTEGER PRIMARY KEY,
  name VARCHAR(32) UNIQUE NOT NULL,
  description VARCHAR(100) NOT NULL
);

CREATE TYPE TASK_STATUS AS ENUM (
  'pending', 'bid_confirmed', 'completed'
);

CREATE TABLE tasks (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  owner_id INTEGER NOT NULL,
  description VARCHAR(1024) NOT NULL,
  category_id INTEGER NOT NULL,

  postal_code INTEGER NOT NULL,
  address VARCHAR(100) NOT NULL,

  start_datetime TIMESTAMP WITH TIME ZONE NOT NULL,
  end_datetime TIMESTAMP WITH TIME ZONE NOT NULL,

  /* bidding related */
  price MONEY NOT NULL,
  status TASK_STATUS NOT NULL DEFAULT 'pending',
  bidding_deadline TIMESTAMP WITH TIME ZONE, /*NULL if not set by deadline*/

  /* For ordering display. Order created can be inferred from id*/
  datetime_updated TIMESTAMP WITH TIME ZONE NOT NULL,

  FOREIGN KEY (category_id) REFERENCES task_categories(id),
  FOREIGN KEY (owner_id) REFERENCES users(id),
  CHECK (start_datetime <= end_datetime)
);

CREATE TABLE task_ratings (
  task_id INTEGER PRIMARY KEY,
  owner_rating INTEGER,
  doer_rating INTEGER,
  CHECK (owner_rating in (NULL, 1, 2, 3, 4, 5)), /*NULL means not chosen*/
  CHECK (doer_rating in (NULL, 1, 2, 3, 4, 5))
);

CREATE TABLE bid_task (
bidder INTEGER,
task_id INTEGER,
bid_amount MONEY NOT NULL,
bid_time TIMESTAMP WITH TIME ZONE NOT NULL,
is_winner BOOLEAN NOT NULL DEFAULT FALSE,
FOREIGN KEY (bidder) REFERENCES users(id),
FOREIGN KEY (task_id) REFERENCES tasks(id)
);

/* User ratings as task doers. Use WHERE clause to filter out users by email*/
CREATE VIEW taskowner_avg_ratings AS
SELECT AVG(*), t.owner_rating
FROM task_ratings t
GROUP BY t.owner_rating;

/* User ratings as task owners Use WHERE clause to filter out users by email*/
CREATE VIEW taskdoer_avg_ratings AS
SELECT AVG(*), t.doer_rating
FROM task_ratings t
GROUP BY t.doer_rating;

/* The user can see how much he has earned so far */
CREATE VIEW total_earned AS
SELECT SUM(b.bid_amount), b.bidder
FROM bid_task b
WHERE is_winner = TRUE
GROUP BY b.bidder;

