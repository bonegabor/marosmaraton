<?php


    require("../includes/config.php");

    if ($_SERVER["REQUEST_METHOD"] == 'GET') {
        if (!isset($_SESSION['race_id'])) {
            $races = CS50::query("SELECT race_id,name,closed FROM races WHERE closed = 0 ORDER BY closed;");
            if (empty($races)) 
                render("form.php", ["title" => "Új verseny", "page" => 'new-race']);
            else
                render("race-select.php", ["title" => "Vereny választása", "page" => 'race-select', "races" => $races]);
        }
        else if ((isset($_GET['r'])) && ($_GET['r'] == 'race-select')) {
            $races = CS50::query("SELECT race_id,name,closed FROM races ORDER BY closed;");
            render("race-select.php", ["title" => "Vereny választása", "page" => 'race-select', "races" => $races]);
        }
        else {
            $rows = CS50::query("SELECT * FROM races WHERE race_id = ?", $_SESSION['race_id']);
            $race = $rows[0];
            render("race-control.php", ["title" => "Vezérlőpult", "page" => "race-control", "race" => $race]);
        }
    }
    else if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        if (isset($_POST['race'])) {
            $_SESSION['race_id'] = $_POST['race'];
            $_SESSION['lap_id'] = null;
            $result = CS50::query("SELECT start,name FROM races WHERE race_id = ?",$_SESSION['race_id']);
            $_SESSION['race_start'] = $result[0]['start'];
            $_SESSION['race_name'] = $result[0]['name'];
        }
        else if (isset($_POST['closerace'])) {
            CS50::query("UPDATE races SET closed = TRUE WHERE race_id = ?",$_SESSION['race_id']);
            if ($_SESSION['lap_id'] != null)
                CS50::query("UPDATE laps SET closed = TRUE WHERE id = ?",$_SESSION['lap_id']);
            $_SESSION['race_id'] = null;
            $_SESSION['lap_id'] = null;
        }
        redirect("race.php");
    }

?>
