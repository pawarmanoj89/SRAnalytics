function showHint(str) {
	var string = str;
	if (string.length == 0) {
		document.getElementById("hintlabel").innerHTML = "";
		return;
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("hintlabel").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET", "autocomplete.php?q=" + string, true);
	xmlhttp.send();
}

function ckeckforuserdata(sruser){
	
	if (sruser.length == 0) {
		document.getElementById("userlabel").innerHTML = "";
		return;
	}
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			
			document.getElementById("userlabel").innerHTML = xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("GET", "getalluserid.php?q=" + sruser, true);
	xmlhttp.send();
	
}
function setAllUsersId(){
 alert('SetFunc');
 document.getElementById('fbtext').value='pawar';
}

var hide = true;

function hideTable(tableId) {
	var t = document.getElementById(tableId);
	t.style.display = "none";
}

function hideRows(tableId) {
	var t = document.getElementById(tableId);
	var len = t.rows.length;
	var rowStyle = (hide) ? "none" : "";
	for (i = 0; i < len; i++) {
		t.rows[i].style.display = rowStyle;
	}
}

function set(row) {
	//document.getElementById("username").value = document.getElementById(row).textContent;
	document.getElementById("username").value = document.getElementById(row).innerHTML;
	hideTable("suggestionTable");
}
function test(msg) {
	alert(msg);
}
function zeroLengthValidation(user) {
	var x = user;
	if (x == null || x == "") {
		document.getElementById('errname').innerHTML = "  Username must be filled out";
		//alert('Blank');
		return false;
	}
	return true;
}