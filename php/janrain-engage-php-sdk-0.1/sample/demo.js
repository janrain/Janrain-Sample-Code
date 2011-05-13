function setupInit() {
  var setupGet = new XMLHttpRequest();
  setupGet.onreadystatechange=function() {
    if (setupGet.readyState==4 && setupGet.status==200) {
      var setupData = JSON.parse(setupGet.responseText);
      if (setupData.stat == 'ok') {
        var iframeSrc = '[appDomain]/openid/embed?token_url=[baseUrl]token%2F&flags=stay_in_window,hide_sign_in_with';
        window.jnBaseUrl = ''+window.location;
        var encBaseUrl = encodeURIComponent(window.location);
        window.jnAppId = setupData.settings.app_id;
        iframeSrc = iframeSrc.replace('[appDomain]', setupData.settings.application_domain);
        iframeSrc = iframeSrc.replace('[baseUrl]', encBaseUrl);
        document.getElementById('the_widget').src = iframeSrc;
      }
    }
  }
  setupGet.open('GET','setup.php',true);
  setupGet.send();
}
function signInOut() {
  if (window.userSignedIn === true) {
    signOut();
  } else {
    hideById('sign_in_feedback');
    contentById('the_content','');
    hideById('the_content');
    if (window.the_widget_url != undefined) {
      document.getElementById('the_widget').src = window.the_widget_url;
    }
    showById('register');
    showById('sign_in');
    showById('the_widget');
    contentById('instructions', '', false);
    contentById('instructions', 'Here we will use Engage to collect data to fill out the form on the right.<br>', true);
    contentById('instructions', 'Sign in with an Engage provider on the left.<br>', true);    
  }
}
function authDone(doneFrame){
  signIn();
  if (window.userSignedIn === true) {
    return false;
  }
  if (doneFrame == true) {
    if (document.getElementById('the_widget').src != '') {
      window.the_widget_url = document.getElementById('the_widget').src;
    }
    document.getElementById('the_widget').src = '';
    hideById('the_widget');
  }
  var engageSession = new XMLHttpRequest();
  engageSession.onreadystatechange=function() {
    if (engageSession.readyState==4 && engageSession.status==200) {
      var sessionData = JSON.parse(engageSession.responseText);
      if (doneFrame == true) {
        contentById('instructions', '', false);        
        contentById('instructions', 'We recived a JSON string with the Engage data:<br><pre>'+engageSession.responseText+'</pre>', true);
        contentById('sign_in_feedback', '');
        if (sessionData.profile.photo != null && sessionData.profile.photo != '') {
          contentById('sign_in_feedback', '<img class="avatar" src="'+sessionData.profile.photo+'" />', true);
          contentById('sign_in_feedback', '<p>'+sessionData.profile.preferredUsername+'</p>', true);
        }
        contentById('sign_in_feedback', '<p>You are now connected to '+sessionData.profile.providerName+'.</p>', true);            
        showById('sign_in_feedback');        
      }
      if (sessionData.user == null || sessionData.user == '') {
        contentById('sign_in_label', 'Please complete the form and click register.');
        formHelper(sessionData);
      } else {
        hideById('sign_in');
      }
    }
  }
  engageSession.open('GET','get-session.php',true);
  engageSession.send();
}
function formHelper(sessionData) {
  if (sessionData != null && sessionData != '') {
    var form_help = new Array(); //abuse as assoc, do not loop this "array"
    form_help['user_name'] = '';
    form_help['first_name'] = '';
    form_help['last_name'] = '';
    form_help['email'] = '';
    form_help['profile_url'] = '';
    contentById('instructions', 'Getting this data marks the end of our Engage authentication. What your site <br>', true);    
    contentById('instructions', 'does with the data is up to you.<br><br>', true);    
    contentById('instructions', 'For this demo we converted it into an object and matched it to some of the fields on the form.<br>', true);
    contentById('instructions', 'This data is then filled into the form to ease registration. Notice that we <br>', true);    
    contentById('instructions', 'provided clear feedback to the user telling them that the connection was made.<br><br>', true);    
    contentById('instructions', 'Go ahead and finish the form and click register.<br>', true);    
    signIn();
    if (typeof sessionData.profile == 'object') {
      if (isValidString(sessionData.profile.preferredUsername)) {
        form_help['user_name'] = sessionData.profile.preferredUsername;
      }
      if (typeof sessionData.profile.name == 'object') {
        if (isValidString(sessionData.profile.name.formatted)) {
          var formatted = sessionData.profile.name.formatted;
          var nameSplit = formatted.split(' ');
          form_help['first_name'] = nameSplit[0];
          nameSplit[0] = '';
          form_help['last_name'] = nameSplit.join(' ');
        }
        if (isValidString(sessionData.profile.name.givenName)) {
          form_help['first_name'] = sessionData.profile.name.givenName;
        }
        if (isValidString(sessionData.profile.name.familyName)) {
          form_help['last_name'] = sessionData.profile.name.familyName;
        }
      }
      if (isValidString(sessionData.profile.email)) {
        form_help['email'] = sessionData.profile.email;
      }
      if (isValidString(sessionData.profile.verifiedEmail)) {
        form_help['email'] = sessionData.profile.verifiedEmail;
      }
      if (isValidString(sessionData.profile.url)) {
        form_help['profile_url'] = sessionData.profile.url;
      }
    }
    for(var i=0;i<document.forms['reg_form'].elements.length;i++){
      if (form_help[document.forms['reg_form'].elements[i].id] != undefined && document.forms['reg_form'].elements[i].value == ''){
        document.forms['reg_form'].elements[i].value = form_help[document.forms['reg_form'].elements[i].id];
      }
    }
  }
}
function validatePost() {
  var doPost = true;
  for(var i=0;i<document.forms['post_form'].elements.length;i++){
    if (document.forms['post_form'].elements[i].value == '' && document.forms['post_form'].elements[i].className.search('required') >= 0){
      contentById('instructions', document.forms['post_form'].elements[i].name + '<br>', true);
      doPost = false;
    }
  }
  if (doPost === true) {
    postPost();
  }
  return false;
}
function validateForm() {
  var doPost = true;
  for(var i=0;i<document.forms['reg_form'].elements.length;i++){
    if (document.forms['reg_form'].elements[i].value == '' && document.forms['reg_form'].elements[i].className.search('required') >= 0){
      contentById('instructions', document.forms['reg_form'].elements[i].name + '<br>', true);
      contentById('reg_form_helper', 'Missing required field.');
      doPost = false;
    }
  }
  if (doPost === true) {
    contentById('reg_form_helper', '');
    postForm();
  }
  return false;
}
function postForm() {
  var engageNonce = new XMLHttpRequest();
  engageNonce.onreadystatechange=function() {
    if (engageNonce.readyState==4 && engageNonce.status==200) {
      var nonceData = JSON.parse(engageNonce.responseText);
      var paramArray = new Array();
      for(var i=0;i<document.forms['reg_form'].elements.length;i++){
        if (document.forms['reg_form'].elements[i].value != ''){
          paramArray.push(document.forms['reg_form'].elements[i].name +'='+ escape(document.forms['reg_form'].elements[i].value));
        }
      }
      paramArray.push('nonce='+nonceData.nonce);
      var regParams = paramArray.join('&');
      var engageReg = new XMLHttpRequest();
      engageReg.onreadystatechange=function() {
        if (engageReg.readyState==4 && engageReg.status==200) {
          var regData = JSON.parse(engageReg.responseText);
          if (regData.stat == 'ok') {
            contentById('instructions', '');
            contentById('the_content', '<div id="the_profile"></div>', false);
            contentById('the_content', 'Your user has been created and the site is now ready to offer the authenticated features and content.<br>', true);
            clearForm('reg');
            hideById('register');                
            signIn();
            userProfile();
          }
        }
      }
      engageReg.open('GET','register.php?'+regParams,true);
      engageReg.send();
    }
  }
  engageNonce.open('GET','nonce.php',true);
  engageNonce.send();
}
function postPost() {
  var postNonce = new XMLHttpRequest();
  postNonce.onreadystatechange=function() {
    if (postNonce.readyState==4 && postNonce.status==200) {
      var nonceData = JSON.parse(postNonce.responseText);
      var paramArray = new Array();
      for(var i=0;i<document.forms['post_form'].elements.length;i++){
        if (document.forms['post_form'].elements[i].value != ''){
          paramArray.push(document.forms['post_form'].elements[i].name +'='+ escape(document.forms['post_form'].elements[i].value));
        }
      }
      paramArray.push('nonce='+nonceData.nonce);
      var postParams = paramArray.join('&');
      var demoPost = new XMLHttpRequest();
      demoPost.onreadystatechange=function() {
        if (demoPost.readyState==4 && demoPost.status==200) {
          var postData = JSON.parse(demoPost.responseText);
          if (postData.stat == 'ok') {
            contentById('instructions', '');
            contentById('the_content', '<div id="the_profile"></div>', false);
            clearForm('post');
            hideById('post');
            signIn(true);
            rpxSocial('Share:',postData.comment,window.jnBaseUrl,'Janrain Engage Sample Site Demo','');
          }
        }
      }
      demoPost.open('GET','post.php?'+postParams,true);
      demoPost.send();
    }
  }
  postNonce.open('GET','nonce.php',true);
  postNonce.send();
}
function userProfile() {
  var siteProfile = new XMLHttpRequest();
  siteProfile.onreadystatechange=function() {
    if (siteProfile.readyState==4 && siteProfile.status==200) {
      var siteProfileData = JSON.parse(siteProfile.responseText);
      if (siteProfileData.user_data.user_name == null || siteProfileData.user_data.user_name == '') {
        contentById('sign_in_label', 'Please retry, starting over.');
        return false;
      }
      hideById('instructions');
      hideById('post');
      contentById('the_content', '<div id="the_profile"></div>');
      contentById('the_profile', '', false);
      contentById('the_profile', siteProfileData.user_data.user_name+', you are connected to '+siteProfileData.authinfo.profile.providerName+'.</p>', true);            
      if (siteProfileData.authinfo.profile.photo != null && siteProfileData.authinfo.profile.photo != '') {
        contentById('the_profile', '<img class="avatar" src="'+siteProfileData.authinfo.profile.photo+'" />', true);
      }
      siteProfileData.user_data.map_data = siteProfileData.map_data;
      var userDataTable1 = CreateDetailView(siteProfileData.user_data,"lightPro",true,'Data stored on this server (database):');
      var userDataTable2 = CreateDetailView(siteProfileData.authinfo.profile,"lightPro",true,'Associated live data from Engage (session):');
      contentById('the_profile', userDataTable1, true);
      contentById('the_profile', userDataTable2, true);
      showById('the_content');        
      showById('the_profile');
    }
  }
  siteProfile.open('GET','get-profile.php',true);
  siteProfile.send();
}
function signIn(home) {
  window.userSignedIn = false;
  document.getElementById("sign_in_out").innerHTML = 'Sign In / Register';
  var checkSignin = new XMLHttpRequest();
  checkSignin.onreadystatechange=function() {
    if (checkSignin.readyState==4 && checkSignin.status==200) {
      var signInData = JSON.parse(checkSignin.responseText);
      if (signInData.stat == 'ok') {
        contentById('user_profile', 'View profile: '+signInData.user_data.user_name );
        hideById('instructions');
        hideById('register');
        hideById('sign_in');
        hideById('the_content');
        contentById('sign_in_out', 'Sign Out');
        contentById('the_content', 'Welcome, enter or update your status.', false);
        showById('user_profile');
        showById('post');
        window.userSignedIn = true;
        if (home == true) {
          goHome();
        }
        showById('the_content');
      }
    }
  }
  checkSignin.open('GET','status.php',true);
  checkSignin.send();
}
function signOut() {
  contentById('user_profile', '');
  var signOutRequest = new XMLHttpRequest();
  signOutRequest.onreadystatechange=function() {
    if (signOutRequest.readyState==4 && signOutRequest.status==200) {
      var signOutData = JSON.parse(signOutRequest.responseText);
      if (signOutData.stat == 'ok') {
        contentById('instructions', 'Signed Out<br>');
        contentById('sign_in_out', 'Sign In / Register');
        hideById('the_content');
        window.userSignedIn = false;
        goHome();
      } 
    }
  }
  signOutRequest.open('GET', 'sign-out.php', true);
  signOutRequest.send();
}
function goHome() {
  hideById('the_profile');
  hideById('sign_in');
  hideById('register');
  hideById('post');
  if (window.userSignedIn === true){
    contentById('instructions', '');
  } else {
    hideById('the_content');
    contentById('instructions', 'Click [Sign In / Register] at the top to begin.');
    showById('instructions');
  }
  showPosts();
  window.postRefreshTimer = setInterval("refreshPosts()", 20000);
}
function refreshPosts() {
  if ( document.getElementById('comments') != null ) {
    showPosts();
  } else {
    clearInterval(window.postRefreshTimer);
  }
}
function showPosts() {
  var postsGet = new XMLHttpRequest();
  postsGet.onreadystatechange=function() {
    if (postsGet.readyState==4 && postsGet.status==200) {
      var postsData = JSON.parse(postsGet.responseText);
      if (postsData.stat == 'ok') {
        var postsTable = createCommentsView(postsData.posts,"comments", 'Visitors:');
        contentById('the_content',postsTable);
        showById('the_content');
      }
    }
  }
  postsGet.open('GET', 'posts.php', true);
  postsGet.send();
}
function createCommentsView(objArray, theme, caption) {
    if (caption != undefined) {
      caption = '<div class="caption">' + caption + '</div>';
    }
    var str = '<div id="' + theme + '">' + caption;
    var count = 0;
    for (var comment in objArray) {
      var imgProvider = '';
      if (objArray[comment].provider != '' && objArray[comment].provider != null){
        imgProvider = '<div class="jn-icon jn-size30 jn-'+objArray[comment].provider+'"></div>';
      }
      str += '<div class="comment"><a class="profile_link" href="'+objArray[comment].profile_url+'" target="_blank">'+imgProvider+objArray[comment].user_name+'</a>';
      str += '<p class="message">'+objArray[comment].comment+'</p></div>';
      count++;
    }
    str += '</div>';
    return str;
}
function CreateDetailView(objArray, theme, enableHeader, caption) {
  // set optional theme parameter
  if (theme === undefined) {
    theme = 'mediumTable';//default theme
  }
  if (enableHeader === undefined) {
    enableHeader = true; //default enable headers
  }
  if (caption != undefined) {
    caption = '<caption>' + caption + '</caption>';
  }
  objArray = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
  var str = '<table class="' + theme + '">' + caption;
  str += '<tbody>';
  var row = 0;
  for (var section in objArray) {
    if (typeof objArray[section] == 'array' || typeof objArray[section] == 'object') {
      for (var index in objArray[section]) {
        if (typeof objArray[section][index] == 'array' || typeof objArray[section][index] == 'object') {
          for (var value in objArray[section][index]) {
            str += (row % 2 == 0) ? '<tr class="alt">' : '<tr>';
            if (enableHeader) {
              str += '<th scope="row">' + value + '</th>';
            }
            str += '<td>' + objArray[section][index][value] + '</td>';
            str += '</tr>';
            row++;
          }
        } else {
          str += (row % 2 == 0) ? '<tr class="alt">' : '<tr>';
          if (enableHeader) {
            str += '<th scope="row">' + index + '</th>';
          }
          str += '<td>' + objArray[section][index] + '</td>';
          str += '</tr>';
          row++;
        }
      } 
    } else {
      str += (row % 2 == 0) ? '<tr class="alt">' : '<tr>';
      if (enableHeader) {
        str += '<th scope="row">' + section + '</th>';
      }
      str += '<td>' + objArray[section] + '</td>';
      str += '</tr>';
      row++;
    }
  }
  str += '</tbody>'
  str += '</table>';
  return str;
}
function rpxSocial (rpxLabel, rpxSummary, rpxLink, rpxLinkText, rpxComment){
  RPXNOW.init({appId: window.jnAppId, xdReceiver: window.jnBaseUrl+'rpx_xdcomm.html'});
  RPXNOW.loadAndRun(['Social'], function () {
    var activity = new RPXNOW.Social.Activity(
     rpxLabel,
     rpxLinkText,
     rpxLink);
    activity.setUserGeneratedContent(rpxComment);
    activity.setDescription(rpxSummary);
    if (document.getElementById('shareimage') != undefined) {
      shareImageSrc = document.getElementById('shareimage').src;
      shareImage = new RPXNOW.Social.ImageMediaCollection();
      shareImage.addImage(shareImageSrc,rpxLink);
      activity.setMediaItem(shareImage);
    }
    RPXNOW.Social.publishActivity(activity);
  });
}
function showById(theId) {
  if ( document.getElementById(theId) != undefined ) {
    document.getElementById(theId).style.display = '';
  }
}
function hideById(theId) {
  if ( document.getElementById(theId) != undefined ) {
    document.getElementById(theId).style.display = 'none';
  }
}
function contentById(theId, theContent, append) {
  if (append === true) {
    document.getElementById(theId).innerHTML += theContent;
  } else {
    document.getElementById(theId).innerHTML = theContent;
  }
}
function clearForm(formName){
  document.getElementById(formName+'_clear_button').click();
}
function isValidString(theString) {
  if ( theString != undefined && theString != null && theString != '' && typeof theString == 'string' ) {
    return true;
  }
  return false;
}
