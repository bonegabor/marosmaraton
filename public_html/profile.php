<?php
    
    // configuration
    require("includes/config.php");
    
    $rows = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["id"]);
        
    $row = $rows[0];
        
    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // else render form
        render("profile.php", ["title" => "Profile", "user" => $row]);
    }

    // else if user reached page via POST to change password
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if ($_POST["submit"] == 'change_pwd')
        {
            
            if (password_verify($_POST["old_password"], $row["hash"]))
            {
                if (empty($_POST["new_password"]))
                {
                    apologize("You must provide a new password.");
                }
                else if ($_POST["new_password"] !== $_POST["confirmation"])
                {
                    apologize("Passwords do not match.");
                }
                CS50::query("UPDATE users SET hash = ?",  password_hash($_POST["new_password"], PASSWORD_DEFAULT));
                
                congratulate("Password changed!");
            }
            apologize("Incorrect password!");
        }
    
    // else if user reached page via POST to add extra funds
        else if ($_POST["submit"] == 'change_email')
        {
            CS50::query("UPDATE users SET email = ? WHERE id = ?",  $_POST["new_email"], $_SESSION["id"]);

            redirect("/profile.php");
            
        }
        else if ($_POST["submit"] == 'change_name')
        {
            CS50::query("UPDATE users SET name = ? WHERE id = ?",  $_POST["new_name"], $_SESSION["id"]);

            redirect("/profile.php");
            
        }
    }
?>
