// Fields are shown or hidden based on user input of a yes or no radio button
function hideShow(screenname) {
    // hide these fields when yesNo field is displayed show them when yesNo is hidden
    var fields = [
        // list of fields to display or hide based on user input
    ];
    var targetField = 'yesNo';     // name of field in flow to toggle display
    var dummyFieldValue = '000000000';
    var no = document.getElementById('id for no value');
    var yes = document.getElementById('id for yes value');
    var radioButton = document.getElementById('capture_'+screenname+'_'+targetField);

    // hide fields group and radioButton field on registration form (radioButton is not set yet)
    if (radioButton && !radioButton.value) {
        for (var i=0; i<fields.length; i++) {
            setFieldDisplay(screenname, fields[i], false);
        }
        setFieldDisplay(screenname, targetField, false, dummyFieldValue);
    }

    // functions to show/hide fields
    var hideFieldsGroup = function() {
        // hide group
        for (var i=0; i<fields.length; i++) {
            setFieldDisplay(screenname, fields[i], false);
        }
        // show radioButton field
        setFieldDisplay(screenname, targetField, true, dummyFieldValue);
    };
    var showFieldsGroup = function() {
        // show group
        for (var i=0; i<fields.length; i++) {
            setFieldDisplay(screenname, fields[i], true);
        }
        // hide radioButton field
        setFieldDisplay(screenname, targetField, false, dummyFieldValue);
    };

    // toggle field displays via radio button
    if (radioButton && radioButton.value && radioButton.value != dummyFieldValue && yes) {
        yes.checked = true;
    }
    else if (no) {
        no.checked = true;
    }
    if (no) {
        if (no.checked) {
            showFieldsGroup();
        }
        no.onchange = showFieldsGroup;
    }
    if (yes) {
        if (yes.checked) {
            hideFieldsGroup();
        }
        yes.onchange = hideFieldsGroup;
    }
}

/*
Show or hide a capture field and optionally set a dummy value when hiding for client-side validation
*/
function setFieldDisplay(screenname, fieldname, display, dummy) {
    var divId = 'capture_'+screenname+'_form_item_'+fieldname;
    var div = document.getElementById(divId);
    if (div) {
        if (display) {
            div.style.display = 'block';
        }
        else {
            div.style.display = 'none';
        }
    }
    if (dummy) {
        var fieldId = 'capture_'+screenname+'_'+fieldname;
        var field = document.getElementById(fieldId);
        if (field) {
            // remove dummy value when shown
            if (display && field.value === dummy) {
                field.value = '';
            }
            // set dummy value if hidden
            if (!display) {
                field.value = dummy;
            }
        }
    }
}
