# Service: Oauth2
## (Doozr\_Oauth2\_Service)
This is the information for the Doozr default service "Oauth2". *Oauth2* provides ...
Ged
    A service to enable Oauth2 features in Doozr projects


    // REQUEST a 
    http://www.testdomain.com/?response_type=code&client_id={{app.parameters.client_id}}&redirect_uri={{ url('authorize_redirect')|url_encode() }}&state={{session_id}}





INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ("fooApp", "the-api-key", "http://www.foo.com/");




