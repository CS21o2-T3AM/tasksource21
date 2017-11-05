<?php

require_once 'constants.inc.php';

function run_update_function($dbh) {
    /* bidding_deadline reached (open) -> bidding_closed | closed */
    $statement = 'run functions to update status';
    $function1 = 'SELECT update_task_status()';
    $result = pg_prepare($dbh, $statement, $function1);
    $params = array();
    $result = pg_execute($dbh, $statement, $params);

    /* task start_datetime reached (assigned | bidding_closed) -> closed */
    $statement = 'run functions to close status';
    $function2 = 'SELECT close_past_task()';
    $result = pg_prepare($dbh, $statement, $function2);
    $params = array();
    $result = pg_execute($dbh, $statement, $params);
}

function get_all_open_tasks($dbh, $task_keywords, $address_keywords, $start_dt, $max_price, $min_price, $category) {
    $statement = 'getting open tasks satisfying filter';
    $query = 'SELECT t.id, t.name, t.bidding_deadline,
              t.start_datetime, t.suggested_price, t.category
              FROM tasks t
              WHERE t.status = \'open\' ';

    if (!empty($task_keywords)) {
        $keywords_array = explode(' ', $task_keywords);
        foreach($keywords_array as $keyword) {
            $query .= "AND (t.name LIKE '%$keyword%' OR t.description LIKE '%$keyword%') ";
        }
    }

    if (!empty($address_keywords)) {
        $keywords_array = explode(' ', $address_keywords);
        foreach($keywords_array as $keyword) {
            $query .= "AND t.address LIKE '%$keyword%' ";
        }
    }

    if (!empty($category)) {
        $query .= "AND t.category = '$category' ";
    }

    if (!empty($start_dt)) {
        $php_to_postgres_format = 'Y-m-d H:i:s';
        $start_dt_convert = new DateTime($start_dt);
        $start_dt = $start_dt_convert->format($php_to_postgres_format);
        $query .= "AND t.start_datetime > '$start_dt' ";
    }

    if (!empty($min_price)) {
        $min_price = floatval($min_price);
        $query .= "AND t.suggested_price > cast($min_price AS money) ";
    }

    if (!empty($max_price)) {
        $max_price = floatval($max_price);
        $query .= "AND t.suggested_price < cast($max_price AS money) ";
    }

    $query .= 'ORDER BY t.bidding_deadline ASC';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array();
    $result = pg_execute($dbh, $statement, $params);

    $tasks = array();
    while($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_START_DT]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_START_DT] = $bid_time;
        $bid_time = new DateTime($row[DB_BIDDING_DEADLINE]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_BIDDING_DEADLINE] = $bid_time;
        $tasks[] = $row;
    }
    return $tasks;
}

function get_task_by_id($dbh, $task_id) {
    $statement = 'getting task with given $task_id';
    $query = 'SELECT t.name, t.description, t.postal_code, t.address, t.status, t.bidding_deadline,
              t.start_datetime, t.end_datetime, t.suggested_price, t.category, t.owner_email
              FROM tasks t
              WHERE t.id = $1';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0) {
        return false;
    }

    $task_array = pg_fetch_assoc($result); // this returns associative array (i.e. dictionary)
    return $task_array;
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

function get_task_categories($dbh) {
    $statement = 'getting categories';
    $query = 'SELECT tc.name
              FROM task_categories tc';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array();
    $result = pg_execute($dbh, $statement, $params);

    $categories = array();
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
              start_datetime, end_datetime, suggested_price, bidding_deadline)
              VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';
    $result = pg_prepare($dbh, $statement, $query);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

function update_task($dbh, $params) {
    if (count($params) !== 10)
        return false;
    $statement = 'update task info';
    $query = 'UPDATE tasks SET name = $1, owner_email = $2, description = $3, category = $4, postal_code = $5, address = $6,
              start_datetime = $7, end_datetime = $8, suggested_price = $9, bidding_deadline = $10
              WHERE id = $11';
    $result = pg_prepare($dbh, $statement, $query);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

function delete_task($dbh, $task_id) {
    $statement = 'close task';
    $query = 'UPDATE tasks
              SET status = \'closed\'
              WHERE id = $1';
    $result = pg_prepare($dbh, $statement, $query);
    $param = array($task_id);
    $result = pg_execute($dbh, $statement, $param);
    return $result;
}

function get_assigned_user($dbh, $task_id) {
    $statement = 'get the user assigned to task';
    $query = 'SELECT b.bidder_email
              FROM bid_task b
              WHERE b.task_id = $1 AND b.is_winner = TRUE';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0)
        return false;

    return pg_fetch_array($result)[DB_BIDDER];
}

// =================================== for login/user related ====================================== //

// returns 0 if no user, 1 if non-admin user, 2 if admin
function check_user_login($dbh, $user_email, $password) {
    $password_hash = hash('sha256', $password, false);
    $statement = 'selecting user';

    $query = 'SELECT u.is_admin
              FROM users u 
              WHERE u.email = $1 AND u.password_hash = $2';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $password_hash);
    $result = pg_execute($dbh, $statement, $params);
    if (pg_num_rows($result) === 0)
        return 0;
    $is_admin = pg_fetch_assoc($result)[ADMIN];
    if ($is_admin === 'f')
        return 1;
    else
        return 2;
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

    return pg_numrows($result) === 0;
}

// =================================== for home.php ====================================== //

function get_tasks_in_bidding($dbh, $user_id) {
    $statement = 'get tasks user is currently bidding for';
    $query = 'SELECT t.id, t.name, t.bidding_deadline
              FROM tasks t
              WHERE t.id IN
              (SELECT b.task_id
              FROM bid_task b
              WHERE b.bidder_email = $1)
              AND t.bidding_deadline > now() AND t.status = \'open\'
              ORDER BY t.bidding_deadline ASC';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id);
    $result = pg_execute($dbh, $statement, $params);

    $tasks = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_BIDDING_DEADLINE]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_BIDDING_DEADLINE] = $bid_time;
        $tasks[] = $row;
    }
    return $tasks;
}

function get_tasks_created($dbh, $user_id) {
    $statement = 'get tasks user has created that is in bidding stage or assignment stage';

    // USE UNION to merge bidding_deadline and start_datetime
    $query = 'SELECT t.id, t.name, t.bidding_deadline AS date, t.status
              FROM tasks t
              WHERE t.owner_email = $1
              AND t.bidding_deadline > now() AND t.status = \'open\'
              
              UNION SELECT t2.id, t2.name, t2.start_datetime, t2.status
              FROM tasks t2
              WHERE t2.owner_email = $1
              AND t2.start_datetime > now() AND t2.status = \'bidding_closed\'
              ORDER BY date ASC';

    // problem is that start_datetime > now() eliminates ones

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id);
    $result = pg_execute($dbh, $statement, $params);

    $tasks = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_DATE]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_DATE] = $bid_time;
        $tasks[] = $row;
    }
    return $tasks;
}

function get_tasks_assigned($dbh, $user_id) {
    $statement = 'get tasks assigned to the user and has to be done in the future';
    $query = 'SELECT t.id, t.name, t.start_datetime
              FROM tasks t
              WHERE t.id IN
              (SELECT b.task_id
              FROM bid_task b
              WHERE b.bidder_email = $1 AND b.is_winner = TRUE)
              AND t.start_datetime > now() AND t.status = \'assigned\'
              ORDER BY t.start_datetime ASC';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id);
    $result = pg_execute($dbh, $statement, $params);

    $tasks = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_START_DT]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_START_DT] = $bid_time;
        $tasks[] = $row;
    }
    return $tasks;
}

function get_tasks_complete($dbh, $user_id) {
    $statement = 'get previous tasks completed/submitted by the user';
    $query = 'SELECT t.id, t.name, t.start_datetime AS date, t.owner_email
              FROM tasks t
              WHERE 
              (
              /* task successfully bidded by the user */
              t.id IN
              (SELECT b.task_id
              FROM bid_task b
              WHERE b.bidder_email = $1 AND is_winner = TRUE)
              
              /* or task created by the user for which there exists a s successful bidder*/
              OR 
              (t.owner_email = $1
              AND EXISTS (SELECT * FROM
              bid_task b2
              WHERE b2.task_id = t.id AND b2.is_winner = TRUE))
              )
              
              /* either case, it has to be a closed task */
              AND t.start_datetime < now() AND t.status = \'closed\'
              ORDER BY date ASC
              ';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id);
    $result = pg_execute($dbh, $statement, $params);

    $tasks = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_DATE]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_DATE] = $bid_time;
        $tasks[] = $row;
    }
    return $tasks;
}

// ================================== rating related ======================================= //

function get_user_avg_rating($dbh, $user_id, $role) {
    if ($role !== 'tasker' && $role !== 'doer')
        return false;

    $statement = 'get user rating';
    $query = 'SELECT AVG(r.rating) AS avg, COUNT(r.rating) AS count
              FROM task_ratings r
              WHERE r.user_email = $1 AND r.role = $2
              GROUP BY r.user_email';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id, $role);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0)
        return 'N/A';

    $row = pg_fetch_assoc($result);
    return sprintf('%.2f/5 (%d)', $row[DB_AVG], $row[DB_COUNT]);
}

function get_user_rating_for_task($dbh, $task_id, $user_email) {
    $statement = 'get rating for a particular task';
    $query = 'SELECT r.rating
              FROM task_ratings r
              WHERE r.user_email = $1
              AND r.task_id = $2';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $task_id);
    $result = pg_execute($dbh, $statement, $params);

    if (pg_num_rows($result) === 0)
        return 0;

    $row = pg_fetch_assoc($result);
    return intval($row['rating']);
}

function insert_user_rating_for_task($dbh, $task_id, $user_email, $role, $rating) {
    $statement = 'insert user rating for a task';
    $query = 'INSERT INTO task_ratings
              (task_id, user_email, rating, role) VALUES ($1, $2, $3, $4)';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id, $user_email, $rating, $role);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

// =================================== bidding related ====================================== //

function get_bids_and_ratings($dbh, $task_id, $limit) {
    $limit_query = '';
    if ($limit !== false) {
        $limit_query = ' LIMIT $2';
    }
    $statement = 'get all bidding information for $task_id';
    $query = 'SELECT b.bid_amount, b.bid_time, b.bidder_email, avg_rating.avg, avg_rating.count
                  FROM bid_task b
                  LEFT JOIN 
                  (SELECT AVG(r.rating) AS avg, COUNT(r.rating) AS count, r.user_email
                  FROM task_ratings r 
                  GROUP BY r.user_email) AS avg_rating
                  ON avg_rating.user_email = b.bidder_email
                  WHERE b.task_id = $1
                  ORDER BY b.bid_amount ASC';
    $query .= $limit_query;

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    if ($limit !== false) {
        $params[] = $limit;
    }

    $result = pg_execute($dbh, $statement, $params);

    // copy into array of arrays
    $bids = array();
    while ($row = pg_fetch_assoc($result)) {
        $bid_time = new DateTime($row[DB_BID_TIME]);
        $bid_time = $bid_time->format('H:i d M Y');
        $row[DB_BID_TIME] = $bid_time;
        $bids[] = $row;
    }
    return $bids;
}

function withdraw_bid($dbh, $user_id, $task_id) {
    $statement = 'delete bid';
    $query = 'DELETE FROM bid_task
              WHERE bidder_email = $1 AND task_id = $2';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_id, $task_id);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

function bid_for_task($dbh, $user_email, $task_id, $bid_amount) {
    $statement = 'check if bid already exists';
    $query = 'SELECT *
              FROM bid_task 
              WHERE bidder_email = $1 AND task_id = $2';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $task_id);
    $result = pg_execute($dbh, $statement, $params);
    if (pg_num_rows($result) !== 0) {
        // update
        $statement = 'update bid amount';
        $query = 'UPDATE bid_task 
                  set bid_amount = $1, bid_time = now()
                  WHERE bidder_email = $2 AND task_id = $3';
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($bid_amount, $user_email, $task_id);
        $result = pg_execute($dbh, $statement, $params);
        return $result;
    } else {
        // insert
        $statement = 'insert new bid for task';
        $query = 'INSERT INTO bid_task (bidder_email, task_id, bid_amount, bid_time) 
                  VALUES ($1, $2, $3, now())';
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($user_email, $task_id, $bid_amount);
        $result = pg_execute($dbh, $statement, $params);
        return $result;
    }
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

function set_as_winner($dbh, $user_email, $task_id) {
    $statement = 'set user as the winner';
    $query = 'UPDATE bid_task
              SET is_winner = TRUE
              WHERE bidder_email = $1 AND task_id = $2';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($user_email, $task_id);
    $result = pg_execute($dbh, $statement, $params);

    $statement = 'update task status to assigned';
    $query = 'UPDATE tasks
              SET status = \'assigned\'
              WHERE id = $1';
    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);

    return $result;
}

function close_task($dbh, $task_id) {
    $statement = 'close task';
    $query = 'UPDATE tasks
              SET status = \'closed\'
              WHERE id = $1';

    $result = pg_prepare($dbh, $statement, $query);
    $params = array($task_id);
    $result = pg_execute($dbh, $statement, $params);
    return $result;
}

