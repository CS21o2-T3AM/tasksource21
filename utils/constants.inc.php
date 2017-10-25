<?php
 /*These constants are for standardizing the keys we use to store values in $_POST, $_SESSION and $_COOKIE*/

// login/register related $_POST array keys
define('PASSWORD', 'password', false);
define('USER_ID', 'userid', false);
define('PASS_CONFIRM', 'confirm', false);
define('DATE_OF_BIRTH', 'dob', false);
define('NAME', 'name', false);
define('CONTACT', 'contact', false);
define('ADMIN', 'admin', false);
define('EMAIL', 'email', false);

define('LOGIN', 'login', false);

// Task related $_POST array keys
define('TASK_NAME', 'task_name', false);
define('TASK_DESC', 'task_desc', false);
define('START_DT', 'start_st', false);
define('END_DT', 'end_dt', false);
define('POSTAL_CODE', 'postal_code', false);
define('ADDRESS', 'address', false);
define('CATEGORY', 'category', false);
define('PRICE', 'price', false);
define('BIDDING_DEADLINE', 'bidding_deadline', false);

define('TASK_ID', 'task_id', false);

// table field names to retrieve values from the table
define('DB_ID', 'id', false);
define('DB_NAME', 'name', false);
define('DB_OWNER', 'owner', false);
define('DB_DESC', 'description', false);
define('DB_CATEGORY', 'category', false);
define('DB_ADDRESS', 'address', false);
define('DB_POSTAL_CODE', 'postal_code', false);
define('DB_START_DT', 'start_datetime', false);
define('DB_END_DT', 'end_datetime', false);
define('DB_PRICE', 'price', false);
define('DB_STATUS', 'status', false);
define('DB_BIDDING_DEADLINE', 'bidding_deadline', false);
define('DB_BID_AMOUNT', 'bid_amount', false);
define('DB_BID_DATE', 'bid_time', false);