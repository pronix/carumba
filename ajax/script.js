var oXmlHttp;

window.onload = function()
{
    oXmlHttp = zXmlHttp.createRequest();
    /*
    document.getElementById("comments").onclick = function()
    {
        var sID = document.getElementById("ratingsID");
        var height = Math.round( screen.height / 2 );
        var width = 500;
        var top = Math.round( height / 2 );
        var left = Math.round( screen.width / 2 ) - 250;
        window.open('/ajax/comments.php?sID='+sID.value, 'Комментарии', 'resizable=yes, scrollbars=yes, height='+height+', width='+width+', top='+top+', left='+left);
        return false;
    }
    */
    var detalsNone = document.getElementById("detalsNone");
    if (detalsNone.value!=1) {
        document.getElementById("comment").onclick = function()
        {
            clickComment();
        }
        document.getElementById("detals").onclick = function()
        {
            clickDetals();
        }
    }
    
    if (detalsNone.value!=0) {
        clickComment();
    }
    
    document.getElementById("ratingBtn").onclick = function()
    {
        var sID = document.getElementById("ratingsID");
        var height = 130;
        var width = 250;
        var top = Math.round( screen.height / 2 ) - 65;
        var left = Math.round( screen.width / 2 ) - 125;
        window.open('/comments/rating_form.php?sID='+sID.value, 'Рейтинг', 'height='+height+', width='+width+', top='+top+', left='+left);
    }
}

function clickComment()
{
    var detals = document.getElementById("detals");
    var comment = document.getElementById("comment");
    var detalsCont = document.getElementById("detalsCont");
    var commentCont = document.getElementById("commentCont");
    var detalsOut = document.getElementById("detalsOut");
    var commentOut = document.getElementById("commentOut");
    var sID = document.getElementById("sID");
    var detalsNone = document.getElementById("detalsNone");

    comment.style.textDecoration = 'none';
    comment.style.cursor = 'default';
    comment.style.color = '#000';
    comment.style.fontWeight = 'bold';
    commentOut.style.borderBottomStyle = 'dashed';
    commentCont.style.backgroundColor = '#FFFFFF';
    document.getElementById("imgCommentLeft").src = "/images/detals_active_left.gif";
    document.getElementById("imgCommentRight").src = "/images/detals_active_right.gif";

    if (detalsNone.value!=1) {
        detals.style.textDecoration = 'underline';
        detals.style.cursor = 'pointer';
        detals.style.color = '#307DDC';
    } else {
        detals.style.textDecoration = 'none';
        detals.style.cursor = 'default';
        detals.style.color = '#000';
    }
    document.getElementById("imgDetalsLeft").src = "/images/detals_left.gif";
    document.getElementById("imgDetalsRight").src = "/images/detals_right.gif";
    detals.style.fontWeight = 'normal';
    detalsOut.style.borderBottomStyle = 'solid';
    detalsCont.style.backgroundColor = '#F0F0F1';

        
    document.getElementById("infoDetals").style.display = 'none';
    document.getElementById("infoComments").style.display = 'block';
}

function clickDetals()
{
    var detals = document.getElementById("detals");
    var comment = document.getElementById("comment");
    var detalsCont = document.getElementById("detalsCont");
    var commentCont = document.getElementById("commentCont");
    var detalsOut = document.getElementById("detalsOut");
    var commentOut = document.getElementById("commentOut");
    if (detals) {
        detals.style.textDecoration = 'none';
        detals.style.cursor = 'default';
        detals.style.color = '#000';
        detals.style.fontWeight = 'bold';
        detalsOut.style.borderBottomStyle = 'dashed';
        detalsCont.style.backgroundColor = '#FFF';
    
        comment.style.textDecoration = 'underline';
        comment.style.cursor = 'pointer';
        comment.style.color = '#307DDC';
        comment.style.fontWeight = 'normal';
        commentOut.style.borderBottomStyle = 'solid';
        commentCont.style.backgroundColor = '#F0F0F1';
    
        document.getElementById("imgCommentLeft").src = "/images/detals_left.gif";
        document.getElementById("imgCommentRight").src = "/images/detals_right.gif";
        document.getElementById("imgDetalsLeft").src = "/images/detals_active_left.gif";
        document.getElementById("imgDetalsRight").src = "/images/detals_active_right.gif";

        document.getElementById("infoComments").style.display = 'none';
        document.getElementById("infoDetals").style.display = 'block';
    }
}

function insertComments( sContent ) {
    var infoComments = document.getElementById("infoComments");
    infoComments.innerHTML = sContent;
}

/*
    document.getElementById("ratingSend").onclick = function()
    {
        oXmlHttp.onreadystatechange = function() {
            if (oXmlHttp.readyState == 4) {
                if (oXmlHttp.status == 200) {
                    saveRating(oXmlHttp.responseText);
                } else {
                    saveRating("-1||Обнаружена ошибка");
                }
            }
        }

        var ratingValue = document.getElementById("ratingValue");
        var ratingStatus = document.getElementById("ratingStatus");
        var ratingsID = document.getElementById("ratingsID");
        if (ratingValue.value > 0) {
            var sRequest = "/ajax/rating.php?value=" + ratingValue.value + "&sID=" + ratingsID.value;
            oXmlHttp.open("get", sRequest, true);
            oXmlHttp.send(null);     
            //alert(sRequest);
        } else {
            saveRating('-1||Укажите значение');
        }
    }
    
}


function saveRating( sMessage ) 
{
    var aData = sMessage.split("||");
    var ratingStatus = document.getElementById("ratingStatus");
    ratingStatus.innerHTML = "<br />" + aData[1];
    ratingStatus.style.display = 'block';
    if (aData[0] != -1) {
        var ratingStat = document.getElementById("ratingStat");
        ratingStat.innerHTML = aData[0];
    }
}
*/
