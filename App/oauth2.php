<?php

    // current context
    // => this is the app which requests an oauth token

    // some setup/config for requesting an authorization
    $appParametersClientId = 'demoapp';        // should this be a previously generated and told API developer key?
    $appAuthorizeRedirect  = 'http://www.doozr.de:81/App/oauth2-authorization-response.php';

    // start a session for state
    session_start();

?>
<html>
<head>
    <title>DoozR - OAuth2 Demonstration Step 1/X</title>
</head>
<body style="background-color: darkolivegreen;">
<h1>Demonstration Application</h1>

Welcome to DoozR's OAuth2.0 Demonstration Application!<br />
This is an application that demos some of the basic OAuth2.0 workflows.<br />
The Grant Type used in this example is the Authorization Code grant type. This is the most common workflow for OAuth2.0.<br />
Clicking the "Authorize" button below will send you to an OAuth2.0 Server to authorize this App named "<?php echo $appParametersClientId; ?>":
<p>
    <a href="oauth2-ask-for-authorization.php?response_type=code&client_id=<?php echo $appParametersClientId; ?>&redirect_uri=<?php echo $appAuthorizeRedirect; ?>&state=<?php echo session_id(); ?>">Authorize</a>
</p>
</body>
</html>