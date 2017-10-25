<?php
    /*
     * Use:
     * file to be required for database connection.
     */

    /*
     * Note:
     * require and include are only different in the way they handle errors. required file missing
     * will throw a fatal exception while include will simply continue running without using that file
     */

    $dbname = 'tasksource21';
    $port = 5432;
    $host = 'localhost';
    $pass = 'password';
    $user = 'postgres';

    // in php, variable names inside double quotes get interpreted, whereas in single quotes
    // the string is literal, just like bash
    $dsn = "host=$host port=$port dbname=$dbname user=$user password=$pass";

    $dbh = pg_connect($dsn) /*This $dbh variable is available after this file is required_once */
    or die('Could not connect to the database\n');

    function get_task_by_id($dbh, $task_id) {
        if (!is_numeric($task_id))
            return false;
        $task_id = intval($task_id);
        $statement = 'getting task with given $task_id';
        $query = 'SELECT t.name, t.description, tc.name, t.postal_code, t.address,
                  t.start_datetime, t.end_datetime, t.price, tc.name, u.name
                  FROM task_categories AS tc JOIN tasks AS t ON t.category_id = tc.id
                  JOIN users AS u ON t.owner_id = u.id
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

    function get_bids_for_task($dbh, $task_id, $limit) {
        if (!is_numeric($task_id))
            return false;

        $task_id = intval($task_id);
        $statement = 'get bidding situation for $task_id';
        $query = 'SELECT b.bid_amount, b.bid_time
                  FROM bid_task AS b
                  WHERE b.task_id = $1
                  ORDER BY bid_amount DESC LIMIT $2';

        // we are not going to show who the voters are?
        $result = pg_prepare($dbh, $statement, $query);
        $params = array($task_id, $limit);
        $result = pg_execute($dbh, $statement, $params);

        // copy into array of arrays
        $bids = array();
        foreach (pg_fetch_assoc($result) as $row) {
            $bids[] = $row;
        }
        return $bids;
    }

    function create_bidding_table($bids) {
        if(count($bids) === 0) {
            echo 'Be the first to bid!';
            return;
        }

        $table_data ='';
        $position = 1;
        $template = '<tr><th>%d</th><td>$%.2f</td><td>%s</td></tr>';
        foreach($bids as $bid) {
            $bid_data = sprintf($template, $position, $bid[DB_BID_AMOUNT], $bid[DB_BID_DATE]);
            $table_data .= $bid_data;
            $position += 1;
        }
        $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>#</th><th>Amount</th><th>Bid Date</th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>


EOT;
        echo $table;
    }

    function get_task_categories($dbh) {
        $statement = 'getting categories';
        $query = 'SELECT tc.name
                  FROM task_categories AS tc';

        $result = pg_prepare($dbh, $statement, $query);
        $params = array();
        $result = pg_execute($dbh, $statement, $params);

        if ($result === false)
            return false;

        $categories = array();
        foreach(pg_fetch_row($result) as $category) {
            $categories[] = $category;
        }

        return $categories;
    }
?>