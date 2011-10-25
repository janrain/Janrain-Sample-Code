// This code sample shows how to make the auth_info API call using Node.js.

var fs = require('fs'),
    http = require('http'),
    https = require('https'),
    querystring = require('querystring');

http.createServer(function(request, response) {
  switch (request.method) {

    case 'GET':  // Serve the static page
      fs.readFile('./index.html', function(error, content) {
        response.writeHead(200, {
          'Content-Type': 'text/html'
        });
        response.end(content, 'utf-8');
      });
      break;

    case 'POST':  // Recieve the token, and request the user's profile from RPX.
      request.setEncoding('utf8');
      request.on('data', function(chunk) {
        var token = querystring.parse(chunk)['token'];
        console.log("Recieved token: " + token);
        // Now that we have the token, we need to make the api call to auth_info.
        var query_params = querystring.stringify({
          // auth_info expects an HTTP Post with the following paramters:
          apiKey: 'REPLACE_WITH_YOUR_RPX_API_KEY',
          token: token
        });
        var url = {
          protocol: "https",
          host: "rpxnow.com",
          path: "/api/v2/auth_info?" + query_params
        };
        console.log("Requesting URL: " + url.protocol + "://" + url.host + url.path);
        https.get(url, function(res) {
          response.writeHead(200, {
            'Content-Type': 'text/javascript'
          });
          res.setEncoding('utf8');
          res.on('data', function(chnk) {
            response.write(chnk);
            console.log(chnk);
          });
          res.on('end', function() {
            response.end();
          });
        });
      });
      break;
  }
}).listen(5000);

console.log('Server running at http://localhost:5000/');
