window.onload = function()
{
    document.getElementById("clear").onclick = function()
    {
        clear();
    }
}

function edit(id)
{
    document.getElementById("url").value = document.getElementById("url"+id).href;
    document.getElementById("referer").value = document.getElementById("referer"+id).href;
    document.getElementById("title").value = document.getElementById("title"+id).innerHTML;
    document.getElementById("text").value = document.getElementById("text"+id).innerHTML;
    document.getElementById("email").value = document.getElementById("email"+id).innerHTML;
    document.getElementById("text").value = document.getElementById("text"+id).innerHTML;

    document.getElementById("actionForm").value = id;
}

function clear()
{
    document.getElementById("url").value = '';
    document.getElementById("referer").value = '';
    document.getElementById("title").value = '';
    document.getElementById("text").value = '';
    document.getElementById("email").value = '';
    document.getElementById("text").value = '';

    document.getElementById("actionForm").value = '';
}

function del()
{
    if (confirm("Все выбранные ссылки будут удалены.\nПродолжить операцию?")) {
        document.getElementById("action").value = "d";
        document.forms[0].submit();
    }
}

function hide()
{
    document.getElementById("action").value = "h";
    document.forms[0].submit();
}

function pub()
{
    document.getElementById("action").value = "p";
    document.forms[0].submit();
}
function move()
{
    document.getElementById("action").value = "m";
    document.forms[0].submit();
}