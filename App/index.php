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
                <li><a href="#services">Services</a></li>
                <li><a href="#install">Install</a></li>
                <li><a href="#configure">Configure</a></li>
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">Demo <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/Demo/Screen/" data-target="#myModal">Request /Demo/Screen/</a></li>
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
                        <li class="divider"></li>
                        <li><a href="cache.php">Cache</a></li>
                        <li><a href="crypt.php">Crypt</a></li>
                        <li><a href="datetime.php">Datetime</a></li>
                        <li><a href="filesystem.php">Filesystem</a></li>
                        <li><a href="oauth2.php">OAuth2</a></li>
                        <li><a href="password.php">Password</a></li>
                        <li><a href="phptal.php">PHPTal</a></li>
                        <li><a href="virtualfilesystem.php">Virtualfilesystem</a></li>
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
        <p>The installation was successful! DoozR - Tiny & lightweight but strong & powerful: Built-In <b>generic Database-Access Layer</b>, native <b>I18n</b> support, <b>ACL</b> by CRUD-principle, <b>Compression</b> for JS and CSS, great <b>Error- and Exception-Handling</b>, secured <b>session</b>, a huge set of <b>pre-implemented loggers</b>, <b>Templating</b> with native I18n support and last but not least a <i>ready-to-use</i> <b>REST-API</b> implementation... Bäm!</p>
        <p>
        <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
            REST-API request /User/1234 &raquo;
        </button>
        </p>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Attention! mod_rewrite could fail here</h4>
            </div>
            <div class="modal-body">
                <p>Please be warned, that requesting a route in a fresh installation can hurt your feelings bad if not installed in a own vhost or root node.</p>
                If the the request on <b>/Api/User/1234</b> fails with e.g. a HTTP <i>404</i> or <i>500</i> response then you probably need to configure your specific environment setup in the mod_rewrite part of the <i>.htaccess</i> file in the root folder of your DoozR <a href="#install">installation</a>.</p>
<pre>
#-----------------------------------------------------------
# PATH CONFIGURATION/SETUP HERE
#-----------------------------------------------------------
RewriteBase /Framework/

RewriteRule .* - [E=DOOZR_PATH_APP:../App/Data/Public/www/]
RewriteRule .* - [E=DOOZR_ROUTER:Route.php]
</pre>
                <p>
                    In most cases you only need to change the <i>RewriteBase</i> to match your folder structure.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="loadingButton" type="button" data-loading-text="Requesting..." class="btn btn-primary" onclick="document.location.href='/Demo/Screen/'">Continue</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<a name="services"></a>
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

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
            <a name="install"></a><h2>Install</h2>
            <p>Installing DoozR is as simple as cloning a repository from github. Clone git@clickalicious. You should clone DoozR into a htdocs-root so you won't need to <a href="#configure">configure</a> the routing.
            After cloning you only need to call the root URI of your installation e.g. http://localhost/</p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-12">
            <a name="configure"></a><h2>Configure</h2>
            <p>A fresh DoozR installation is runnable without the need to be configured as long as DoozR is installed in the root folder of a host (vhost, htdocs).</p>
        </div>
    </div>

    <hr>

    <footer>
        <p>DoozR | &copy; clickalicious 2005 - <?=date('Y');?></p>
    </footer>
</div> <!-- /container -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="Data/Public/www/view/assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
<script src="Data/Public/www/view/assets/js/vendor/bootstrap.min.js"></script>
<script src="Data/Public/www/view/assets/js/main.js"></script>

</body>
</html>
