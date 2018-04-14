<?php

    /**
     * helpers.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Helper functions.
     */

    require_once("config.php");

/** Hibakereső függvény
 *
 */

    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }

    function photo_loop($event_id) {
        $photos = CS50::query("SELECT * FROM photos WHERE `event_id` = ?",$event_id);
        foreach ($photos as $photo) {
            echo '
            <div id="'.str_replace(".","_",str_replace("/","_",$photo['file_path'])).'" class="event-img-wrapper">
                <img class="img-responsive event-img" src="'. $photo['file_path'] .'" width="70%" />
                <div class="img-buttons">
                    <a href="'.$photo['file_path'].'" class="btn btn-default btn-sm" download>
                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    </a>
                    <a href="#" onclick="deletePhoto(\''.$photo['file_path'].'\'); event.preventDefault(); " class="btn btn-danger btn-sm" download>
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </a>
                </div>
            </div>';
        }
    }

/** Form name -> HTML form
 *    builds an HTML form from the database
 */
    function form_loop($form_name) {   
        $structure = CS50::query("SELECT * FROM forms WHERE name = ?", $form_name);
        if (empty($structure)) {
            apologize("Empty query");
        }
        $vals = CS50::query("SELECT * FROM forms_data WHERE form = ? OR form = 'all'", $form_name);
        foreach ($structure as $field) {
            echo '<div class="form-group">';
            if (in_array($field['field_type'],['text','date','email'])) {
                echo '<input id="' . $field['field_name'] .'" class="form-control" name="' . $field['field_name'] .'" placeholder="' . $field['placeholder'] . '" type="' . $field['field_type'] . '" '.$field['req'].'/>';
            }
            elseif ($field['field_type'] == 'number') {
                echo '<input id="' . $field['field_name'] .'" class="form-control" name="' . $field['field_name'] .'" placeholder="' . $field['placeholder'] . '" type="' . $field['field_type'] . '" step="any" '.$field['req'].'/>';
            }
            elseif ($field['field_type'] == 'radio') {
                foreach ($vals as $val) {
                    if ($field['field_name'] == $val['field']) {
                        echo '<input name="' . $field['field_name'] . '" type="' . $field['field_type'] .'" value="' . $val['alias'] . '" '.$field['req'].'/> ' . $val['value'] .' </br>';
                    }
                }
            }
            elseif ($field['field_type'] == 'checkbox') {
                foreach ($vals as $val) {
                    if ($field['field_name'] == $val['field']) {
                        echo '<input name="' . $field['field_name'] . '" type="' . $field['field_type'] .'" value="' . $val['alias'] . '" '.$field['req'].'/> ' . $val['value'] .' </br>';
                    }
                }
            }
            elseif ($field['field_type'] == 'select') {
                echo '<select id="' . $field['field_name'] . '" name="' . $field['field_name'] .'" class="form-control" '.$field['req'].'>';
                if ($field['def'] != null) {
                    echo '<option value="'.$field['def'].'" selected>' . $field['def'] .'</option>';
                }
                else {
                    echo '<option value="" disabled selected hidden>' . $field['placeholder'] .'</option>';
                    foreach ($vals as $val) {
                        if ($field['field_name'] == $val['field']) {
                            echo '<option value="'. $val['alias'] .'"> '. $val['value'] .' </option>';
                        }
                    }
                }
                echo '</select>';
            }
            elseif ($field['field_type'] == 'textarea') {
                echo '<textarea name="'. $field['field_name'] .'" placeholder="'. $field['placeholder'] .'" class="form-control"></textarea>';
            }
            elseif ($field['field_type'] == 'button') {
                echo '<a href="afuncs.php?process='.$field['field_name'].'" id="'. $field['field_name']. '" class="btn btn-xs btn-info" data-toggle="modal" data-target="#basicModal">'.$field['placeholder'].'</a>';
            }
            elseif ($field['field_type'] == 'dropzone') {
                //echo '<div id="dropzone"></div>';
                echo '<input type="file" name="files[]" multiple  class="form-control" style="height:auto;">';
            }
            echo '</div>';
        }
    }

/** Bármely hiba esetén meghívható, és generál egy hiba oldalt
 *
 */

    function apologize($message)
    {
        render("apology.php", ["message" => $message, "sorry" => "Sorry!"]);
    }
    
    function congratulate($message)
    {
        render("apology.php", ["message" => $message, "sorry" => "Success!"]);
    }

    /**
     * Facilitates debugging by dumping contents of argument(s)
     * to browser.
     */
    function dump()
    {
        $arguments = func_get_args();
        require("../views/dump.php");
        exit;
    }

    /**
     * Logs out current user, if any.  Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     */
    function logout()
    {
        // unset any session variables
        $_SESSION = [];

        // expire cookie
        if (!empty($_COOKIE[session_name()]))
        {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }


    /**
     * Redirects user to location, which can be a URL or
     * a relative path on the local host.
     *
     * http://stackoverflow.com/a/25643550/5156190
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */
    function redirect($location)
    {
        if (headers_sent($file, $line))
        {
            trigger_error("HTTP headers already sent at {$file}:{$line}", E_USER_ERROR);
        }
        header("Location: {$location}");
        exit;
    }

    /**
     * Renders view, passing in values.
     */
    function render($view, $values = [], $view2 = null, $values2 = [])
    {
        // if view exists, render it
        if (file_exists("../views/{$view}"))
        {
            // extract variables into local scope
            extract($values);
            if (isset($values2)) {
                extract($values2);
            }
            // render view (between header and footer)
            require("../views/header.php");
            require("../views/{$view}");
            if (isset($view2)){
                require("../views/{$view2}");
            }
            require("../views/footer.php");
            exit;
        }

        // else err
        else
        {
            trigger_error("Invalid view: {$view}", E_USER_ERROR);
        }
    }

?>
