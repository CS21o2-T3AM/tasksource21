/* 1. UNION */
SELECT t.id, t.name, t.bidding_deadline AS date, t.status
FROM tasks t
WHERE t.owner_email = $1
      AND t.bidding_deadline > now() AND t.status = 'open'

UNION SELECT t2.id, t2.name, t2.start_datetime, t2.status
      FROM tasks t2
      WHERE t2.owner_email = $1
            AND t2.start_datetime > now() AND t2.status = 'bidding_closed'
ORDER BY date ASC;

/* 2. LEFT JOIN */
SELECT b.bid_amount, b.bid_time, b.bidder_email,
  r.avg, r.count
FROM bid_task b
  LEFT JOIN
  doer_avg_ratings r
    ON r.user_email = b.bidder_email
WHERE b.task_id = $1
ORDER BY b.bid_amount DESC;

/* Nested */
SELECT t.id, t.name, t.start_datetime
FROM tasks t
WHERE t.id IN
(SELECT b.task_id
FROM bid_task b
WHERE b.bidder_email = $1 AND b.is_winner = TRUE)
AND t.start_datetime > now() AND t.status = 'assigned'
ORDER BY t.start_datetime ASC;