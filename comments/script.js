    function edit(id) 
    {
        var height = 300;
        var width = 550;
        var top = Math.round( height / 2 );
        var left = Math.round( screen.width / 2 ) - 275;
        window.open('/comments/form.php?id='+id, 'Комментарии', 'resizable=yes, height='+height+', width='+width+', top='+top+', left='+left);
        //return false;
    }
    
    function send(a) 
    {
        //alert(a);
        document.getElementById('action').value = a;
        document.forms[1].submit();
    }