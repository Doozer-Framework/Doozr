# The important DoozR controllers
*DoozR* is based on two layers to provide access to the **Back** & the **Front**. These two controllers are called:

    DoozR_Controller_Front
	DoozR_Controller_Back

The *Front-controller* is used to handle everything that *comes-from* and *goes-to* the client. The *Back-controller* is used to handle everything that is related to **MVP**, it dispatches the retrieved request to the three parts **Model**, **View** and **Presenter**. Every part is then able to process the request and give back a single response via the the *Front-controller*. The *Front-controller* makes the whole request available including *header*, *request-parameter* and provide you an smart interface for sending *data* (response) back to the client including correct *header*, *response* with *response-code* and so on.

## Front
The Front-controller is the layer between the *Client* (CLI|WEB) which request a resource and the Back-controller which dispatches the incoming request (preprocessed) to *Model*, *View* and *Presenter*. The Front-controller is also responsible for sending data as response back to the client. These are some methods provided by the Front-controller:

    getRuntimeEnvironment() [returns the runtimeEnvironment DoozR runs, CLI or WEB]
    getRequest()     [returns the Request-object of current request]
    getResponse()    [returns the Response-object for current request]

As you may assume the *Front-controller* is more a Decorator which provides access to two seperate classes

    DoozR_Request_(Web|Cli)
	DoozR_Response_(Web|Cli)

Those classes represents the request object and the response object - based on the current running runtimeEnvironment - which can be either *WEB* or *CLI*.

## Back
The *Back-controller*.
