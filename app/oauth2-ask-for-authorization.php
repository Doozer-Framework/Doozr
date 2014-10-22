<?php
/***********************************************************************************************************************
 *
 * DEMONSTRATION
 * Service: Oauth2
 *
 **********************************************************************************************************************/

    // current context
    // => this is the server (service-provider) which receives a request
    // the place where the user will see a message and is required to either
    // allow or reject access ...

/**
 * Bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';

/**
 * Instanciate DoozR
 */
$DoozR = DoozR_Core::getInstance();

/**
 * Get registry containing DoozR's base object instances
 */
$registry = DoozR_Registry::getInstance();


require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Oauth2/Service.php';


// the actions supported by object
$actions = array(
'create',
'read',
'update',
'delete'
);


if ($_POST) {

    // create a new object to generate a permissions set
    $oauth2 = DoozR_Loader_Serviceloader::load('oauth2', DoozR_Oauth2_Service::MODE_SERVER, 'PDO', array(
            'dsn'  => 'mysql:dbname=test;host=127.0.0.1',
            'user' => 'root',
            'pass' => ''
        )
    );

    $get = $_GET;
    $post = $_POST->getArray();

    $request = OAuth2\Request::createFromGlobals($get, $post);
    $response = new OAuth2\Response();

    // validate the authorize request
    if (!$oauth2->validateAuthorizeRequest($request, $response)) {
        $response->send();
        die;
    }

    // print the authorization code if the user has authorized your client
    $isAuthorized = ($_POST->authorize() === '1');

    $oauth2->handleAuthorizeRequest($request, $response, $isAuthorized);

    if ($isAuthorized) {
        // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
        //$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
        //exit("SUCCESS! Authorization Code: $code");
    }

    $response->send();

    // Add the "Authorization Code" grant type (this is where the oauth magic happens)
    //pred($oauth2->getStorage(0));

    $clientId = $_POST->client_id();
    $redirectUri = $_POST->redirect_uri();
    $state = $_POST->state();
} else {
    $clientId = $_GET->client_id();
    $redirectUri = $_GET->redirect_uri();
    $state = $_GET->state();

}

?>
<html>
<head>
    <title>DoozR - OAuth2 Demonstration Step 2/X</title>
</head>
<body style="background-color: grey;">
<h1>Foo</h1>

The app "<?php echo $clientId ?>" wants to access the following data:
    <ul>
        <li>Life</li>
        <li>Girlfriend</li>
        <li>Bankaccount</li>
    </ul>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?client_id=<?php echo $clientId; ?>&redirect_uri=<?php echo $redirectUri; ?>&response_type=code&state=<?php echo $state; ?>" method="post">
        <input class="button authorize" value="Yes, I Authorize This Request" type="submit">
        <input name="authorize" value="1" type="hidden">
    </form>
    <form id="cancel" action="<?php echo $_SERVER['PHP_SELF']; ?>?client_id=<?php echo $clientId; ?>&redirect_uri=<?php echo $redirectUri; ?>&response_type=code&state=<?php echo $state; ?>" method="post">
        <a href="#" onclick="document.getElementById('cancel').submit()">cancel</a>
        <input name="authorize" value="0" type="hidden">
    </form>
</body>
</html>
