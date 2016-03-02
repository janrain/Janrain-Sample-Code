## Add UUID to on-page Javascript

### Page needs to have BlueKai iframe and bk-coretag.js script loaded without the call to bk_doJSTag.

```html
<!-- Begin BlueKai Tag -->
<iframe
    name="__bkframe"
    height="0" width="0" frameborder="0"
    style="display:none;position:absolute;clip:rect(0px 0px 0px 0px)"
    src="about:blank">
</iframe>
<script type="text/javascript" src="http://tags.bkrtx.com/js/bk-coretag.js"></script>
<!-- End BlueKai Tag -->
```

### Create a function to call bk_addPageCtx and bk_doJSTag.

```javascript
function bk_init(uuid, siteId, pixelLimit) {
    bk_addPageCtx("id", uuid);      // NOTE: "id" may be different.
    bk_doJSTag(siteId, pixelLimit);
}
```

### Create a function to inject our initilization script into the page.

```javascript
function bk_inject_script() {
    var script   = document.createElement("script");
    script.type  = "text/javascript";
    // You may want to use script.src to load the script from a file rather then an inline script.
    script.text  = "var uuid = janrain.capture.ui.getProfileCookieData('uuid')); bk_init(uuid, siteId, pixelLimit);"

    document.body.appendChild(script);
}
```

### Register a funtion which will inject our initilization script when the uuid is available.

```javascript
janrain.events.onCaptureProfileCookieSet.addHandler(function(){
  bk_inject_script();
});
```
