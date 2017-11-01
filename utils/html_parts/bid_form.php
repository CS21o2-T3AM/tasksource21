<?php

require_once '../utils/constants.inc.php';
function echo_bid_form($bid_err, $bid) {
    if (!empty($bid_err)) {
        $danger_form = 'has-danger';
        $form_control_class = 'form-control-danger';
    } else {
        $danger_form = '';
        $form_control_class = '';
    }

    $bid_message = $bid === 0? 'You have not bidded for this task.' : "Your bid for this task is <b>$bid</b>";

    $bid_delete_button = '';
    if ($bid !== 0) { // if the user has placed any bid
        $bid_delete_button = ' or <input type="submit" class=" btn" value="Withdraw bid" name="withdraw">';
    }

    $form = <<< EOT
<div class="col-11 mt-3">
    <form action="" method="POST">
        
        <fieldset about="Bidding">
            <div class="form-group $danger_form">
                <label class="form-control-label text-success" for="bid">$bid_message</label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input class="form-control $form_control_class" type="number" step="0.01" id="bid" name="bid" placeholder="Place your bid">
                </div>
                <span class="error text-danger">$bid_err</span>
            </div>
        </fieldset>
        
        <div class="form-group center">
            <input class="btn btn-primary" type="submit" name="submit" value="Submit"/> 
            $bid_delete_button
        </div>
    </form>
</div>
EOT;
    echo $form;
}

function echo_assigned_user($assigned_user_email, $task_owner_email) {
    $current_user_email = $_SESSION[EMAIL];
    $assigned = $assigned_user_email;
    if ($assigned_user_email === $current_user_email) {
        $assigned = 'you!';
    } else if ($assigned_user_email !== $task_owner_email) {
        // if not the task owner, don't show who won it
        $assigned = 'another user';
    }
    $message = "<span class=\"error text-success\">This task is assigned to $assigned</span>";
    echo $message;

}
