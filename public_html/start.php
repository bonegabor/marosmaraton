<?php


    require("includes/config.php");

    if ($_SERVER["REQUEST_METHOD"] == 'GET') {
        if (!isset($_SESSION['race_id'])) {
            $races = CS50::query("SELECT race_id,name FROM races WHERE closed = FALSE;");
            if (empty($races)) 
                render("form.php", ["title" => "Új verseny", "page" => 'new-race']);
            else
                render("race-select.php", ["title" => "Vereny választása", "page" => 'race-select', "races" => $races]);
        }
        else {
            // Ha kell az egyéni indítás is, akkor itt kell beszúrni egy formot.
            //
            //$competitors = CS50::query("SELECT * FROM `team_members` as tm JOIN `teams` as t on tm.team_id = t.tid  WHERE t.race_id = ? order by t.race_number", $_SESSION['race_id']);
            
            $open_lap = CS50::query("SELECT id,lap_no FROM laps WHERE closed = 0 AND race_id = ?;", $_SESSION['race_id']);
            if (!empty($open_lap)) { 
                $_SESSION['lap_id'] = $open_lap[0]['id'];
                $_SESSION['lap_no'] = $open_lap[0]['lap_no'];
            }
            else
                $_SESSION['lap_id'] = null;
            $categories = CS50::query("SELECT distinct c.shortname, c.cat_id, r.start_time, c.race_id FROM categories c LEFT JOIN race r ON c.cat_id = r.cat_id and r.lap_id = ? where c.race_id = ?;", $_SESSION['lap_id'], $_SESSION['race_id']);
            render("start.php", ["title" => "Rajt", "page" => "start", "categories" => $categories]);
        }
    }
    else if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        $result = CS50::query("select max(lap_no) from laps where race_id = ?",$_SESSION['race_id']);
        if ($result == 0)
            $lap_no = 1;
        else
            $lap_no = $result[0]['max(lap_no)'] + 1;
        $result = CS50::query("INSERT INTO laps (race_id,lap_no) VALUES (?,?);",$_SESSION['race_id'],$lap_no);
        if ($result == 0) 
            apologize("Új verseny hozzáadása sikertelen");
        $result = CS50::query("SELECT max(id) FROM laps;");
        if (empty($result)) apologize("Valami gubanc van a körökkel!");
        $_SESSION['lap_id'] = $result[0]['max(id)']; 
        redirect("start.php");
    }

?>
