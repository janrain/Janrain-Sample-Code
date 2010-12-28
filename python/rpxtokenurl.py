# This code sample shows how to make the auth_info API call using Python.

import urllib
import urllib2

# json is native in python 2.6, but must be installed for previous versions
import json

# Step 1) Extract the token from your environment.  If you are using app engine,
# you'd do something like:
#
# token = self.request.get('token')
#
# If you are using Django you'd do this:
#
# def rpx_response(request, token):
#   ...
#
# Or, if you are using raw CGI you'd do something like this:
#
# import cgi
# form = cgi.FieldStorage()
# token = form['token'].value

# Step 2) Now that we have the token, we need to make the api call to auth_info.
# auth_info expects an HTTP Post with the following paramters:
api_params = {
    'token': token,
    'apiKey': 'REPLACE_WITH_YOUR_RPX_API_KEY',
    'format': 'json',
}

# make the api call
http_response = urllib2.urlopen('https://rpxnow.com/api/v2/auth_info',
                                urllib.urlencode(api_params))

# read the json response
auth_info_json = http_response.read()

# Step 3) process the json response
auth_info = json.loads(auth_info_json)

# Step 4) use the response to sign the user in
if auth_info['stat'] == 'ok':
    profile = auth_info['profile']
   
    # 'identifier' will always be in the payload
    # this is the unique idenfifier that you use to sign the user
    # in to your site
    identifier = profile['identifier']
   
    # these fields MAY be in the profile, but are not guaranteed. it
    # depends on the provider and their implementation.
    name = profile.get('displayName')
    email = profile.get('email')
    profile_pic_url = profile.get('photo')

    # actually sign the user in.  this implementation depends highly on your
    # platform, and is up to you.
    sign_in_user(identifier, name, email, profile_pic_url)
   
else:
    print 'An error occured: ' + auth_info['err']['msg']


 
