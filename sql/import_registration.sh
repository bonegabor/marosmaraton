#!/bin/bash

db=db_name
user=username
pw=password

count=$(echo "SELECT max(tid) as c FROM teams;" | mysql $db -u $user -p$pw)
tn=$(echo $count | cut -d ' ' -f 2)
count=$(echo "SELECT max(mid) as c FROM team_members;" | mysql $db -u $user -p$pw)
tmn=$(echo $count | cut -d ' ' -f 2)

mysql -u $user -p$pw $db < teams.sql
mysql -u $user -p$pw $db < team_members.sql

mysql -u $user -p$pw $db --execute="SET foreign_key_checks = 0; UPDATE teams_temp SET tid = tid + $tn  ORDER BY tid DESC; UPDATE team_members_temp SET team_id = team_id + $tn, mid = mid + $tmn  ORDER BY mid DESC; SET foreign_key_checks = 1; "
mysql -u $user -p$pw $db --execute="INSERT INTO teams SELECT * FROM teams_temp; INSERT INTO team_members SELECT * FROM team_members_temp;"
mysql -u $user -p$pw $db --execute=" DROP TABLE team_members_temp;DROP TABLE teams_temp;"
