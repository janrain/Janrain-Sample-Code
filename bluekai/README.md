## Add UUID to on-page Javascript

### BlueKai will generate some sample code similar to the following

```html
<!-- Begin BlueKai Tag -->
<iframe
    name="__bkframe"
    height="0" width="0" frameborder="0"
    style="display:none;position:absolute;clip:rect(0px 0px 0px 0px)"
    src="about:blank"></iframe>
<script type="text/javascript" src="http://tags.bkrtx.com/js/bk-coretag.js"></script>
<script type="text/javascript">
bk_doJSTag(32015, 1);           // Change 32015 to your container id.
</script>
<!-- End BlueKai Tag -->
```

### Your page needs to have the BlueKai iframe and bk-coretag.js script loaded without the script that calls bk_doJSTag.

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

### Add back the script that calls bk_doJSTag with the the following modifications to the janrainCaptureWidgetOnLoad function in your janrain-init.js file.

```javascript
janrain.events.onCaptureProfileCookieSet.addHandler(function(){
    var uuid = janrain.capture.ui.getProfileCookieData('uuid');
    bk_addPageCtx("id", uuid);      // NOTE: "id" may be different.
    // Add any other customizations
    bk_doJSTag(32015, 1);           // Change 32015 to your container id.
});
```
