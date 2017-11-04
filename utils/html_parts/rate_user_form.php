<?php

function echo_user_rate_form($rating_target, $rate_err) {

    $form = <<< EOT
<div class="col-11 mt-3">
    <form action="" method="POST">
        
        <fieldset about="Rating">
        <legend>Rate $rating_target for this task</legend>
            <div class="form-group">
                <label class="form-control-label" for="bid">Rate 1-5</label>
                    <input class="form-control" name="rating" type="number" step="1" id="bid" max="5" min="1" placeholder="Your rating">
                    <input type="hidden" name="rating_target" value="$rating_target">
                <span class="error text-danger">$rate_err</span>
            </div>
        </fieldset>
        
        <div class="form-group center">
            <input class="btn btn-primary" type="submit" name="rate" value="Submit"/> 
        </div>
    </form>
</div>
EOT;
    echo $form;
}