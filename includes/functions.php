<?php 

//require(__DIR__."/../includes/config.php");

function registration() {
    if ($_GET['action'] == 'typeahead') {
        $result = CS50::query("SELECT DISTINCT team_name,category,race_number FROM pre_registrations WHERE MATCH(team_name) AGAINST (? IN BOOLEAN MODE) AND registered = FALSE AND race = ? LIMIT 20", $_GET["teamname"].'*',$_SESSION['race_id']);

        // output places as JSON (pretty-printed for debugging convenience)
        header("Content-type: application/json");
        print(json_encode($result));
        exit;
    }
    if ($_GET['action'] == 'getDetails') {
        $result = CS50::query("SELECT first_name,last_name,country,region,city,postal_code,address,email,phone,reg_date, 
                                CASE 
                                    WHEN reg_date < '2018-03-16' THEN 25 
                                    WHEN reg_date >= '2018-03-16' AND reg_date < '2018-04-15' THEN 35
                                    ELSE 45
                                END AS fee
                               FROM pre_registrations WHERE team_name = ?", $_GET["teamname"]);

        // output places as JSON (pretty-printed for debugging convenience)
        header("Content-type: application/json");
        print(json_encode($result));
        exit;
    }

    if ($_GET['action'] == 'pre') {
        $result = CS50::query("SELECT * FROM pre_registrations WHERE registered = FALSE AND race = ?;",$_SESSION['race_id']);
        if ($result == 0) {
            $table = "<tr><td>Nincs előjegyzett csapat!</td></tr>";
        }
        $table = '';
        foreach ($result as $res) {
            $table .= "<tr><td>".htmlspecialchars($res['team_name'])."</td><td>".htmlspecialchars($res['last_name'])." ".htmlspecialchars($res['first_name'])."</td><td>".htmlspecialchars($res['phone'])."</td></tr>";
        }
        echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3 class="modal-title">Előjegyzettek</h3></div><div class="modal-body"><table style="100%"><tr><th>Csapatnév</th><th>Csapatvezető</th><th>Telefonszám</th></tr>';
        print $table;
        echo '</table></div>'; /* end of modal-body */
    }
}




function race() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['action'] == 'start') {
            if (isset($_GET['cat_id'])) {
                $result = CS50::query("INSERT INTO race (race_id,lap_id,cat_id,team_id,start_time) (SELECT ?,?,?,tid,NOW() FROM teams t JOIN categories c on c.cat_id = t.category WHERE t.category = ? and t.race_id = ? );", $_SESSION['race_id'], $_SESSION['lap_id'], $_GET['cat_id'], $_GET['cat_id'],$_SESSION['race_id']);
                if ($result == 0 ) {
                    header("Content-type: text/html");
                    print("Nincs versenyző a kategóriában!");
                }
                else {
                    $result = CS50::query("SELECT start_time FROM race ORDER BY id DESC LIMIT 1;");
                    header("Content-type: application/json");
                    print(json_encode($result[0]['start_time']));
                }
            }
            elseif (isset($_GET['race_number'])) {
                if (!isset($_SESSION['running'])) {
                    if (!in_array($_GET['race_number'],$_SESSION['finished'])) {
                        $result = CS50::query("INSERT INTO race (race_id,lap_id,cat_id,team_id,start_time) (SELECT ?,?,cat_id,tid,NOW() FROM teams t JOIN categories c on c.cat_id = t.category WHERE t.race_number = ? and t.race_id = ? );", $_SESSION['race_id'], $_SESSION['lap_id'], $_GET['race_number'],$_SESSION['race_id']);
                        $_SESSION['running'] = $_GET['race_number'];
                        if ($result == 0 ) {
                            header("Content-type: application/json");
                            print(-1); //Nincs ilyen versenyszám ebben a versenyben.
                        }
                        else {
                            $result = CS50::query("SELECT r.id, c.shortname, t.race_number, r.finish_time, r.penality, ADDTIME(r.finish_time,SEC_TO_TIME(rs.penpoints * r.penality)) as final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id JOIN races rs ON rs.race_id = r.race_id WHERE r.race_id = ? AND r.lap_id = ? and t.race_number = ?;", $_SESSION['race_id'], $_SESSION['lap_id'], $_GET['race_number']);
                            header("Content-type: application/json");
                            print(json_encode($result));
                        }
                    }
                    else { 
                        header("Content-type: application/json");
                        print(-2); //Már befejezte a futamot ez a versenyző
                    }
                }
                else {
                    header("Content-type: application/json");
                    print(-3); //Már elindult egy versenyző
                }
            }
        }
        elseif ($_GET['action'] == 'initfinish') {
            $_SESSION['finished'] = [];
            $result = CS50::query("SELECT r.id, c.shortname, t.race_number, r.finish_time, r.penality, ADDTIME(r.finish_time,SEC_TO_TIME(rs.penpoints * r.penality)) as final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id JOIN races rs ON rs.race_id = r.race_id WHERE r.race_id = ? AND r.lap_id = ? and r.finish_time <> '0000-00-00 00:00:00';", $_SESSION['race_id'], $_SESSION['lap_id']);
            foreach ($result as $rid) {
                $_SESSION['finished'][] = $rid['race_number'];
            }
            header("Content-type: application/json");
            print(json_encode($result));
        }
        elseif ($_GET['action'] == 'finish') {
            if (!in_array($_GET['race_number'],$_SESSION['finished'])) {
                $result = CS50::query("UPDATE race set finish_time = SEC_TO_TIME(TIMESTAMPDIFF(SECOND,start_time,NOW())) WHERE race_id = ? AND lap_id = ? and team_id = (SELECT tid FROM teams WHERE race_id = ? AND race_number = ?);", $_SESSION['race_id'], $_SESSION['lap_id'], $_SESSION['race_id'], $_GET['race_number']);
                $_SESSION['running'] = null;
                if ($result == 0 ) {
                    header("Content-type: application/json");
                    print(-1);
                }
                else {
                    $_SESSION['finished'][] = $_GET['race_number'];
                    $result = CS50::query("SELECT r.id, c.shortname, t.race_number, r.finish_time, r.penality, ADDTIME(r.finish_time,SEC_TO_TIME(rs.penpoints * r.penality)) as final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id JOIN races rs ON rs.race_id = r.race_id WHERE r.race_id = ? AND r.lap_id = ? and t.race_number = ?;", $_SESSION['race_id'], $_SESSION['lap_id'], $_GET['race_number']);
                    header("Content-type: application/json");
                    print(json_encode($result));
                }
            }
            else { 
                header("Content-type: application/json");
                print(-2);
            }
        }
        elseif ($_GET['action'] == 'finish-lap') {
            $result = CS50::query("UPDATE laps SET closed = true where id = ?;",$_SESSION['lap_id']);
            $_SESSION['lap_id'] = null;
            redirect("/admin.php");
        }

        elseif ($_GET['action'] == 'laps-results') {
            $result = CS50::query("SELECT c.cat_id,t.tid,r.id,c.shortname,t.race_number,(select lap_no from laps where id = r.lap_id) as lap_no,(select group_concat(concat(first_name,' ',last_name) separator '</br>') as name FROM team_members where team_id = t.tid) as team_members,r.finish_time,r.penality,r.final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id WHERE r.race_id = ?;", $_SESSION['race_id']);
            header("Content-type: application/json");
            print(json_encode($result));
        }

        elseif ($_GET['action'] == 'final-results') {
            $cmd = "SELECT c.cat_id,t.tid,r.id,c.shortname,t.race_number,(select lap_no from laps where id = r.lap_id) as lap_no,(select group_concat(concat(first_name,' ',last_name) separator '</br>') as name FROM team_members where team_id = t.tid) as team_members, r.finish_time, r.penality, r.final_time as final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id WHERE r.race_id = ? and r.final_time in (SELECT min(final_time) FROM `race` WHERE race_id = ? and team_id = t.tid group by team_id);";
            $result = CS50::query($cmd, $_SESSION['race_id'], $_SESSION['race_id']);
            header("Content-type: application/json");
            print(json_encode($result));
        }
    }
    else {
        if ($_POST['action'] = 'penality' and $_POST['id'] != null) {
            $cmd = "update race set penality = ? where id = ?" ;
            $result = CS50::query($cmd, $_POST['value'],$_POST['id']);
            if ($result == 0)
                print -1;
            else
                print 1;   
        }
    }
}

function preregister_team_member($nr) {
    if (isset($_POST[$nr.'-first_name'])) {
        $result = CS50::query("INSERT INTO pre_registrations (team_name,race,category,race_number,first_name,last_name,country,region,postal_code,city,address,email,phone) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);", $_POST['team_name'],$_POST['race'],$_POST['category'],$_POST['race_number'],$_POST[$nr.'-first_name'], $_POST[$nr.'-last_name'],  $_POST[$nr.'-country'],$_POST[$nr.'-region'],$_POST[$nr.'-postal_code'],$_POST[$nr.'-city'],$_POST[$nr.'-address'],$_POST[$nr.'-email'],$_POST[$nr.'-phone']);

        if ($result == 0 )
            apologize("Adatbázis hiba (team_member)");
        return 0;
    }
    else
        return 1;
}



function insert_team_member($nr,$team_id) {
    $fee_paid = isset($_POST[$nr.'-fee_paid']) ? 'true' : 'false';
    if (isset($_POST[$nr.'-first_name'])) {
        $result = CS50::query("INSERT INTO team_members (team_id,first_name,last_name,fee,fee_paid,reg_date,country,region,city,postal_code,address,email,phone) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);", $team_id, $_POST[$nr.'-first_name'], $_POST[$nr.'-last_name'], $_POST[$nr.'-fee'], $fee_paid, $_POST[$nr.'-reg_date'],$_POST[$nr.'-country'],$_POST[$nr.'-region'],$_POST[$nr.'-city'],$_POST[$nr.'-postal_code'],$_POST[$nr.'-address'],$_POST[$nr.'-email'],$_POST[$nr.'-phone']);
        if ($result == 0 )
            apologize("Adatbázis hiba (team_member)");
        return 0;
    }
    else
        return 1;
}

function race_control() {
    if ($_GET['action'] == 'competitors-table') {
        $competitors = CS50::query("SELECT t.tid, tm.mid, c.shortname, t.race_number, t.team_name, tm.first_name, tm.last_name, DATE(tm.reg_date) as reg_date, tm.fee, tm.fee_paid FROM `team_members` as tm JOIN `teams` as t on tm.team_id = t.tid JOIN categories as c ON t.category = c. cat_id WHERE t.race_id = ? order by t.race_number", $_SESSION['race_id']);

        // output events as JSON 
        header("Content-type: application/json");
        print(json_encode($competitors));
    }
    elseif ($_GET['action'] == 'categories-table') {
        $categories = CS50::query("SELECT * FROM categories WHERE race_id = ?", $_SESSION['race_id']);

        // output events as JSON 
        header("Content-type: application/json");
        print(json_encode($categories));
    }
    elseif ($_GET['action'] == 'results-table') {
        $categories = CS50::query("SELECT c.cat_id,t.tid, r.id, c.shortname, t.race_number, (SELECT GROUP_CONCAT(CONCAT(first_name,' ',last_name)) FROM team_members WHERE ) as team_members, r.finish_time, r.penality, ADDTIME(r.finish_time,SEC_TO_TIME(rs.penpoints * r.penality)) as final_time FROM race r JOIN teams t ON r.team_id = t.tid JOIN categories c ON t.category=c.cat_id JOIN races rs ON rs.race_id = r.race_id WHERE r.race_id = ? AND r.lap_id = ? and r.finish_time <> '0000-00-00 00:00:00';", $_SESSION['race_id'], $_SESSION['lap_id']);

        // output events as JSON 
        header("Content-type: application/json");
        print(json_encode($categories));
    }
}

function extendform($form) {
    if (in_array($_GET['category'],['C2','K2']))
        $max = 2;
    elseif (in_array($_GET['category'],['WWK','RRK','SK','TK']))
        $max = 1;
    else 
        $max = 6;

    // outputs the extension of the form asked by ajax
    header("Content-type: text/html");
    for ($i = 2; $i <= $max; $i++) {
        echo '<div class="team-member">';
        form_loop($form.$i);
        echo "</div>";
    }
}

function get_categories() {
    $cmd = sprintf("SELECT shortname FROM categories c JOIN races r ON c.race_id = r.race_id WHERE r.race_id = ?;");
    $result = CS50::query($cmd, $_GET['race']);

    if ($result) {
        header("Content-type: application/json");
        print(json_encode($result));
    }
    else
        apologize('No categories defined!');
}

function verify_team_name() {
    $cmd = sprintf("SELECT team_name from pre_registrations where team_name = ? AND archive = FALSE;");
    $result = CS50::query($cmd, $_GET['team_name']);

    header("Content-type: application/json");
    if ($result)
        print(json_encode('0'));
    else
        print(json_encode('1'));
}

function verify_race_number($table = 'teams') {
    $cmd = sprintf("select race_number from %s where race_number = ?;",$table);
    $result = CS50::query($cmd, $_GET['race_number']);

    header("Content-type: application/json");
    if ($result)
        print(json_encode('0'));
    else
        print(json_encode('1'));
}

function edit() {
    if ($_POST['table'] == 'categories') {
        if (isset($_POST['id'])) {
            $cmd = "delete from ".$_POST['table']." where cat_id = ?";
            $result = CS50::query($cmd, $_POST['id']);
            if ($result == 0)
                print -1;
            else
                print 3;
        }
        elseif ($_POST['cat_id'] != null) {
            $cmd = "update categories set ".$_POST['column']." = ? where cat_id = ? and race_id = ?" ;
            $result = CS50::query($cmd, $_POST['value'],$_POST['cat_id'],$_SESSION['race_id']);
            if ($result == 0)
                print -1;
            else
                print 1;   
        }
        else {
            $cmd = "insert into categories (race_id, ".$_POST['column'].") values (?,?);";
            $result = CS50::query($cmd, $_SESSION['race_id'], $_POST['value']);
            if ($result == 0)
                print -1;
            else
                print 2;   
        }
    }
    elseif ($_POST['table'] == 'team_members') {
        if ($_POST['mid'] != null) {
            $cmd = "update team_members set ".$_POST['column']." = ? where mid = ?" ;
            $result = CS50::query($cmd, $_POST['value'],$_POST['mid']);
            if ($result == 0)
                print -1;
            else
                print 1;   
        }
    }
}
/*function delete_record() {
    $pid = $_GET['pid'];
    if ($_GET['eid'] != "") {
        $eid = $_GET['eid'];
        CS50::query("DELETE FROM events WHERE patient_id = ? AND event_id = ?", $pid, $eid);
    }
    else {
        CS50::query("DELETE FROM events WHERE patient_id = ?", $pid);
        CS50::query("DELETE FROM patient_data WHERE id = ?", $pid);
    }
}

function get_events() {
    $pid = $_GET['id'];
    $events = CS50::query("SELECT e.event_id,e.event,e.date,e.comments,e.location,e.county,e.x,e.y,e.circumstances,e.days_between,e.food_given,e.keeping,e.destination,e.transport_mode,e.diagnosis,e.treatment,e.brought_in_becau,e.cause_of_injury,e.injury_tract, f.value, e.has_photos FROM events as e INNER JOIN forms_data as f ON `event` = f. alias WHERE `patient_id` = ? order by event_id desc",$pid);
    foreach ($events as $key => $value) {
        $eid = $events[$key]['event_id'];
        if ($events[$key]['has_photos'] != 0) {
            $events[$key]['has_photos'] = '<a href="/afuncs.php?process=modalgallery&event_id=' . $eid . '" class="btn-sm btn-default" role="button" data-toggle="modal" data-target="#basicModal"><span aria-hidden="true" class="glyphicon glyphicon-picture"></span></a>';
        }
        else {
            $events[$key]['has_photos'] = '';
        }
        $events[$key]['actions'] = '<a href="#" onclick="deleteRecord(' . $pid . ',' . $eid . '); event.preventDefault(); " class="btn-sm btn-danger" role="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a>';
    }
    // output events as JSON 
    header("Content-type: application/json; charset=utf-8");
    print(json_encode($events));
}

function patients_full_list () {
    $completelist = [];

    $inactive = CS50::query("select pd.id, pd.species, pd.current_location, pd.marking, pd.state, f.value from patient_data as pd inner join forms_data as f on pd.current_location = f.alias where pd.state = 0");
    $active = CS50::query("select pd.id, pd.species, pd.current_location, pd.marking, pd.state, f.value from patient_data as pd inner join forms_data as f on pd.current_location = f.alias where pd.state = 1");

    foreach ($inactive as $key => $value) {
        $this_id = $inactive[$key]['id'];
        $inactive[$key]['details'] = '<a href="/new-event.php?id='. $this_id . '&event=bekerules" class="btn-sm btn-default" role="button"><span aria-hidden="true" class="glyphicon glyphicon-repeat"></span></a><a href="#" onclick="deleteRecord(' . $this_id . '); event.preventDefault(); " class="btn-sm btn-danger" role="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a><a href="/patient-data.php?id=' . $this_id . '" class="btn-sm btn-info" role="button"><span aria-hidden="true" class="glyphicon glyphicon-menu-down"></span></a><a href="#" onclick="eventsTable(' . $this_id . ',\'ex-events-table\'); event.preventDefault()" class="btn-sm btn-info" role="button"><span aria-hidden="true" class="glyphicon glyphicon-menu-right"></span></a>';
    }

    foreach ($active as $key => $value) {
        $this_id = $active[$key]['id'];
        $active[$key]['details'] = '<a href="/new-event.php?id='.$this_id.'" class="btn-sm btn-default" role="button"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span></a><a href="/new-event.php?id='. $this_id . '&event=etetes" class="btn-sm btn-default" role="button"><span aria-hidden="true" class="glyphicon glyphicon-cutlery"></span></a><a href="#" onclick="deleteRecord(' . $this_id . '); event.preventDefault(); " class="btn-sm btn-danger" role="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></a><a href="/patient-data.php?id=' . $this_id . '" class="btn-sm btn-info" role="button"><span aria-hidden="true" class="glyphicon glyphicon-menu-down"></span></a><a href="#" onclick="eventsTable(' . $this_id . ',\'events-table\'); event.preventDefault()" class="btn-sm btn-info" role="button"><span aria-hidden="true" class="glyphicon glyphicon-menu-right"></span></a>';
    }

    $completelist[0] = $inactive;
    $completelist[1] = $active;

   // output events as JSON 
    header("Content-type: application/json");
    print(json_encode($completelist, JSON_PRETTY_PRINT));
}

function feedall() {
    $patients = $_SESSION['patients'];
    foreach ($patients as $patient) {
        CS50::query("INSERT INTO `events`(`patient_id`, `user_id`, `event`, `executor_id`) VALUES (?,?,'etetes',?)",$patient['id'], $_SESSION['id'], $_SESSION['id']);
    }
}

function contacts() {
    $places = CS50::query("SELECT id, name FROM users WHERE MATCH(`name`) AGAINST (? IN BOOLEAN MODE) LIMIT 20", $_GET["name"].'*');

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places));
}

function places() {
    $places = CS50::query("SELECT * FROM places WHERE county = ? and MATCH(`name`) AGAINST (? IN BOOLEAN MODE) LIMIT 20", $_GET["county"], $_GET["place"].'*');

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places));
}

function species() {
    $species = CS50::query("SELECT * FROM species_list WHERE MATCH(`scientific_name` , `hungarian_name`, `romanian_name`, `english_name`) AGAINST (? IN BOOLEAN MODE) LIMIT 20", $_GET["species"].'*');

    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($species));
}

function newcontact() {
    global $vals;
    $structure = CS50::query("SELECT * FROM forms WHERE name = 'new_contact' ORDER BY ord");
    if (empty($structure)) {
        apologize("Empty query");
    }
    echo '<script>placesTypeahead(); </script><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3 class="modal-title">Add new contact</h3></div><div class="modal-body"><form id="add-new-contact" action="new-contact.php" method="post"><fieldset>';
    form_loop($structure,$vals);
    echo '<div class="form-group"><button class="btn btn-default" type="submit" onclick="event.preventDefault(); addContact(); "><span aria-hidden="true" class="glyphicon glyphicon-plus-sign"></span>Add new contact</button></div></fieldset></form></div>';
}

function modalgallery() {
    $event_id = $_GET['event_id'];
    echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3 class="modal-title">Fotók</h3></div><div class="modal-body">';
    photo_loop($event_id);
    echo '</div>';
}

function delete_photo() {
    $photo = $_GET['photo'];
    $patient = substr($photo,5,strpos($photo,'_') - 5);
    unlink($photo);
    CS50::query("UPDATE events SET has_photos = has_photos - 1 WHERE event_id = (SELECT event_id FROM photos WHERE file_path = ?)", $photo);
    CS50::query('DELETE FROM photos WHERE file_path = ?', $photo);
}*/
?>
