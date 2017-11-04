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

// creates HTML input element, type = "select"
function make_select_from_array_with_checkbox($items) {
    echo '<div class="form-group">';
    echo '<span class="float-right">
          <input type="checkbox" name="check_category"></span>';
    echo '<label class="col-form-label" for="category">Task Category</label>';
    echo '<select class="form-control custom-select" id="category" name="category">';
    foreach($items as $item) {
        echo "<option value=\"$item\">$item</option>";
    }
    echo '</select></div>';
}
