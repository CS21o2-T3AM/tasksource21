/*
 * This function closes tasks whose start_datetime has reached
   open -> bidding_closed | closed (if no bidding)
   bidding_closed -> assigned | closed (if no doer selected before deadline)
   Call this BEFORE update_task_status
 */

CREATE OR REPLACE FUNCTION
  close_past_task()
  RETURNS VOID AS $$

DECLARE closecursor NO SCROLL CURSOR FOR
  SELECT t.id
  FROM tasks t
  WHERE (t.status = 'assigned' OR t.status = 'bidding_closed')
        AND start_datetime < (SELECT date_trunc('minute', now()) cur_day_start);
  task_id INTEGER;
BEGIN
  OPEN closecursor;
  task_id:=0;
  LOOP
    FETCH closecursor INTO task_id;
    EXIT WHEN NOT FOUND;

    UPDATE tasks
    SET status = 'closed'
    WHERE id = task_id;

  END LOOP;
  CLOSE closecursor;

END; $$
LANGUAGE PLPGSQL;
