<?php 
    require("includes/config.php");

    
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $result = CS50::query("SELECT cat_id,shortname FROM categories WHERE race_id = ?;", $_SESSION['race_id']);
        if ($result == 0)
            apologize('Nincs még regisztrált kategória');
        else
            render("registration-form.php", ["title" => "Regisztráció", "page" => "registration","categories" => $result]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $result = CS50::query("INSERT INTO teams (race_number, category, race_id, team_name) VALUES(?, ?, ?, ?)", $_POST["race_number"], $_POST["category"], $_SESSION['race_id'], $_POST['team_name']);
        if ($result === 0)
            apologize("Adatbázis hiba.");

        $result = CS50::query("SELECT count(*) FROM pre_registrations WHERE team_name = ?",$_POST['team_name']);
        if ($result != 0)
            CS50::query("UPDATE pre_registrations SET registered = TRUE WHERE team_name = ?",$_POST['team_name']);
        
        $rows = CS50::query("SELECT max(tid) from teams;");
        $tid = $rows[0]["max(tid)"];
        for ($i = 1; $i <= 6; $i++) {
            if (isset($_POST[$i.'-member'])) {
                $result = insert_team_member($i,$tid);
                if ($result == 1)
                    apologize("Valami nem jó");
            }
        }

        redirect("registration.php");
    }
    
?>
