<?php
 /*These constants are for standardizing the keys we use to store values in $_POST, $_SESSION and $_COOKIE*/

// login/register related $_POST array keys
define('PASSWORD', 'password', false);
define('USER_ID', 'userid', false);
define('PASS_CONFIRM', 'confirm', false);
define('NAME', 'name', false);
define('CONTACT', 'contact', false);
define('ADMIN', 'is_admin', false);
define('EMAIL', 'email', false);

define('LOGIN', 'login', false);

// Task related $_POST array keys
define('TASK_NAME', 'task_name', false);
define('TASK_DESC', 'task_desc', false);
define('START_DT', 'start_dt', false);
define('END_DT', 'end_dt', false);
define('POSTAL_CODE', 'postal_code', false);
define('ADDRESS', 'address', false);
define('CATEGORY', 'category', false);
define('PRICE', 'price', false);
define('BIDDING_DEADLINE', 'bidding_deadline', false);
define('BID', 'bid', false);

define('TASK_ID', 'task_id', false);

// table field names to retrieve values from the table
define('DB_ID', 'id', false);
define('DB_NAME', 'name', false);
define('DB_OWNER', 'owner_email', false);
define('DB_DESC', 'description', false);
define('DB_CATEGORY', 'category', false);
define('DB_ADDRESS', 'address', false);
define('DB_POSTAL_CODE', 'postal_code', false);
define('DB_START_DT', 'start_datetime', false);
define('DB_END_DT', 'end_datetime', false);
define('DB_SUGGESTED_PRICE', 'suggested_price', false);
define('DB_STATUS', 'status', false);
define('DB_BIDDING_DEADLINE', 'bidding_deadline', false);
define('DB_BID_AMOUNT', 'bid_amount', false);
define('DB_BID_TIME', 'bid_time', false);
define('DB_BIDDER', 'bidder_email', false);
define('DB_RATING', 'rating', false);

define('DB_DATE', 'date', false); // some generic field name to be assigned
define('DB_AVG', 'avg', false);
define('DB_COUNT', 'count', false);

define('STATUS_CLOSED', 'closed', false);
define('STATUS_OPEN', 'open', false);
define('STATUS_BIDDING_CLOSED', 'bidding_closed', false);
define('STATUS_ASSIGNED', 'assigned', false);

// search fields
define('TASK_KEYWORDS', 'task_keywords', false);
define('ADDRESS_KEYWORDS', 'address_keywords', false);
define('PRICE_MAX', 'max_price', false);
define('PRICE_MIN', 'min_price', false);
define('CHECK_ADDRESS_KEYWORDS', 'check_address_keywords', false);
define('CHECK_TASK_KEYWORDS', 'check_task_keywords', false);
define('CHECK_PRICE_MIN', 'check_min_price', false);
define('CHECK_PRICE_MAX', 'check_max_price', false);
define('CHECK_START_DATE', 'check_start_datetime', false);
define('CHECK_CATEGORY', 'check_category', false);