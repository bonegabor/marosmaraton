<?php 
    require("includes/config.php");

    
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['thankyou'])) {
        congratulate("Thank you for your registration!");

    //    render("thankyou.php", ["title" => "Thank you!", "page" => "thankyou"]);
    }
    else
    {
        $result = CS50::query("SELECT race_id, name FROM races WHERE closed = FALSE;" );
        if ($result == 0)
            apologize('Nincs még regisztrált kategória');
        else
            render("prereg-form.php", ["title" => "Registration", "page" => "preregister","races" => $result]);
    }
}

// else if user reached page via POST (as by submitting a form via POST)
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    for ($i = 1; $i <= 6; $i++) {
        
        if (isset($_POST[$i.'-first_name']) && $_POST[$i.'-first_name'] != '') {
            $result = preregister_team_member($i);
            if ($result == 1)
                apologize("Valami nem jó");
        }
    }

    redirect("/?thankyou");
}

?>
