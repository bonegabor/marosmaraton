<?php


    require("../includes/config.php");

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
            if (!isset($_SESSION['lap_id'])) {
                $open_lap = CS50::query("SELECT id FROM laps WHERE closed = 0 AND race_id = ?;", $_SESSION['race_id']);
                if (!empty($open_lap)) 
                    $_SESSION['lap_id'] = $open_lap[0]['id'];
                else 
                    apologize("Nincs megkezdett futatam!");
            }
            render("finish.php", ["title" => "Cél", "page" => "finish"]);
        }
    }
    else if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    /*    $result = CS50::query("INSERT INTO laps (race_id) VALUES (?);",$_SESSION['race_id']);
        if ($result == 0) 
            apologize("Új verseny hozzáadása sikertelen");
        $result = CS50::query("SELECT max(id) FROM laps;");
        if (empty($result)) apologize("Valami gubanc van a körökkel!");
        $_SESSION['lap_id'] = $result[0]['max(id)']; 
        redirect("start.php");*/
    }

?>
