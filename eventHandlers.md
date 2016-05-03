### How do I get the access token generated for a user after registration or login?

```javascript
janrain.events.onCaptureLoginSuccess.addHandler(function(response) {
  var accessToken = response.accessToken;
})
```

### How do I get a user's display name from localStorage after the user has logged in?

```javascript
janrain.events.onCaptureLoginSuccess.addHandler(function(response) {
  var janrainCaptureProfileData = localStorage.janrainCaptureProfileData;
  var janrainCaptureProfileDataParsed = JSON.parse(janrainCaptureProfileData);
  var displayName = janrainCaptureProfileDataParsed.displayName;
})
```

### I want to show a specific message when the user's password is updated.

```javascript
janrain.events.onCaptureSaveSuccess.addHandler(function(response) {
  var screen = response.screen;
  var statusMessage = response.statusMessage;
  if (screen === "changePassword" && statusMessage === "profileSaved") {
    $('#editProfile .capture_form_item').lastChild.textContents = "your custom message";
  }
})
```

### When an existing user logs in with Twitter for the first time, the user gets an error message on the Almost Done screen when the user tries to fill in the email address that the user previously registered with. How do I display a different error message to these users when the `unique` validation error message is displayed suggesting that the user go back and log in with the user's other credentials?

```javascript
janrain.events.onCaptureRenderComplete.addHandler(function(response) {
  var screen = response.screen;
  if (screen === "socialRegistration") {
    var element = $('#capture_socialRegistration_form_item_emailAddress .capture_tip_error')
    if (element) {
      element.innerHTML = "Your custom message";
    }
  }
});
```
