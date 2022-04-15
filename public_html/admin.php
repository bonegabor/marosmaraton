<?php

	// configuration
	require("includes/config.php"); 

    $rows = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["id"]);
    $row = $rows[0];
        
//    render("profile.php", ["title" => "Profile", "user" => $row, "page" => "home"]);
    redirect("race.php");
?>
