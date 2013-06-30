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
    $appTokenRedirect  = 'http://www.doozr.de:81/App/oauth2-authorization-response.php';  // must be the same as 1st req
    $client_id         = 'demoapp';

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

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Oauth2/Service.php';

?>
<html>
<head>
    <title>DoozR - OAuth2 Demonstration Step 3/X</title>
</head>
<body style="background-color: darkolivegreen;">
    <?php
        if ($_GET->error()) {
            $headline = ($_GET->error_description()) ? $_GET->error_description() : 'Unknown error';
            $success  = false;
        } else {
            $headline = 'Authorization code retrieved!';
            $code     = $_GET->code();
            $success  = true;
        }
    ?>

    <h1><?php echo $headline; ?></h1>

    <?php
        if ($success === true) {
    ?>
        <p>We've received an Authorization-Code from the OAuth2.0 Server:</p>
        <p>Authorization Code: <?php echo $code; ?></p>
        <p>Now we exchange this Authorization-Code against an Access-Token:</p>
        <p>
            <form action="oauth2-request-token.php" method="post">
                <input class="button authorize" value="Request a token" type="submit">
                <input type="hidden" name="code" value="<?php echo $code; ?>">
                <input type="hidden" name="grant_type" value="authorization_code" ?>
                <input type="hidden" name="redirect_uri" value="<?php echo $appTokenRedirect; ?>">
                <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                <input type="hidden" name="client_secret" value="the-api-key">
            </form>
        </p>
        <div class="help">
            <em>Usually this is done behind the scenes, but we're going step-by-step so you don't miss anything!</em>
        </div>
    <?php
        } else {
    ?>
        <p>The access was rejected. This app can't access any user data @ ...</p>
    <?php
        }
    ?>
</body>
</html>