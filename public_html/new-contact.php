<?php

    require("includes/config.php");

    if ($_SERVER["REQUEST_METHOD"] == 'GET')
    {
        $structure = CS50::query("SELECT * FROM forms WHERE name = 'new_contact'");
        if (empty($structure)) {
            apologize("Empty query");
        }

        render("form.php", ["title" => "Add new contact", "structure" => $structure, "vals" => $vals]);
    }
    else if ($_SERVER["REQUEST_METHOD"] == 'POST')
    {

        CS50::query("INSERT INTO `users`(`name`, `county`, `postal_code`, `address`, `location`, `email`, `telephone`, `mobile`, `fax`, `status`, `website`, `comments`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)", $_POST["name"], $_POST["county"], $_POST["postal_code"], $_POST["address"], $_POST['location'], $_POST["email"], $_POST["telephone"], $_POST["mobile"], $_POST["fax"], $_POST['status'], $_POST['website'], $_POST["comments"]);
        $last = CS50::query("SELECT LAST_INSERT_ID()");
        $last_id = $last[0]['LAST_INSERT_ID()'];
        
//        redirect("/contacts.php");
    }
?>
