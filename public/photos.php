<?php

	// configuration
	require("../includes/config.php"); 

    // render management page
    render("photos.php", ["title" => "Fényképek", "event_id" => $_GET['event_id']]);
?>
