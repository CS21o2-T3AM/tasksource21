<?php
/* Use these functions to extract the possible task categories from db*/
function get_task_categories($dbh) {
    $statement = 'getting categories';
    $query = 'SELECT * FROM task_categories';

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

// creates HTML input element, type = "select"
function make_select_from_array($array) {
    echo '<div class="form-group">';
    echo '<label class="col-form-label" for="category">Task Category</label>';
    echo '<select class="form-control custom-select" id="category" name="category">';
    foreach($array as $item) {
        echo "<option value=$item>$item</option>";
    }
    echo '</select></div>';
}
