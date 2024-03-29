<?php

    /**
     * config.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Configures app.
     */

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    // requirements
    require("helpers.php");
    require("functions.php");

    // CS50 Library
    $conf = [
        "database" => [
            "host" => "mysql",
            "name" => getenv("DB_NAME"),
            "username" => getenv("DB_USERNAME"),
            "password" => getenv("DB_PASSWORD")
        ]
    ];
    require("vendor/library50-php-5/CS50/CS50.php");
    CS50::init($conf);
    CS50::query("set names utf8;");

    // enable sessions
    session_start();

    // require authentication for all pages except /login.php, /logout.php, and /register.php
    if (!in_array($_SERVER["PHP_SELF"], ["/login.php", "/logout.php", "/register.php", "/index.php", "/afuncs.php"]))
    {
        if (empty($_SESSION["id"]))
        {
            redirect("login.php");
        }
    }
   
?>
