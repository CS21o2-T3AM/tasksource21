<?php

require_once '../utils/constants.inc.php';
function echo_bid_form($bid_err) {
    if (!empty($bid_err)) {
        $danger_form = 'has-danger';
        $form_control_class = 'form-control-danger';
    } else {
        $danger_form = '';
        $form_control_class = '';
    }

    $form = <<< EOT
<form action="" method="POST">
    
    <fieldset about="Bidding">
        <div class="form-group $danger_form">
            <label class="form-control-label" for="bid">Bid for this task: </label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input class="form-control $form_control_class" type="number" step="0.01" id="bid" name="bid" placeholder="Place your bid">
            </div>
            <span class="error text-danger">$bid_err</span>
        </div>
    </fieldset>
    
    <div class="form-group center">
        <input class="btn btn-primary" type="submit" name="submit" value="Submit"/>
    </div>
</form>
EOT;
    echo $form;
}

function echo_user_bid($bid) {
    $message = "<span class=\"error text-success\">Your bid for this task is $bid</span>";
    echo $message;
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
