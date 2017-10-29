<?php

// creates HTML input element, type = "select"
function make_select_from_array($items) {
    echo '<div class="form-group">';
    echo '<label class="col-form-label" for="category">Task Category</label>';
    echo '<select class="form-control custom-select" id="category" name="category">';
    foreach($items as $item) {
        echo "<option value=\"$item\">$item</option>";
    }
    echo '</select></div>';
}
