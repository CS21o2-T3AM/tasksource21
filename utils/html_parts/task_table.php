<?php

function echo_tasks_table_all($tasks) {
    if(count($tasks) === 0) {
        echo '<span class="text-success">There is currently no tasks open for bidding</span>';
        return;
    }

    $table_data ='';
    $template = '<tr><td><a href="view_task.php?task_id=%d">%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
    foreach($tasks as $task) {
        $task_data = sprintf($template, $task[DB_ID], $task[DB_NAME], $task[DB_CATEGORY], $task[DB_SUGGESTED_PRICE], $task[DB_BIDDING_DEADLINE], $task[DB_START_DT]);
        $table_data .= $task_data;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>Task Name</th><th>Category</th><th>Suggested price</th><th>Bidding deadline</th><th>Start date</th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}

function echo_table_completed_tasks($tasks) {
    if(count($tasks) === 0) {
        echo '<span class="text-success">You have not assigned or been assigned for any tasks yet</span>';
        return;
    }

    $table_data ='';
    $template = '<tr><td><a href="view_task.php?task_id=%d">%s</a></td><td>%s</td><td>%s</td></tr>';
    foreach($tasks as $task) {
        $remark = '';
        $task_data = sprintf($template, $task[DB_ID], $task[DB_NAME], $task[DB_DATE], $remark);
        $table_data .= $task_data;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>Task Name</th><th>Completed Date</th><th>Remark</th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}

function echo_table_assigned_tasks($tasks) {
    if(count($tasks) === 0) {
        echo '<span class="text-success">You have no pending tasks</span>';
        return;
    }

    $table_data ='';
    $template = '<tr><td><a href="view_task.php?task_id=%d">%s</a></td><td>%s</td></tr>';
    foreach($tasks as $task) {
        $task_data = sprintf($template, $task[DB_ID], $task[DB_NAME], $task[DB_START_DT]);
        $table_data .= $task_data;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>Task Name</th><th>Task start Date</th><th></th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}

function echo_table_bidding_tasks($tasks) {
    if(count($tasks) === 0) {
        echo '<span class="text-success">You have no bidding in progress</span>';
        return;
    }

    $table_data ='';
    $template = '<tr><td><a href="view_task.php?task_id=%d">%s</a></td><td>%s</td></tr>';
    foreach($tasks as $task) {
        $task_data = sprintf($template, $task[DB_ID], $task[DB_NAME], $task[DB_BIDDING_DEADLINE]);
        $table_data .= $task_data;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>Task Name</th><th>Bidding Deadline</th><th></th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}

function echo_table_created_tasks($tasks) {
    if(count($tasks) === 0) {
        echo '<span class="text-success">You have not created any tasks</span>';
        return;
    }

    $table_data ='';
    $template = '<tr><td><a href="view_task.php?task_id=%d">%s</a></td><td>%s</td><td>%s</td></tr>';
    foreach($tasks as $task) {
        // get task status
        $remark = $task[DB_STATUS] === 'open'? 'bidding in progress': 'assign a doer';
        $task_data = sprintf($template, $task[DB_ID], $task[DB_NAME], $task[DB_DATE], $remark);
        $table_data .= $task_data;
    }

    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>Task Name</th><th>Bidding / Assignment deadline</th><th>Remark</th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}
