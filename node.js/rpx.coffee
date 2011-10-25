# This code sample shows how to make the auth_info API call using Node.js.

[fs, http, https, querystring] =
['fs', 'http', 'https', 'querystring'].map require

http.createServer (request, response)->
  
  switch request.method

    when 'GET'  # Serve the static page
     fs.readFile './index.html', (error, content)->
       response.writeHead 200, { 'Content-Type': 'text/html' }
       response.end content, 'utf-8'

    when 'POST' # Recieve the token, and request the user's profile from RPX.
      request.setEncoding 'utf8'
      request.on 'data', (chunk)->
        token = querystring.parse(chunk)['token']
        console.log "Recieved token: " + token
        # Now that we have the token, we need to make the api call to auth_info.
        query_params = querystring.stringify
          # auth_info expects an HTTP Post with the following paramters:
          apiKey: 'REPLACE_WITH_YOUR_RPX_API_KEY'
          token: token
        url =
          protocol: "https"
          host: "rpxnow.com"
          path: "/api/v2/auth_info?" + query_params
        console.log "Requesting URL: #{url.protocol}://" + url.host + url.path
        # make the api call
        https.get url, (res)->
          response.writeHead 200, { 'Content-Type': 'text/javascript' } # Or try 'application/json' if supported.
          res.setEncoding 'utf8'
          res.on 'data', (chnk)->
            response.write chnk
            console.log chnk
          res.on 'end', ->
            response.end()
         
.listen(5000)

console.log 'Server running at http://localhost:5000/'
