// simplified version of janrain-init.js to highlight key aspects of code-for-token solution
  
// ...
janrain.settings.capture.responseType = 'code';
// ...
  
function janrainCaptureWidgetOnLoad() {
    // Function to handle the response from an AJAX cals to the server to get
    // new access tokens and create the client-side session.
    var createSessionWithTokenFromServer = function(response) {
        if (response.access_token) {
            janrain.capture.ui.createCaptureSession(response.access_token);
            var expires = new Date(response.expires * 1000);
            console.log("Server-side session found. Access token " + response.access_token + " expires on " + expires);
        } else {
            console.log("Server-side session not found." + response);
        }
    };
 
    // When the end-user logs in the authorization code must be passed to the
    // server side where it will be exchanged for access and refresh tokens and
    // stored in the PHP session.
    janrain.events.onCaptureLoginSuccess.addHandler(function(result) {
        console.log("Passing authorization code to server-side session: " + result.authorizationCode);
        $.post("exchange_code.php", {
            'authorization_code': result.authorizationCode,
            'redirect_uri': janrain.settings.capture.redirectUri
        }, function(response) {
            createSessionWithTokenFromServer(response);
        });
    });
 
    // When the end-user ends the client-side session, send a request to the
    // server-side PHP script to end the server-side session.
    janrain.events.onCaptureSessionEnded.addHandler(function(result) {
        $.post("end_session.php");
    });
 
    // The token stored client-side may expire before a new access token
    // is requested from the server (page refresh). This will cause an error
    // if trying to use the token (such as "Save" on edit profile). Request a
    // refreshed token and re-render the screen.
    janrain.events.onCaptureSaveFailed.addHandler(function(result) {
        if (result.statusMessage == "invalidAccessToken") {
            $.getJSON('get_token.php', function(response) {
                createSessionWithTokenFromServer(response);
                janrain.capture.ui.renderScreen(result.screen);
            });
        }
    });
 
    // Before starting capture.ui, request the access token from the server-side
    // session. The server handles refreshing the token when it expires.
    $.getJSON('get_token.php', function(response) {
        createSessionWithTokenFromServer(response);
        janrain.capture.ui.start();
    });
}
