function signInOut() {
  if (userSignedIn === true) {
    signOut();
  } else {
    hideById('sign_in_feedback');
    if (window.the_widget_url != undefined) {
      document.getElementById('the_widget').src = window.the_widget_url;
    }
    showById('register');
    showById('sign_in');
    showById('the_widget');
    contentById('debug', '', false);
    contentById('debug', 'Here we will use Engage to collect data to fill out the form on the right.<br>', true);
    contentById('debug', 'Sign in with an Engage provider on the left.<br>', true);    
  }
}
function authDone(doneFrame){
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
        contentById('debug', '', false);        
        contentById('debug', 'We recived a JSON string with the Engage data:<br><pre>'+engageSession.responseText+'</pre>', true);
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
    form_help['identifier'] = '';
    contentById('debug', 'Getting this data marks the end of our Engage authentication. What your site <br>', true);    
    contentById('debug', 'does with the data is up to you.<br><br>', true);    
    contentById('debug', 'For this demo we converted it into an object and matched it to some of the fields on the form.<br>', true);
    contentById('debug', 'This data is then filled into the form to ease registration. Notice that we <br>', true);    
    contentById('debug', 'provided clear feedback to the user telling them that the connection was made.<br><br>', true);    
    contentById('debug', 'Go ahead and finish the form and click register.<br>', true);    
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
      if (isValidString(sessionData.profile.identifier)) {
        form_help['identifier'] = sessionData.profile.identifier;
      }
    }
    for(var i=0;i<document.forms['reg_form'].elements.length;i++){
      if (form_help[document.forms['reg_form'].elements[i].id] != undefined && document.forms['reg_form'].elements[i].value == ''){
        document.forms['reg_form'].elements[i].value = form_help[document.forms['reg_form'].elements[i].id];
      }
    }
  }
}
function validateForm() {
  var doPost = true;
  for(var i=0;i<document.forms['reg_form'].elements.length;i++){
    if (document.forms['reg_form'].elements[i].value == '' && document.forms['reg_form'].elements[i].className.search('required') >= 0){
      contentById('debug', document.forms['reg_form'].elements[i].name + '<br>', true);
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
            contentById('debug', '');
            contentById('the_content', '<div id="the_profile"></div>', false);
            contentById('the_content', 'Your user has been created and the site is now ready to offer the authenticated features and content.<br>', true);
            clearForm();
            hideById('register');                
            userProfile();
            signIn();
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
function userProfile() {
  var siteProfile = new XMLHttpRequest();
  siteProfile.onreadystatechange=function() {
    if (siteProfile.readyState==4 && siteProfile.status==200) {
      var siteProfileData = JSON.parse(siteProfile.responseText);
      if (siteProfileData.user_data.user_name == null || siteProfileData.user_data.user_name == '') {
        contentById('sign_in_label', 'Please retry, starting over.');
        return false;
      }
      hideById('debug');
      contentById('the_profile', '', false);
      contentById('the_profile', siteProfileData.user_data.user_name+', you are connected to '+siteProfileData.authinfo.profile.providerName+'.</p>', true);            
      if (siteProfileData.authinfo.profile.photo != null && siteProfileData.authinfo.profile.photo != '') {
        contentById('the_profile', '<img class="avatar" src="'+siteProfileData.authinfo.profile.photo+'" />', true);
      }
      var userDataTable = CreateDetailView(siteProfileData.user_data,"lightPro",true);
      userDataTable += CreateDetailView(siteProfileData.authinfo.profile,"lightPro",true);      
      contentById('the_profile', userDataTable, true);
      showById('the_content');        
      showById('the_profile');
    }
  }
  siteProfile.open('GET','get-profile.php',true);
  siteProfile.send();
}
function signIn() {
  userSignedIn = false;
  document.getElementById("sign_in_out").innerHTML = 'Register';
  var checkSignin = new XMLHttpRequest();
  checkSignin.onreadystatechange=function() {
    if (checkSignin.readyState==4 && checkSignin.status==200) {
      var signInData = JSON.parse(checkSignin.responseText);
      if (signInData.stat == 'ok') {
        contentById('user_profile', signInData.user_data.user_name);
        hideById('debug');
        hideById('register');
        hideById('sign_in');
        contentById('sign_in_out', 'Sign Out');
        contentById('the_content', 'Welcome! ', true);
        showById('the_content');
        userSignedIn = true;
      } else {
        contentById('debug', 'Click Register to start.<br>', true);
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
        contentById('debug', 'Signed Out<br>');
        contentById('sign_in_out', 'Sign In or Register');
        hideById('the_content');
        userSignedIn = false;
        window.location = window.location;
      } 
    }
  }
  signOutRequest.open('GET', 'sign-out.php', true);
  signOutRequest.send();
}
function showById(theId) {
  document.getElementById(theId).style.display = 'block';
}
function hideById(theId) {
  document.getElementById(theId).style.display = 'none';
}
function contentById(theId, theContent, append) {
  if (append === true) {
    document.getElementById(theId).innerHTML += theContent;
  } else {
    document.getElementById(theId).innerHTML = theContent;
  }
}
function clearForm(){
  document.getElementById('clear_button').click();
}

function isValidString(theString) {
  if ( theString != undefined && theString != null && theString != '' && typeof theString == 'string' ) {
    return true;
  }
  return false;
}

function CreateDetailView(objArray, theme, enableHeader) {
    // set optional theme parameter
    if (theme === undefined) {
        theme = 'mediumTable';  //default theme
    }

    if (enableHeader === undefined) {
        enableHeader = true; //default enable headers
    }

    objArray = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;

    var str = '<table class="' + theme + '">';
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


