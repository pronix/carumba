window.onload = function()
{
    //alert('Loaded');
}
/*
function edit(id)
{
    var content = document.getElementById('content'+id);
    var form = document.getElementById('input');
    var form_id = document.getElementById('id');
    form_id.value = id;
    form.value = content.innerHTML
}
*/

function saveReply()
{
    var edit_form = document.getElementById('edit_form');
    edit_form.action.value = 'save_reply';
    alert(edit_form.action.value);
    edit_form.submit();
}

function edit(id) {
    //var sId = document.getElementById("content"+id).value;
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("get", "/task/?XMLHttpEdit=1&id=" + id, true);
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4) {
            if (oXmlHttp.status == 200) {
                displayCustomerInfo(oXmlHttp.responseText);
                var form_id = document.getElementById('id');
                var action = document.getElementById('action');
                form_id.value = id;
                action.value = 'save_reply';
            } else {
                displayCustomerInfo("An error occurred: " + oXmlHttp.statusText); //statusText is not always accurate
            }
        }
    };
    oXmlHttp.send(null);
}

function displayCustomerInfo(sText) {
    var divCustomerInfo = document.getElementById("input");
    divCustomerInfo.value = sText;
}

function resetForm() {
    var form_id = document.getElementById('id');
    var action = document.getElementById('action');
    var input = document.getElementById('input');
    form_id.value = '';
    action.value = 'addreply';
    input.value = '';
}
