<?php

require_once 'constants.inc.php';

function get_bids_and_ratings($dbh, $task_id) {
    if (!is_numeric($task_id))
        return false;
    $task_id = intval($task_id);
    $statement = 'get all bidding information for $task_id';
    $query = 'SELECT b.bid_amount, b.bid_time, b.bidder_email, u.name, r.rating
                  FROM bid_task AS b
                  JOIN users AS u
                  ON b.bidder_email = u.email
                  JOIN taskdoer_ratings AS r
                  ON r.user = bid_task.bidder_email
                  WHERE b.task_id = $1
                  ORDER BY b.bid_amount, r.rating DESC';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    // copy into array of arrays
    $bids = array();
    while ($row = pg_fetch_assoc($result)) {
        $bids[] = $row;
    }
    return $bids;
}

function find_user_bid_for_task($dbh, $user_email, $task_id) {
    $statement = 'getting user bid for task';
    $query = 'SELECT t.bid_amount
              FROM bid_task t
              WHERE t.bidder_email = $1 AND t.task_id = $2';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $task_id);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0) {
         return 0;
    } else {
        return pg_fetch_assoc($result)[DB_BID_AMOUNT];
    }
}

function bid_for_task($dbh, $user_email, $task_id, $bid_amount) { // handle update too
    $statement = 'check if bid already exists';
    $query = 'SELECT *
              FROM bid_task WHERE
              bidder_email = $1 AND task_id = $2';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $task_id);
    $result = pg_execute($dbh, $statement, $params);
    if (pg_num_rows($result) !== 0) {
        // update
        $statement = 'update bid amount';
        $query = 'UPDATE bid_task set bid_amount = $1
                  WHERE bidder_email = $2 AND task_id = $3';
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($bid_amount, $user_email, $task_id);
        $result = pg_execute($dbh, $statement, $params);
        return $result;
    } else {
        // insert
        $statement = 'insert new bid for task';
        $query = 'INSERT INTO bid_task (bidder_email, task_id, bid_amount, bid_time) 
                  VALUES ($1, $2, $3, current_timestamp)';
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($user_email, $task_id, $bid_amount);
        $result = pg_execute($dbh, $statement, $params);
        return $result;
    }

}

function get_task_by_id($dbh, $task_id) {
    if (!is_numeric($task_id))
        return false;
    $task_id = intval($task_id);
    $statement = 'getting task with given $task_id';
    $query = 'SELECT t.name, t.description, t.postal_code, t.address, t.status, t.bidding_deadline,
                  t.start_datetime, t.end_datetime, t.suggested_price, t.category, t.owner_email
                  FROM tasks t
                  JOIN users u ON t.owner_email = u.email
                  WHERE t.id = $1';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0) {
        return false;
    } else {
        return pg_fetch_assoc($result); // this returns associative array (i.e. dictionary)
    }
}

function get_task_array_or_redirect($dbh) {
    if (empty($_GET[TASK_ID])) {
        header('Location: home.php'); // set message
        exit;
    }
    $task_array = get_task_by_id($dbh, $_GET[TASK_ID]);
    if ($task_array === false) {
        header('Location: home.php'); // set message
        exit;
    }
    return $task_array;
}

function get_bids_for_task($dbh, $task_id, $limit) {
    if (!is_numeric($task_id))
        return false;

    $statement = 'get bidding situation for $task_id';
    $query = 'SELECT b.bid_amount, b.bid_time, b.bidder_email, r.rating
                  FROM bid_task b 
                  LEFT JOIN doer_avg_ratings r
                  ON r.user_email = b.bidder_email
                  WHERE b.task_id = $1
                  ORDER BY b.bid_amount DESC LIMIT $2';

    // we are not going to show who the voters are?
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id, $limit);
    $result = pg_execute($dbh, $statement, $params);
    // copy into array of arrays
    $bids = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_BID_DATE]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_BID_DATE] = $bid_time;
        $bids[] = $row;
    }
    return $bids;
}

function get_task_categories($dbh) {
    $statement = 'getting categories';
    $query = 'SELECT tc.name
              FROM task_categories tc';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array();
    $result = pg_execute($dbh, $statement, $params);

    if ($result === false)
        return false;

    $categories = array();
//    foreach(pg_fetch_row($result) as $category) { // this doesn't work: iterates only once since
    // it does not iterate through pg_fetch_row more than once
//        $categories[] = $category;
//    }
    while ($row = pg_fetch_row($result)) {
        $categories[] = $row[0];
    }

    return $categories;
}

function insert_new_task($dbh, $params) {
    if (count($params) !== 10)
        return false;

    $statement = 'inserting the task into db';
    $query = 'INSERT INTO tasks (name, owner_email, description, category, postal_code, address,
              TIMESTAMP start_datetime, TIMESTAMP end_datetime, suggested_price, TIMESTAMP bidding_deadline)
              VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';
    $result = pg_prepare($dbh, $statement, $query);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

function update_task($dbh, $params) {
    $statement = 'inserting the task into db';
    $query = 'UPDATE tasks SET name = $1, owner_email = $2, description = $3, category = $4, postal_code = $5, address = $6,
              start_datetime = $7, end_datetime = $8, suggested_price = $9, bidding_deadline = $10
              WHERE id = $11';
    $result = pg_prepare($dbh, $statement, $query);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

// returns false if database connection fails, 0 if no user, 1 if non-admin user, 2 if admin
function check_user_login($dbh, $user_email, $password) {
    $password_hash = hash('sha256', $password, false);
    $statement = 'selecting user';

    $query = 'SELECT u.is_admin
              FROM users u 
              WHERE u.email = $1 AND u.password_hash = $2';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $password_hash);
    $result = pg_execute($dbh, $statement, $params);
    if ($result === false)
        return false;
    if (pg_num_rows($result) === 0)
        return 0;
    $is_admin = pg_fetch_assoc($result)[ADMIN];
    if ($is_admin === 'f')
        return 1;
    else
        return 2;
}

function get_assigned_user($dbh, $task_id) {
    $statement = 'getting the user assigned to task';
    $query = 'SELECT b.bidder_email
              FROM bid_task b
              WHERE b.task_id = $1 AND b.is_winner = TRUE';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    if ($result === false)
        return false;
    if (pg_num_rows($result) === 0)
        return false;

    return pg_fetch_array($result)[DB_BIDDER];
}

function insert_new_user($dbh, $params) {
    if (count($params) !== 4)
        return false;

    $statement = 'inserting new user';
    $query = 'INSERT INTO users (email, password_hash, name, phone) VALUES ($1, $2, $3, $4)';
    $result = pg_prepare($dbh, $statement, $query);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

function check_user_not_exist($dbh, $email) {
    $statement = 'checking for duplicate user';
    $query = 'SELECT * FROM users WHERE email=$1';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($email);
    $result = pg_execute($dbh, $statement, $params);
    if ($result === false)
        die('problem with database');

    return pg_numrows($result) === 0;
}

function get_owner_rating($dbh, $owner_id) {
    $statement = 'get owner rating';
    $query = 'SELECT r.rating
              FROM tasker_avg_rating r 
              WHERE r.user = $1';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($owner_id);
    $result = pg_execute($dbh, $statement, $params);
    if ($result === false)
        die('problem with database');

    return pg_numrows($result)[DB_RATING];
}

