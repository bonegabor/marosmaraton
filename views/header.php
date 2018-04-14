<!DOCTYPE html>

<html>

    <head>
        <meta charset="utf-8">

        <!-- http://getbootstrap.com/ -->
        <link href="/css/bootstrap.min.css" rel="stylesheet"/>

        <link href="/css/styles.css" rel="stylesheet"/>
        
        <link href="/css/jquery-ui.min.css" rel="stylesheet"/>

        <!-- https://jquery.com/ -->
        <script src="/js/jquery-1.11.3.min.js"></script>
    
        <!-- http://jqueryui.com/ -->
        <script src="js/jquery-ui.min.js"></script>

        <!-- https://github.com/twitter/typeahead.js/ -->
        <script src="/js/typeahead.jquery.min.js"></script> 
        

        <!-- http://underscorejs.org/ -->
        <script src="/js/underscore-min.js"></script>

        <!-- http://olifolkerd.github.io/tabulator/ -->
        <link href="/css/tabulator-simple.css" rel="stylesheet">
        <script type="text/javascript" src="/js/tabulator.js"></script>

        <!-- https://dropzonejs.com/ 
        <script type="text/javascript" src="/js/dropzone.js"></script>
        <link href="/css/dropzone.css" rel="stylesheet"> -->

        <?php if (isset($title)): ?>
            <title>Maros Maraton: <?= htmlspecialchars($title) ?></title>
        <?php else: ?>
            <title>Maros Maraton</title>
        <?php endif ?>

        <!-- http://getbootstrap.com/ -->
        <script src="/js/bootstrap.min.js"></script>

        <script src="/js/scripts.js"></script>
        
<!--        <script src='https://www.google.com/recaptcha/api.js'></script> -->

    </head>

    <body>
        <div class="container">

            <div id="top">
                <?php if (!empty($_SESSION["id"])): ?>
                <nav class="navbar navbar-default navbar-fixed-top">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a href="race.php?r=race-select" class="navbar-brand"><img alt="Maros Maraton" src="/img/logo.png"/></a>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <?php if (isset($page)) : ?>
                                <?php if ($page == 'home') : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item"><a href="settings.php" class="nav-link">Beállítások</a></li>
                                        <li class="nav-item"><a href="race.php" class="nav-link">Verseny</a></li>
                                    </ul>
                                <?php elseif ((in_array($page,['race','start','finish','race-control'])) && ($_SESSION['race_start'] == 'category')) : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item"><a href="/admin.php" class="nav-link">Versenymenü</a></li>
                                        <li class="nav-item"><a href="registration.php" class="nav-link">Regisztráció</a></li>
                                        <li class="nav-item"><a href="start.php" class="nav-link">Rajt</a></li>
                                        <li class="nav-item"><a href="finish.php" class="nav-link">Cél</a></li>
                                    </ul>
                                <?php elseif ((in_array($page,['race','start','finish','race-control'])) && ($_SESSION['race_start'] == 'team')) : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item" title="Vissza"><a href="/admin.php" class="nav-link">Versenymenü</a></li>
                                        <li class="nav-item"><a href="registration.php" class="nav-link">Regisztráció</a></li>
                                        <li class="nav-item"><a href="start.php" class="nav-link">Rajt és cél</a></li>
                                    </ul>
                                <?php elseif ($page == 'race-select') : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item" title="Vissza"><a href="/admin.php" class="nav-link"><span class="glyphicon glyphicon-menu-left"></a></li>
                                        <li class="nav-item"><a href="new-race.php" class="nav-link">Új verseny</a></li>
                                    </ul>
                                <?php elseif ($page == 'race-controlx') : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item" title="Vissza"><a href="race.php?r=race-select" class="nav-link"><span class="glyphicon glyphicon-menu-left"></a></li>
                                        <li class="nav-item"><a href="registration.php" class="nav-link">Regisztáció</a></li>
                                        <li class="nav-item"><a href="start.php" class="nav-link">Rajt</a></li>
                                        <li class="nav-item"><a href="finish.php" class="nav-link">Cél</a></li>
                                    </ul>
                                <?php elseif ($page == 'registration') : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item" title="Vissza"><a href="race.php" class="nav-link"><span class="glyphicon glyphicon-menu-left"></a></li>
                                        <li class="nav-item" title="Előjegyzések"><a href="afuncs.php?process=registration&action=pre" class="nav-link" data-toggle="modal" data-target="#basicModal" >Előjegyzettek</a></li>
                                    </ul>
                                <?php else : ?>
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item" title="Vissza"><a href="/admin.php" class="nav-link"><span class="glyphicon glyphicon-menu-left"></a></li>
                                    </ul>
                                <?php endif ?>
                            <?php else : ?>
                                <ul class="nav navbar-nav">
                                    <li class="nav-item"><a href="settings.php" class="nav-link">Beállítások</a></li>
                                    <li class="nav-item"><a href="race.php" class="nav-link">Verseny</a></li>
                                </ul>
                            <?php endif ?>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="navbar-text"><?php if (isset($_SESSION['race_name'])) echo htmlspecialchars($_SESSION["race_name"]); ?></li>
                                <li class="navbar-text"><?= htmlspecialchars($_SESSION["user"]["name"]) ?></li>
                                <li class="nav-item" title="Profil"><a href="profile.php" class="nav-link"><span class="glyphicon glyphicon-user"></span></a></li>
                                <li class="nav-item" title="Kilépés"><a href="logout.php" class="nav-link"><span class="glyphicon glyphicon-log-out"></span></a></li>
                         </ul>
                        </div>
                    </div>
                </nav>
                <?php endif ?>
            </div>
<!--            <div class="wide">
                <img src="/img/header.jpg" />
            </div> -->
            <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content"></div>
                </div>
            </div>
            <div id="middle">
