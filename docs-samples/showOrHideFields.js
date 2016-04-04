// JavaScript
function showOrHideFields() {
    if (document.getElementById("someID").value === "some value") {
        document.getElementById("anotherID").style.display = "block";
    } else {
        document.getElementById("anotherID").style.display = "none";
    }
}

// jQuery
function showOrHideFields() {
    if ($("#someID").val() === "some value") {
        $("#anotherID").show();
    } else {
        $("#anotherID").hide();
    }
}
