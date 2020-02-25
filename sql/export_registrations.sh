#!/bin/bash

db=db_name
user=username
pw=password

mysqldump -u $user -p$pw $db teams | sed -e "s/teams/teams_temp/gi" > teams.sql
mysqldump -u $user -p$pw $db team_members | sed -e "s/team_members/team_members_temp/gi" | sed -e "s/teams/teams_temp/gi"> team_members.sql
