var querystring = require('querystring');
var https = require('https');
var express = require('express');
var bodyParser = require('body-parser')

var app = express();
app.use(bodyParser.urlencoded({
  extended: true
}));

// Place your Janrain API key here.
var janrain_api_key = '_API_KEY_';

// This serves up the static files that contain the HTML and JavaScript.
app.use(express.static('.'));

// This function will call Janrain's auth_info endpoint, and invoke a callback
// function with the response.
function callAuthInfo(token, callback) {
    // Construct the payload to send to Janrain's auth_info endpoint.
    var post_data = querystring.stringify({
        'token': token,
        'apiKey': janrain_api_key,
        'format': 'json'
    });

    var post_options = {
        host: 'rpxnow.com',
        path: '/api/v2/auth_info',
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Content-Length': post_data.length
        }
    };

    // Create the post request.
    var post_req = https.request(post_options, function(res) {
        var data = '';
        res.setEncoding('utf8');
        // Append data as we receive it from the Janrain server.
        res.on('data', function(d) {
            data += d;
        });
        // Once we have all the data, we can parse it and return the data we
        // want.
        res.on('end', function() {
            callback(JSON.parse(data));
        });
    });

    post_req.write(post_data);
    post_req.end();
}

// This endpoint is where we will send our access token during non-redirect
// login.
app.get('/profile', function(req, res) {
    callAuthInfo(req.query.token, function(auth_info) {
        // The full response can contain authentication secrets (Pro and
        // Enterprise only), so we'll just send the profile back to the
        // browser.
        res.send(auth_info.profile)
    });
});

// This endpoint is where the Janrain Login service will post the access token
// during redirect login.
app.post('/profile', function(req, res) {
    callAuthInfo(req.body.token, function(auth_info) {
        // This is where we would use the itentifier from the auth_info response
        // to log the user into our site. For the purposes of this example,
        // we'll simply output the full response.
        res.send('<pre>' + JSON.stringify(auth_info, null, 2) + '</pre>')
    });
})

// Start the server!
var server = app.listen(3000, function() {
    var port = server.address().port;

    console.log('Janrain Samples available at http://localhost:' + port);
});
