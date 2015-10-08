# Janrain Examples
Basic examples of token URLs that can be used with Janrain's Social Login
service.

No warranty, use at your own risk.

## Janrain Social Login Widget Examples
You can find example implementations of the Janrain Social Login widget in the
`widget-examples` folder, along with a tiny Node server and token url
implementation. To see the examples in action, do the following:

1. The first thing you'll want to do is install [Node.js](https://nodejs.org/).
1. Next, open a terminal and run the following commands:
1. `git clone git@github.com:janrain/Janrain-Sample-Code.git`
1. `cd Janrain-Sample-Code/widget-examples`
1. `npm install`
1. Edit line 7 of `server.js`, replacing `_PLACE_YOUR_API_KEY_HERE_` with
   your Janrain API key.
1. Edit line 13 of `modal-non-redirect-signin.html`, replacing
   `_YOUR_APP_NAME_HERE_` with your Janrain app name. Your app name will be the
   subdomain section of your Application Domain (which you can find by visiting
   the settings screen on the Janrain Dashboard).
1. Now start the server with `node server.js`
1. That's it! You should now be able to access the examples at
   [http://localhost:3000](http://localhost:3000).

## Third Party Examples
### Haskell
* [authenticate](http://hackage.haskell.org/cgi-bin/hackage-scripts/package/authenticate)


### Perl
* [Net-API-RPX-0.01](http://search.cpan.org/~konobi/Net-API-RPX-0.01/)
