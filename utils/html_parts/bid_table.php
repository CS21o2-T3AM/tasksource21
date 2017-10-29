<?php
function echo_bidding_table_for_bidder($bids, $user_id) {
    if(count($bids) === 0) {
        echo '<span class="text-success">Be the first to bid!</span>';
        return;
    }

    $table_data ='';
    $position = 1;
    $template = '<tr><th>%d</th><td>%s</td><td>%s</td><td>%s</td></tr>';
    foreach($bids as $bid) {
        $bidder_indicator = $bid['bidder_email'] === $user_id ? '<- Your bid' : ''; // only print his identity
        $bid_data = sprintf($template, $position, $bid[DB_BID_AMOUNT], $bid[DB_BID_DATE], $bidder_indicator);
        $table_data .= $bid_data;
        $position += 1;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>#</th><th>Amount</th><th>Bid Date</th><th></th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>

EOT;
    echo $table;
}

function echo_bidding_table_for_owner($bids) {
    $table_data ='';
    $position = 1;
    $template = '<tr><th>%d</th><td>%s</td><td>%s</td><td>%s</td><td>%.1f</td></tr>';
    foreach($bids as $bid) {
        $bid_data = sprintf($template, $position, $bid[DB_BID_AMOUNT], $bid[DB_BID_DATE], $bid[DB_BIDDER], $bid[DB_RATING]);
        $table_data .= $bid_data;
        $position += 1;
    }
    $table = <<< EOT
<table class="table">
    <thead>
        <tr><th>#</th><th>Amount</th><th>bidding time</th><th>Bidder</th><th>Bidder email</th><th>Bidder rating</th><th></th></tr>
    </thead>
    <tbody>
        $table_data
    </tbody>
        
</table>


EOT;
    echo $table;
}