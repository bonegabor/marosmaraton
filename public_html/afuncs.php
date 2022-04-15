<?php 

require("includes/config.php");

if (isset($_GET['process'])) {
    switch ($_GET['process']) {

    case 'race': race();
    break;

    case 'getCategories': get_categories();
    break;

    case 'preregistration': verify_team_name();
    break;

    case 'verifyracenumberPrereg': verify_race_number('pre_registrations');
    break;

    case 'registration': registration();
    break;

    case 'verifyracenumber': verify_race_number('both');
    break;

    case 'race-control': race_control();
    break;

    case 'modalgallery': modalgallery();
    break;

    case 'extendform': extendform($_GET['form']);
    break;

    default:
    echo 'Ez nem kéne megjelenjen!';

    }
}

elseif (isset($_POST['process'])) {
    switch ($_POST['process']) {

    case 'edit': edit();
    break;

    case 'race': race();
    break;

    default:
    echo 'Ez sem kéne megjelenjen!';
    }
}


?>
