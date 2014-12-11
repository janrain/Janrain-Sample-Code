// This code sample shows how to make the auth_info API call using Node.js.

var http = require('http');
var fs = require('fs');
var querystring = require('querystring');
var ecstatic = require('ecstatic');
var request = require('request');
var concatStream = require('concat-stream');

var staticHandler = ecstatic(__dirname);

http.createServer(function(request, response) {
  if (request.method === "GET") return staticHandler(request, response);
  // Wait for the POST to finish, parse the token, and request the user's profile from RPX.
  request.pipe(concatStream(function(err, body) {
    var token = querystring.parse(body)['token'];
    console.log("Recieved token: " + token);
    // Now that we have the token, we need to make the api call to auth_info.
    var query_params = querystring.stringify({
      // auth_info expects an HTTP Post with the following parameters:
      apiKey: 'REPLACE_WITH_YOUR_RPX_API_KEY',
      token: token
    });
    var url = "https://rpxnow.com/api/v2/auth_info?" + query_params;
    console.log("Requesting URL: ", url);
    request(url).pipe(response);
  }));
  }
)
.listen(5000);

console.log('Server running at http://localhost:5000/');