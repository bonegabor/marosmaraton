<?php

    require("includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == 'GET')  
    render("form.php", ["title" => "Új verseny", "page" => 'new-race']);
elseif ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $result = CS50::query("INSERT INTO races (name,date,location,start,closed) VALUES (?,?,?,?,FALSE);",$_POST['name'],$_POST['date'],$_POST['location'],$_POST['start_type']);
    $_SESSION['race_start'] = $_POST['start_type'];
    if ($result == 0) 
        apologize("Új verseny hozzáadása sikertelen");
    redirect("race.php");
}
?>
