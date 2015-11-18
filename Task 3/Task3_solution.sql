-- 1.
-- The subquery returns the largest number of logouts, and
-- the outer query gets user(s) whose number of logouts is equal to the largest number
-- returned from the subquery
SELECT user_id, COUNT(*) total_logouts
FROM login_history
WHERE login_action = 'logout' AND DAYNAME(login_time) = 'Wednesday' AND MONTHNAME(login_time) = 'September' AND YEAR(login_time) = '2012'
GROUP BY user_id
HAVING total_logouts = ( SELECT MAX(temp.total_logouts)   
						 FROM ( SELECT user_id, COUNT(*) total_logouts
                                FROM login_history
                                WHERE login_action = 'logout' AND DAYNAME(login_time) = 'Wednesday' AND MONTHNAME(login_time) = 'September' AND YEAR(login_time) = '2012'
                                GROUP BY user_id ) temp
                        );
						
-- 2.
-- The outer query find the total number of logouts per user for each month,
-- and then the rows which don't have the maximum number of logouts in a certain month are eliminated.
-- The subqueries find the maximum number of logouts for each month.
-- It's possible that more than one user has the maximum number of logouts in a certain month,
-- and this solution returns all of them.
SELECT month_name month, year, user_id, total_logouts 
FROM (	SELECT user_id, MONTH(login_time) month, YEAR(login_time) year, MONTHNAME(login_time) month_name, COUNT(*) total_logouts
    	FROM login_history
   	    WHERE login_action = 'logout'
      	GROUP BY user_id, month, year      
	 ) t1
WHERE total_logouts IN ( SELECT t2.total_logouts
                   		 FROM ( SELECT t3.month, t3.year, MAX(t3.total_logouts) total_logouts
                                FROM (  SELECT user_id, MONTH(login_time) month, YEAR(login_time) year, COUNT(*) total_logouts
                                        FROM login_history
                                        WHERE login_action = 'logout'
                                        GROUP BY user_id, month, year
                                    ) t3
                                GROUP BY month, year
                              ) t2
				   		WHERE t1.month = t2.month AND t1.year = t2.year
						)
ORDER BY t1.year, t1.month, t1.user_id;
						

-- 3.
-- The query find total number of actions for every user, including those users who didn't have an action, ie. had 0.
SELECT u.user_id, COUNT(h.login_action) total_actions
FROM user u LEFT JOIN login_history h ON u.user_id = h.user_id
GROUP BY u.user_id
ORDER BY u.user_id