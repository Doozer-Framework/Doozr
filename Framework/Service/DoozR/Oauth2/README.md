# Module: Oauth2
## (DoozR\_Oauth2\_Module)
This is the information for the DoozR default module "Oauth2". *Oauth2* provides ...
Ged
    A service to enable Oauth2 features in DoozR projects


    // REQUEST a 
    http://www.testdomain.com/?response_type=code&client_id={{app.parameters.client_id}}&redirect_uri={{ url('authorize_redirect')|url_encode() }}&state={{session_id}}





INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ("fooApp", "the-api-key", "http://www.foo.com/");




