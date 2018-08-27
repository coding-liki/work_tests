create temp table users(id bigserial, group_id bigint);
insert into users(group_id) values (1), (1), (1), (2), (1), (3);

SELECT 
	group_id  
FROM 
	(SELECT 
		group_id, 
		id, 
		(case 
			WHEN
				group_id <> lead(group_id, 1, group_id+1) over(order by id) 
			THEN 'X' 
		 end) AS flg 
	  FROM users u) AS tt 
WHERE 
	flg is not NULL 
ORDER BY id ASC;

SELECT 
	id, 
	group_id, 
	(lead(id,1, id+1) over(order by id) - id) as count  
FROM 
	(SELECT 
		group_id, 
		id, 
		(case 
			WHEN 
				group_id <> lead(group_id, -1, group_id-1) over(order by id) 
			THEN 'X' 
		 end) AS flg 
	 FROM users u ) AS tt 
WHERE 
	flg is not NULL 
ORDER BY id ASC;
