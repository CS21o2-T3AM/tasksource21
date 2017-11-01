<?php
function echo_bidding_board($bids) {
    if(count($bids) === 0) {
        echo '<div class="text-warning col-11 mt-3 mb-3"><h5>No one has bid yet</h5></div>';
        return;
    }

    $table_data ='';
    $position = 1;
    $template = '<tr><th>%d</th><td>%s</td><td>%s</td><td>%s</td></tr>';
    foreach($bids as $bid) {
        $rating = !empty($bid[DB_AVG]) ? sprintf('%.2f/5 (%d)', $bid[DB_AVG], $bid[DB_COUNT])  : 'N/A';
        $bid_data = sprintf($template, $position, $bid[DB_BID_AMOUNT], $bid[DB_BIDDER], $rating);
        $table_data .= $bid_data;
        $position += 1;
    }
    $table = <<< EOT
    <table class="table">
        <thead>
            <tr><th>#</th><th>Amount</th><th>Bidder</th><th>Bidder rating</th></tr>
        </thead>
        <tbody>
            $table_data
        </tbody>
            
    </table>
</form>
EOT;
    echo $table;
}

function echo_bidding_table_form($bids) {
    if(count($bids) === 0) {
        echo '<div class="text-danger col-11">No bid was submitted for this task. Click below to close this task';
        echo '<form action="" method="POST"><input class="btn" type="submit" value="Close" name="close"></form></div>';
        return;
    }

    $table_data = '';
    $position = 1;
    $template = '<tr><th>%d</th><td>%s</td><td>%s</td><td>%s</td>';
    foreach($bids as $bid) {
        $rating = !empty($bid[DB_AVG]) ? sprintf('%.2f/5 (%d)', $bid[DB_AVG], $bid[DB_COUNT])  : 'N/A';
        $bid_data = sprintf($template, $position, $bid[DB_BID_AMOUNT], $bid[DB_BIDDER], $rating);
        $bid_data .= '<td><input type="radio" name="winner" value="'.urlencode($bid[DB_BIDDER]).'"></td></tr>';
        $table_data .= $bid_data;
        $position += 1;
    }

    $table = <<< EOT
<form action="" method="POST">
    <table class="table">
        <thead>
            <tr><th>#</th><th>Amount</th><th>Bidder</th><th>Bidder rating</th></tr>
        </thead>
        <tbody>
            $table_data
        </tbody>
            
    </table>
    
    <div class="form-group center">
        <input class="btn btn-primary" type="submit" name="submit" value="submit"> or 
        <input class="btn" type="submit" name="close" value="close">
    </div>
</form>
EOT;
    echo $table;
}