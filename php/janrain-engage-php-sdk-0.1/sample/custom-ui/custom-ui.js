function startLogin(providerbutton) {
  var provider = providerbutton.replace('_button','');
  var oidbase = '';
  var getoidpart = 'false';
  var extra = '';
  var action = '';
  switch (provider) {
    case 'aol':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'https://openid.aol.com/{screenname}';
      getoidpart = 'true';
      break;
    case 'blogger':
      action = 'https://'+realm+'/openid/start';
      oidbase = '{blogger domain}';
      getoidpart = 'true';
      break;
    case 'facebook':
      action = 'https://'+realm+'/facebook/connect_start';
      extra = '<input type="hidden" name="ext_perms" value="publish_stream,email,offline_access" />';
      break;
    case 'flickr':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://flickr.com/';
      break;
    case 'google':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'https://www.google.com/accounts/o8/id';
      break;
    case 'hyves':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://hyves.nl/';
      break;
    case 'linkedin':
      action = 'https://'+realm+'/linkedin/start';
      break;
    case 'livejournal':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://{username}.livejournal.com/';
      getoidpart = 'true';
      break;
    case 'myopenid':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://myopenid.com/';
      break;
    case 'myspace':
      action = 'https://'+realm+'/myspace/start';
      break;
    case 'netlog':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://netlog.com/{nickname}';
      getoidpart = 'true';
      break;
    case 'openid':
      action = 'https://'+realm+'/openid/start';
      oidbase = '{openid url}';
      getoidpart = 'true';
      break;
    case 'paypal':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'https://openid.paypal-ids.com/';
      break;
    case 'twitter':
      action = 'https://'+realm+'/twitter/start';
      oidbase = 'https://openid.aol.com/{screenname}';
      break;
    case 'symantec':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://pip.verisignlabs.com/';
      break;
    case 'windowslive':
      action = 'https://'+realm+'/liveid/start';
      break;
    case 'wordpress':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://{username}.wordpress.com/';
      getoidpart = 'true';
      break;
    case 'yahoo':
      action = 'https://'+realm+'/openid/start';
      oidbase = 'http://me.yahoo.com/';
      break;
  }
  loginpop = window.open('','name','height=470,width=630');
  if (window.focus) {loginpop.focus()}
  loginpop.document.write('<html><head><title>Login</title>');
  loginpop.document.write('<script src="./custom-ui.js"></script>');
  loginpop.document.write('</head>');
  loginpop.document.write('<body>');
  loginpop.document.write('<form id="'+provider+'" name="'+provider+'" action="'+action+'" method="GET">');
  if (oidbase != null && oidbase != '') {
    loginpop.document.write('<input type="hidden" name="openid_identifier" value="'+oidbase+'" />');
  }
  loginpop.document.write('<input type="hidden" name="token_url" value="'+tokenurl+'" />');
  if (extra != null && extra != '') {
    loginpop.document.write(extra);
  }
  loginpop.document.write('</form>');
  loginpop.document.write('<script type="text/javascript">launchLogin("'+provider+'",'+getoidpart+');</script>');
  loginpop.document.write('</body></html>');
  loginpop.document.close();
}
function launchLogin(provider, getoidpart) {
  if ( getoidpart == true ) {
    var theform = document.getElementById(provider);
    var lefty = theform.openid_identifier.value.indexOf('{');
    var righty = theform.openid_identifier.value.indexOf('}');
    var length = righty - lefty;
    var special = theform.openid_identifier.value.substr(lefty, length+1); 
    var oidpart = getCookie("engageOidPart");
    if (oidpart==null || oidpart=="") {
      var prefill = special;
    } else {
      var prefill = oidpart;
    }
    var trimspecial = theform.openid_identifier.value.substring(lefty+1, righty);    
    var theprompt = "What is your "+provider+" "+trimspecial+"?";
    var oidpart = prompt(theprompt, prefill);
    if (oidpart!=null && oidpart!="" && oidpart != special) {
      setCookie("engageOidPart", oidpart, 364);
      var rawoid = theform.openid_identifier.value;
      var oid = rawoid.replace(special,oidpart);
      theform.openid_identifier.value = oid;
    } else {
      self.close();
    }
  }
  setCookie('engageProvider',provider,364);
  document.getElementById(provider).submit();
}
function setCookie(c_name,value,exdays) {
  var exdate=new Date();
  exdate.setDate(exdate.getDate() + exdays);
  var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
  document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name) {
  var i,x,y,ARRcookies=document.cookie.split(";");
  for (i=0;i<ARRcookies.length;i++) {
    x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
    y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
    x=x.replace(/^\s+|\s+$/g,"");
    if (x==c_name) {
      return unescape(y);
    }
  }
}
function checkProviderCookie() {
  var provider=getCookie("engageProvider");
  if (provider!=null && provider!="") {
   thebutton = document.getElementById(provider + "_button");
   thebutton.className += " return_exp";
  }
}
