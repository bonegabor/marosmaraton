<?php

    // configuration
    require("includes/config.php");

    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // else render form
        render("register_form.php", ["title" => "Register"]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["username"]))
        {
            apologize("You must provide your username.");
        }
        if (empty($_POST["email"]))
        {
            apologize("You must provide your email address.");
        }
        else if (empty($_POST["password"]))
        {
            apologize("You must provide your password.");
        }
        else if ($_POST["password"] !== $_POST["confirmation"])
        {
            apologize("Passwords do not match.");
        }
        
        
        //insert new user
        $result = CS50::query("INSERT IGNORE INTO users (username, email, hash, colleague, name) VALUES(?, ?, ?, 1, ?)", $_POST["username"], $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), $_POST['name']);
        if ($result === 0)
        {
            apologize("Username or email in use.");
        }
        else
        {
            $rows = CS50::query("SELECT LAST_INSERT_ID() AS id");
            
            // remember that user's now logged in by storing user's ID in session
            $_SESSION["id"] = $rows[0]["id"];
            $_SESSION["user"] = $rows[0];
            
            // redirect to portfolio
            redirect("/admin.php");
        }
    }
    
?>
