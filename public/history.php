<?php

    // configuration
    require("../includes/config.php"); 

    $positions = null;
    
    $rows = CS50::query("SELECT * FROM history WHERE user_id = ?", $_SESSION["id"]);
    if (!empty($rows[0]))
    {
        $positions = $rows;        
    }
    else
    {
        apologize("No transactions yet.");
    }

    
    
    // render portfolio
    render("history.php", ["title" => "Portfolio", "positions" => $positions ]);

?>
