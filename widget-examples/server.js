var querystring = require('querystring');
var https = require('https');
var express = require('express');
var app = express();

// Place your Janrain API key here.
var janrain_api_key = '_PLACE_YOUR_API_KEY_HERE_';

// This serves up the static files that contain the HTML and JavaScript.
app.use(express.static('.'));

// This `profile` endpoint is where we will send our access token from the
// Janrain Social Login service.
app.get('/profile', function(req, response) {
    // Extract the token from the query parameters.
    var token = req.query.token;

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
            janrain_payload = JSON.parse(data);
            // The full response can contain authentication secrets (Pro and
            // Enterprise only), so we'll just send the profile back to the
            // browser.
            response.send(janrain_payload.profile)
        });
    });

    post_req.write(post_data);
    post_req.end();

});

// Start the server!
var server = app.listen(3000, function() {
    var host = server.address().address;
    var port = server.address().port;

    console.log('Example app listening at http://' + host + ':' + port, host, port);
});
