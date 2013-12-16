<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>DoozR | The lightweight MVP PHP-Framework for high-performance websites</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="Data/Public/www/view/assets/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="Data/Public/www/view/assets/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="Data/Public/www/view/assets/css/main.css">

    <!--[if lt IE 9]>
    <script src="js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">DoozR</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">Demo <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/Demo/Screen/">Request /Demo/Screen/</a></li>
                        <li class="divider"></li>
                        <li><a href="acl.php">ACL</a></li>
                        <li><a href="compact.php">Compact</a></li>
                        <li><a href="configreader.php">Configreader</a></li>
                        <li><a href="database.php">Database</a></li>
                        <li><a href="debug.php">Debug</a></li>
                        <li><a href="error.php">Error</a></li>
                        <li><a href="form.php">Form</a></li>
                        <li><a href="i18n.php">I18n</a></li>
                        <li><a href="registry.php">Registry</a></li>
                        <li><a href="rest.php">REST</a></li>
                        <li><a href="session.php">Session</a></li>
                        <li><a href="template.php">Template</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.navbar-collapse -->
    </div>
</div>

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
    <div class="container">
        <h1>Say hello to DoozR!</h1>
        <p>The installation was successful! This is the default page of DoozR which provides you some demonstrations of DoozR's functionality. The following boxes containing links to demonstrations like Database-Access, I18n, ACL, Session, REST, Template and so many more ...</p>
        <p><a class="btn btn-primary btn-lg" href="/Demo/Screen/">Try to request /Demo/Screen/ &raquo;</a></p>
    </div>
</div>

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>ACL</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="acl.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Compact</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="compact.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Configreader</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-default" href="configreader.php">View demo &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>Database</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="database.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Debug</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="debug.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Error</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-default" href="error.php">View demo &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>Form</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="form.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>I18n</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="i18n.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Registry</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-default" href="registry.php">View demo &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h2>REST</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="rest.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Session</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-default" href="session.php">View demo &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h2>Template</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-default" href="template.php">View demo &raquo;</a></p>
        </div>
    </div>

    <hr>

    <footer>
        <p>DoozR | &copy; clickalicious 2007 - 2013</p>
    </footer>
</div> <!-- /container -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="Data/Public/www/view/assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
<script src="Data/Public/www/view/assets/js/vendor/bootstrap.min.js"></script>
<script src="Data/Public/www/view/assets/js/main.js"></script>

</body>
</html>
