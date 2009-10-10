function clearVal(obj,title){
	if(obj.value==title){
		obj.value='';
	}
}

function writeVal(obj,title){
	if(obj.value==''){
		obj.value=title;
	}
}

function showCatForCar(carID)
{
	var c = document.getElementById(carID);
	if (c)
	{
		var d = document.getElementById(tempCarID);
		if (d) {
			d.style.display = "none";
		}
		c.style.display = "block";
	}
	tempCarID = carID;
	return true;
}

function setNullFilter()
{
	var filterForm = document.getElementById("filterForm");
	filterForm.from.value='';
	filterForm.to.value='';
	if(filterForm.propID != null) {
		filterForm.propID.selectedIndex = 0;
	}
	filterForm.submit();
}

function openPopupWindow (formURL, width, height)
{
  var Left = (screen.width - width) / 2;
  var Top = (screen.height - height) / 2;
  window.open(formURL,"","toolbar=0, scrollbars=no,location=0,status=0,menubar=0,width="+width+",height="+height+",border=1,resizable=yes,left="+Left+",top="+Top);  
}
