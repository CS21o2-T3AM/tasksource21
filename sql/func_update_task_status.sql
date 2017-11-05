CREATE OR REPLACE FUNCTION
  update_task_status()
  RETURNS VOID AS $$

DECLARE countcursor NO SCROLL CURSOR FOR
  SELECT
    count(b.*) AS bidcount,
    t.id
  FROM tasks t
    LEFT JOIN bid_task b
      ON b.task_id = t.id
  WHERE t.status = 'open'
        AND t.bidding_deadline < now()
  GROUP BY t.id;

  bidcount BIGINT;
  task_id  INTEGER;

BEGIN
  OPEN countcursor;
  bidcount:=0;
  task_id:=0;
  LOOP
    FETCH countcursor INTO bidcount, task_id;
    EXIT WHEN NOT FOUND;

    IF bidcount > 0
    THEN
      UPDATE tasks
      SET status = 'bidding_closed'
      WHERE id = task_id;

    ELSE
      UPDATE tasks
      SET status = 'closed'
      WHERE id = task_id;

    END IF;
  END LOOP;
  CLOSE countcursor;
END; $$
LANGUAGE PLPGSQL;
