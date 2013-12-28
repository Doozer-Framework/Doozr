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
            <ul id="main-menu" class="nav navbar-nav">
                <li id="link-top" class="active"><a href="#top">Home</a></li>
                <li id="link-features"><a href="#features">Features</a></li>
                <li id="link-services"><a href="#services">Services</a></li>
                <li id="link-requirements"><a href="#requirements">Requirements</a></li>
                <li id="link-install"><a href="#install">Install</a></li>
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">Core Demo <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="acl.php">ACL</a></li>
                        <li><a href="compact.php">Compact</a></li>
                        <li><a href="configreader.php">Configreader</a></li>
                        <li><a href="database.php">Database</a></li>
                        <li><a href="form.php">Form</a></li>
                        <li><a href="i18n.php">I18n</a></li>
                        <li><a href="registry.php">Registry</a></li>
                        <li><a href="rest.php">REST</a></li>
                        <li><a href="session.php">Session</a></li>
                        <li><a href="template.php">Template</a></li>
                        <li><a href="cache.php">Cache</a></li>
                        <li><a href="crypt.php">Crypt</a></li>
                        <li><a href="datetime.php">Datetime</a></li>
                        <li><a href="filesystem.php">Filesystem</a></li>
                        <li><a href="oauth2.php">OAuth2</a></li>
                        <li><a href="password.php">Password</a></li>
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
            Try REST-API /User/1234 &raquo;
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
                If the the request on <b>/Api/User/1234</b> fails with e.g. a HTTP <i>404</i> or <i>500</i> response then you probably need to <a onclick="document.location.href='#install';" style="cursor: pointer;" data-dismiss="modal">configure</a> your specific environment setup in the mod_rewrite part of the <i>.htaccess</i> file in the root folder of your DoozR installation.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="loadingButton" type="button" data-loading-text="Requesting..." class="btn btn-primary" href="/Api/User/1234">Continue</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<a name="features"></a>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <p style="height: 88px;">&nbsp;</p>
            <h2>Features</h2>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Lightweight & intuitive</h3>
            <p>DoozR is lightweight! The structure is kept optimized for best inheritance and reuse of classes. Using DoozR in projects is intuitive and stepping into your first DoozR based project could not be easier.</p>
        </div>
        <div class="col-lg-4">
            <h3>State-of-the-Art</h3>
            <p>DoozR is build by using modern tools and PHP-Standards from the community. DoozR is unit-tested and well documented. It was developed focussing on FURPS.</p>
        </div>
        <div class="col-lg-4">
            <h3>MVP</h3>
            <p>DoozR provides a modern and easy to use MVP-stack. This stack combines the <i>Model</i> & <i>View</i> with the <i>Presenter</i> by using the observer-pattern.</p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Templates</h3>
            <p>DoozR provides you a generic Interface to existing template engines. So the good news is: In the near future it won't matter if your are using Smarty, PHPTAL, Twig - Currently only PHPTAL support.</p>
        </div>
        <div class="col-lg-4">
            <h3>Errors VS. Exceptions</h3>
            <p>DoozR does something that will become native PHP behavior in one of the next releases. Errors will be converted to Exceptions so that you can easily fetch PHP's errors with a try-/catch-block.</p>
        </div>
        <div class="col-lg-4">
            <h3>Speed</h3>
            <p>DoozR is fast, optimized for a maximum number of concurrent request and developed using a profiler to prevent bottlenecks in the codebase.</p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Developer-Tools</h3>
            <p>DoozR is built by developer for developer. Everything a developer need to work effectively and with less pain is deep integrated into DoozR. The Debug-Mode is one great tool and comes aside one other great feature a deeply integrated logging subsystem.</p>
        </div>
        <div class="col-lg-4">
            <h3>Security</h3>
            <p>Security is an important layer which is also deep integrated into all relevant parts of DoozR. DoozR provides state-of-the-art en-/decryption, hashing, secure password generator, secured Session (bind to IP, unique Identifier to every single user).</p>
        </div>
        <div class="col-lg-4">
            <h3>Services</h3>
            <p>DoozR does not provide any module, plugin or extension. Everything in the DoozR world is called a service or a service-provider. You can extend DoozR's base service so easily. The platform to develop is great and intuitive to start with.</p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Runtime Environments</h3>
            <p>DoozR supports different runtime environments used by modern web applications. So no matter if you develop a Console/Commandline-Application (CLI) or a Web-Application (WEB) an API (API) or even if you want to run the App with the native PHP webserver (PHP > 5.4) everything is fully supported.</p>
        </div>
        <div class="col-lg-4">
            <h3>Database (alpha)</h3>
            <p>DoozR provides a generic OOP-database-layer to access databases. The generic layer can be a proxy (automated generated classes) to any existing dbal like Doctrine or phpPillow for CouchDB. It provides always the same interface for connecting to DB or sending queries.</p>
        </div>
        <div class="col-lg-4">
            <h3>REST API</h3>
            <p>A prebuilt REST API ready to use - only a dream? Not really - we've built an API to start with on top of the MVP structure. DoozR comes with the predefined route yourdomain.com/Api/. Start developing you own API so easily.</p>
        </div>
    </div>

    <hr>

    <a name="services"></a>
    <div class="row">
        <div class="col-lg-12">
            <p style="height: 88px;">&nbsp;</p>
            <h2>Services</h2>
        </div>
    </div>
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Form</h3>
            <p>This service is a powerful tool which enables you to collect information from visitors easy as never before. High usability, secure forms, tokens, pagination and many more...</p>
            <p><a class="btn btn-default" href="form.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>I18n</h3>
            <p>DoozR provides I18n support from URL/URI, over routing down to the templates. Every single part can be translated/localized - gettext support.</p>
            <p><a class="btn btn-default" href="i18n.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Oauth2</h3>
            <p>Fully standard compliant Oauth2 service with client & server. The demo of this service is a small application.</p>
            <p><a class="btn btn-default" href="oauth2.php">Big Info before &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Datetime</h3>
            <p>Sometimes it's pain to handle Dates/Time-Values and calculate with or convert them to other formats. This service provides all the functionality you need to handle Date/Time-values easily.</p>
            <p><a class="btn btn-default" href="database.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>REST</h3>
            <p>The REST service is a simple transformer service which transforms a HTTP request to a valid API request which for example is then ready to be passed to DoozR_Base_Presenter_Rest.</p>
            <p><a class="btn btn-default" href="rest.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Compact</h3>
            <p>The compact service helps you compressing JavaScript- & CSS-Files and combining single files to a large one to reduce the amount of requests and reduce the load on the server. This service can be used by adding a route to DoozR e.g. <i>/Compress/</i>.</p>
            <p><a class="btn btn-default" href="compact.php">Try &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>ACL</h3>
            <p>This service provides features for handling permissions of a user or a group. The service provides a nice fluent API to ask for and check permissions. All permissions are stored in a single integer.</p>
            <p><a class="btn btn-default" href="acl.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Session</h3>
            <p>This service is a handy helper for easy and secure sessions with PHP. This service is default enabled in Web-Environment.</p>
            <p><a class="btn btn-default" href="session.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Template</h3>
            <p>Proxy to PHPTAL library. This service provides an easy access to PHPTAL library of DoozR. Currently only PHPTAL support. More libraries will be added soon.</p>
            <p><a class="btn btn-default" href="template.php">Try &raquo;</a></p>
        </div>
    </div>

    <!-- Example row of columns -->
    <div class="row">
        <div class="col-lg-4">
            <h3>Configreader</h3>
            <p>This service provides a recursive reader for configuration files - supports: INI-, XML- and JSON format.</p>
            <p><a class="btn btn-default" href="configreader.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Cache</h3>
            <p>The caching service is one of the core components of DoozR - realized as a service. It supports caching in filesystem, memcached, Redis and is heavily used by DoozR to improve its performance run for run.</p>
            <p><a class="btn btn-default" href="cache.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Crypt</h3>
            <p>The Crypt service provides en- & decryption with AES container. This service is used by Session service, Filesystem service, Oauth2 service and by Cache service for en-/decryption of content.</p>
            <p><a class="btn btn-default" href="crypt.php">Try &raquo;</a></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <h3>Filesystem</h3>
            <p>This service provides easy access to the filesystem and default system path'. It supports a virtual mode which is realized using the Virtualfilesystem service. It provides an OOP fluent interface and supports persistent read and write which brings good performance (we use it for filesystem-logging! ;).</p>
            <p><a class="btn btn-default" href="filesystem.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Virtualfilesystem</h3>
            <p>This service provides access to a virtualfilesystem. We've built this service to fetch all write operations to filesystem while processing request and response and flushing write op's after response was served. This also increases the speed of a response served.</p>
            <p><a class="btn btn-default" href="virtualfilesystem.php">Try &raquo;</a></p>
        </div>
        <div class="col-lg-4">
            <h3>Password</h3>
            <p>This service helps you generating all the different kinds of passwords. With special chars or easy rememberable. It also validates hashes against submitted passwords and checks how similar two passed passwords are.</p>
            <p><a class="btn btn-default" href="password.php">Try &raquo;</a></p>
        </div>
    </div>

    <hr>

    <a name="requirements"></a>
    <div class="row">
        <div class="col-lg-12">
            <p style="height: 88px;">&nbsp;</p>
            <h2>Requirements</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <h3>System environment</h3>
            <p>
                <ul>
                    <li class="text-info">Apache (>= 1.0.0)</li>
                    <li> + mod_rewrite</li>
                    <li class="text-info">PHP >= 5.1 (we recommend >= 5.4)</li>
                    <li class="text-info">memcache (PHP Extension) + memcached (Daemon) | or Redis</li>
                </ul>
                Optional
                <ul>
                    <li>Redis (as memcache replacement)</li>
                    <li>igbinary Extension (for EAP branch)</li>
                    <li>For <a href="http://nginx.org/" target="_blank">nginx</a> you will need to convert the mod_rewrite rules</li>
                </ul>
            </p>
        </div>
        <div class="col-lg-6">
            <h3>Recommendet Settings</h3>
            <p>
                <h4>php.ini</h4>
                <dl class="dl-horizontal">
                    <dt>memory_limit</dt>
                    <dd>8M</dd>
                    <dt>xdebug.max_nesting_level</dt>
                    <dd>50</dd>
                </dl>
            </p>
        </div>
    </div>

    <hr>

    <a name="install"></a>
    <div class="row">
        <div class="col-lg-6">
            <p style="height: 88px;">&nbsp;</p>
            <h2>Install</h2>
            <p>To install DoozR just clone this repository <b>https://github.com/clickalicious/DoozR.git</b> into a document root:</p>
            <p><pre>git clone https://github.com/clickalicious/DoozR.git .</pre></p>
            <p>if you do so you won't need to <a href="#install">configure</a> the routing. After cloning you only need to call the root URI of your installation e.g. http://localhost/</p>
        </div>
        <div class="col-lg-6">
            <p style="height: 88px;">&nbsp;</p>
            <h2>Configure</h2>
            <p>A fresh DoozR installation should run without any need to be configured. But in some cases you need to adjust the path to document root. This is done by editing the <strong>.htaccess</strong> file in DoozR root folder.</p>
            <p>
<pre>
#-----------------------------------------------------------
# PATH CONFIGURATION/SETUP HERE
#-----------------------------------------------------------
RewriteBase <b>/Framework/</b> <-- HERE (CHANGE THIS TO MATCH YOUR ENVIRONMENT)

RewriteRule .* - [E=DOOZR_PATH_APP:../App/Data/Public/www/]
RewriteRule .* - [E=DOOZR_ROUTER:Route.php]
</pre>
            </p>
        </div>
    </div>

    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />


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
