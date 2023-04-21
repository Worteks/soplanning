
function toggle2(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
		setCookie(obj, 'none', 1000, '/');
	}
	else {
		el.style.display = '';
		setCookie(obj, '', 1000, '/');
	}
}

function getPosition(element, attribute) {
	// exemple : offsetLeft et offsetTop
	var p = 0;
	while (element) {
		p += element[attribute];
		element = element.offsetParent;
	}
	return p;
}

//au format dd/mm/yyyy
function getDate(strDate){
	var day = strDate.substring(0,2);
	var month = strDate.substring(3,5) - 1;
	var year = strDate.substring(6,10);
	var d = new Date(year, month, day);
	return d;
}
//   0 si date_1=date_2
//   1 si date_1>date_2
//  -1 si date_1<date_2
function dateCompare(date_1, date_2){
	var diff = date_1.getTime()-date_2.getTime();
	return (diff==0?diff:diff/Math.abs(diff));
}

function remplirDateFinPeriode() {
	if (document.getElementById('date_fin').value == '') {
		var dateDebut = document.getElementById('date_debut').value;
		if (dateDebut != '') {
			document.getElementById('date_fin').value = dateDebut;
		}
	}
}

function remplirDateRepetition(cible) {
	if (document.getElementById(cible).value == '') {
		var dateDebut = document.getElementById('date_debut').value;
		if (dateDebut != '') {
			document.getElementById(cible).value = dateDebut;
		}
	}
}

function controlDate(date) {
	if (date != '' && !date.match(/^\d\d\/\d\d\/\d\d\d\d$/)) {
		return false;
	}
	return true;
}

var timerMasquerSousMenu = null;
var SousMenuOpened = false;

function masquerSousMenu(obj) {
	// si on est sur un menu déroulant, ne pas perdre le focus
	if (document.activeElement.type == 'select-one') {
		return;
	}
	var o = document.getElementById(obj);
	o.style.display = 'none';
	SousMenuOpened = false;
	revertCellule();
}

function masquerSousMenuDelai(obj) {
	timerMasquerSousMenu = setTimeout("masquerSousMenu('" + obj + "')",500);
}

function AnnuleMasquerSousMenu(obj) {
	if (timerMasquerSousMenu){
		clearTimeout(timerMasquerSousMenu);
	}
}

function revertCellule()
{
	document.getElementById(dragElementParent).appendChild(document.getElementById(idDrag));
	document.getElementById(idDrag).style.border = oldDragBorder;
	$('#'+idDrag).tooltip('enable');
}

function windowErreurDeplacement() {
	jQuery("#alertModal .modal-body").html(js_deposerCaseSurDate);
	jQuery("#alertModal").addClass('alert alert-error');
	jQuery("#alertModal").modal();
	setTimeout("jQuery('#alertModal').modal('hide');", 1000);
}

function windowDeplacementOK() {
	jQuery("#alertModal .modal-body").html(js_deplacementOk);
	jQuery("#alertModal").modal();
	setTimeout("jQuery('#alertModal').modal('hide');", 1000);
}

function windowPatienter() {
	jQuery("#alertModal .modal-body").html(js_patienter);
	jQuery("#alertModal").modal();
}

function assombrirPage () {
	var page_screen;
	if ( ! document.getElementById ('page_screen')) {
		page_screen = document.createElement ('DIV');
		page_screen.id = "page_screen";
		var body = document.getElementsByTagName ('BODY')[0];
		body.insertBefore (page_screen, body.firstChild);
	} else {
		page_screen = document.getElementById ('page_screen');
	}
	page_screen.style.height = Math.max (document.body.scrollHeight, document.body.clientHeight) + 'px';
	page_screen.style.width = Math.max (document.body.scrollWidth, document.body.clientWidth) + 'px';
	page_screen.style.display = 'block';
}


function retablirPage () {
	var page_screen = document.getElementById ('page_screen');
	page_screen.style.display = 'none';
}

function addEvent( obj, type, fn ) {
	if ( obj.attachEvent ) {
		obj['e'+type+fn] = fn;
		obj[type+fn] = function(){obj['e'+type+fn]( window.event );}
		obj.attachEvent( 'on'+type, obj[type+fn] );
	} else
		obj.addEventListener( type, fn, false );
}


function addLoadEvent(func) {
   var oldonload = window.onload;
   if (typeof window.onload != "function") {
	  window.onload = func;
   } else {
	  window.onload = function() {
		 if (oldonload) {
			oldonload();
		 }
		 func();
	  };
   }
}


var Reloader = {
	CONFIG_REFRESH_TIMER : 0,
	REFRESH_BLOCKED : false,
	STOP_REFRESH : false,
	UPDATE_STATUS : true,

	init : function(CONFIG_REFRESH_TIMER)
	{
		Reloader.CONFIG_REFRESH_TIMER = CONFIG_REFRESH_TIMER;
		setInterval("Reloader.checkRefresh()", CONFIG_REFRESH_TIMER*1000);
	},

	checkRefresh : function()
	{
		Reloader.UPDATE_STATUS = true;
		if (Reloader.STOP_REFRESH) {
			return;
		}
		if (hostReachable()) {
			top.location.reload();
		}
	},

	stopRefresh : function()
	{
		Reloader.UPDATE_STATUS = false;
		Reloader.STOP_REFRESH = true;
	},

	closeWindow : function()
	{
		Reloader.STOP_REFRESH = false;
		if (Reloader.UPDATE_STATUS) {
			if (hostReachable()) {
				top.location.reload();
			}
		}
	}
};

// cr?ation, lecture et suppression de cookie
function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

// expires : ? indiquer en jours
function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name+"="+escape( value ) +
		( ( expires ) ? ";expires="+expires_date.toGMTString() : "" ) + //expires.toGMTString()
		( ( path ) ? ";path=" + path : "" ) +
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}

function deleteCookie( name, path, domain ) {
	if ( getCookie( name ) ) document.cookie = name + "=" +
			( ( path ) ? ";path=" + path : "") +
			( ( domain ) ? ";domain=" + domain : "" ) +
			";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}


// fonctions pour textarea auto-enlarge
function attachAutoResizeEvents(obj)
{
	var txtX=document.getElementById(obj)
	var minH=txtX.style.height.substr(0,txtX.style.height.indexOf('px'))
	txtX.onchange=new Function("resize(this,"+minH+")")
	txtX.onkeyup=new Function("resize(this,"+minH+")")
	txtX.onchange(txtX,minH)
}
function resize(txtX,minH)
{   txtX.style.height = 'auto' // required when delete, cut or paste is performed
    txtX.style.height = txtX.scrollHeight+'px'
    if(txtX.scrollHeight<=minH)
        txtX.style.height = minH+'px'
}


function getRadioValue(idOrName) {
	var value = null;
	var element = document.getElementById(idOrName);
	var radioGroupName = null;

	// if null, then the id must be the radio group name
	if (element == null) {
		radioGroupName = idOrName;
	} else {
		radioGroupName = element.name;
	}
	if (radioGroupName == null) {
		return null;
	}
	var radios = document.getElementsByTagName('input');
	for (var i=0; i<radios.length; i++) {
		var input = radios[ i ];
		if (input.type == 'radio' && input.name == radioGroupName && input.checked) {
			value = input.value;
			break;
		}
	}
	return value;
}

function fermerMessage() {
	document.getElementById('divMessage').style.display = "none";
}

function videChampsFinTache(actif) {
	var champs = new Array();
	champs['date_fin'] = 'text';
	/*champs['nb_jours'] = 'text';*/
	champs['duree'] = 'text';
	champs['heure_debut'] = 'text';
	champs['heure_fin'] = 'text';
	champs['matin'] = 'checkbox';
	champs['apresmidi'] = 'checkbox';
	for (var valeur in champs) {
		if (valeur == actif) {
			continue;
		}
		if (valeur == 'heure_fin' && actif == 'heure_debut') {
			continue;
		}
		if (valeur == 'heure_debut' && actif == 'heure_fin') {
			continue;
		}
		if (champs[valeur] == 'text') {
			document.getElementById(valeur).value = '';
		}
		if (champs[valeur] == 'checkbox') {
			document.getElementById(valeur).checked = false;
		}
	}
}

function hours_am_pm(ts) {
  var H = +ts.substr(0, 2);
  var h = (H % 12) || 12;
  h = (h < 10)?("0"+h):h;  // leading 0 at the left for 1 digit hours
  var ampm = H < 12 ? "AM" : "PM";
  ts = h + ':' + ts.substr(3, 2) + ampm;
  return ts;
    }

/* */
function heurefinSynchro(heure,step)
{
	heure2=hours_am_pm(heure);
	$('#heure_fin').timepicker('option',
		{
			'show2400': 'true',
			'timeFormat': 'H\\:i',
			'step':step,
			'scrollDefault': heure2,
			'minTime': heure2,
			'durationTime': heure2,
			'showDuration':true
		});
	
}
/* Function for init select2 dropdown */
function initselect2(lang,choix,parentModal) {
	// init select2 if element is optionnal
	jQuery(".select2").select2({
			allowClear: true, 
			language: lang, 
			tags: "true",
			theme: 'bootstrap',
			width: 'resolve',
			placeholder: choix
			});
	$('.select2-search__field').css('width', '105%');
	if ($.fn.modal){
		$.fn.modal.Constructor.prototype._enforceFocus = function() {};
	}
}


function chargerYScrollPos(){
	window.scrollTo(0,yscroll);
}

function hostReachable() {
	// Handle IE and more capable browsers
	var xhr = new ( window.ActiveXObject || XMLHttpRequest )( "Microsoft.XMLHTTP" );
	var status;

	// Open new request as a HEAD to the root hostname with a random param to bust the cache
	xhr.open( "HEAD", window.location.href + "/?rand=" + Math.floor((1 + Math.random()) * 0x10000), false );

	// Issue request and handle response
	try {
		xhr.send();
		if (xhr.status >= 200 && xhr.status < 304) {
			return true;
		} else {
			return false;
		}
	} catch (e) {
		return false;
	}
}

// get checkboxes value for the given prefix
function getCheckboxes(formName, inputPrefix) {
	var resultat = new Array();
	var inputList = document.forms[formName].elements;
	var compteur = 0;
	for(compteur;compteur!=inputList.length;compteur++){
		var str = inputList[compteur].id;
		var tab = str.split("_");
		if (tab[0] == inputPrefix && inputList[compteur].checked) {
			resultat.push(inputList[compteur].value);
		}
	}
	return resultat;
}
// get Select value, return select value or array of values if multiple select
function getSelectValue(selectId)
{
	var elmt = document.getElementById(selectId);
	if(elmt.multiple == false)
	{
		return elmt.options[elmt.selectedIndex].value;
	}
	var values = new Array();
	for(var i=0; i< elmt.options.length; i++)
	{
		if(elmt.options[i].selected == true)
		{
			values[values.length] = elmt.options[i].value;
		}
	}
	return values;
}

function loadScript(url, callback){

    var script = document.createElement("script")
    script.type = "text/javascript";
    if (script.readyState){  //IE
        script.onreadystatechange = function(){
            if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  //Others
        script.onload = function(){
            callback();
        };
    }
    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}

function loadjscssfile(filename, filetype){
    if (filetype=="js"){ //if filename is a external JavaScript file
        var fileref=document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", filename)
    }
    else if (filetype=="css"){ //if filename is an external CSS file
        var fileref=document.createElement("link")
        fileref.setAttribute("rel", "stylesheet")
        fileref.setAttribute("type", "text/css")
        fileref.setAttribute("href", filename)
    }
    if (typeof fileref!="undefined")
        document.getElementsByTagName("head")[0].appendChild(fileref)
}

function cellClic(id,type) {
	Reloader.stopRefresh();
	// Nouvelle tache
	if (type==1) {
		var idtab=id.split('_');
		var projet=idtab[1];
		var annee=idtab[2].substring(0, 4);
		var mois=idtab[2].substring(4, 6);
		var jour=idtab[2].substring(6, 8);
		var datedebut=annee+'-'+mois+'-'+jour;
		if (idtab[3] == null)
		{
			xajax_ajoutPeriode(datedebut, projet);
		} else {
			xajax_ajoutPeriode(datedebut, projet,'',idtab[3]);
		}
	}else {
		var idtab=id.split('_');
		xajax_modifPeriode(idtab[1]);
	}
	return false;
}

// Gestion du drag & drop
function allowDrop(ev) {
	ev.preventDefault();
    var el = ev.target;
    var parent = el.getAttribute("data-parent");
    if (parent != null)
    {
        var el = document.getElementById(parent);
        $('#'+parent).attr("drop-active", true);
    }else
    {
        $(ev.target).attr("drop-active", true);
    }
}

function leaveDropZone(ev) {
	ev.preventDefault();
    var el = ev.target;
    var parent = el.getAttribute("data-parent");
    if (parent != null)
    {
        var el = document.getElementById(parent);
        $('#'+parent).removeAttr("drop-active", true);
    }else
    {
       $(ev.target).removeAttr("drop-active", true);
    }
}

function drag(ev) {
	idDrag=ev.target.id;
	$('#'+idDrag).tooltip('hide');
	$('#'+idDrag).tooltip('disable');
	var el = ev.target;
	var parent = el.getAttribute("data-parent");
	if(!parent){
		el.setAttribute("data-parent", el.parentNode.id);
	}
	dragElementParent = el.parentNode.id;
	var oldDragBorder=ev.target.style.border;
	ev.target.style.border = "1px solid red";
	ev.dataTransfer.setData("Text", el.id);
}

function drop(ev) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("Text");
	var dragElement = ev;
    if (ev.target.id != data)
    {
	    ev.target.appendChild(document.getElementById(data));
        $(ev.target).removeAttr("drop-active");
	    idCaseEnCoursDeplacement = data;
	    //idCaseDestination = ev.target.id;
		var el = ev.target;
		var dataparent = el.getAttribute("data-parent");
		if (dataparent != null)
		{
			idCaseDestination = dataparent;
		}else idCaseDestination = ev.target.id;
	    var el=document.getElementById('divChoixDragNDrop');
	    var offset=$('#'+idCaseDestination).offset();
	    el.style.position = 'absolute';
	    el.style.top = offset.top + 20 + "px";
	    el.style.left = offset.left + 20 + "px";
	    el.style.display = 'block';
    }
}

function multiselecthide() {
   // hide other menus before showing this one
   $('.ms-options-wrap > .ms-options.ms-active').each(function(){
      $(this).removeClass('ms-active');
    });
}

function desactiverRappelVersion () {
	setCookie('infosVersionInactif', '1', 30, '/');
}

function convertToAscii(string) {
    const unicodeToAsciiMap = {'Ⱥ':'A','Æ':'AE','Ꜻ':'AV','Ɓ':'B','Ƀ':'B','Ƃ':'B','Ƈ':'C','Ȼ':'C','Ɗ':'D','ǲ':'D','ǅ':'D','Đ':'D','Ƌ':'D','Ǆ':'DZ','Ɇ':'E','Ꝫ':'ET','Ƒ':'F','Ɠ':'G','Ǥ':'G','Ⱨ':'H','Ħ':'H','Ɨ':'I','Ꝺ':'D','Ꝼ':'F','Ᵹ':'G','Ꞃ':'R','Ꞅ':'S','Ꞇ':'T','Ꝭ':'IS','Ɉ':'J','Ⱪ':'K','Ꝃ':'K','Ƙ':'K','Ꝁ':'K','Ꝅ':'K','Ƚ':'L','Ⱡ':'L','Ꝉ':'L','Ŀ':'L','Ɫ':'L','ǈ':'L','Ł':'L','Ɱ':'M','Ɲ':'N','Ƞ':'N','ǋ':'N','Ꝋ':'O','Ꝍ':'O','Ɵ':'O','Ø':'O','Ƣ':'OI','Ɛ':'E','Ɔ':'O','Ȣ':'OU','Ꝓ':'P','Ƥ':'P','Ꝕ':'P','Ᵽ':'P','Ꝑ':'P','Ꝙ':'Q','Ꝗ':'Q','Ɍ':'R','Ɽ':'R','Ꜿ':'C','Ǝ':'E','Ⱦ':'T','Ƭ':'T','Ʈ':'T','Ŧ':'T','Ɐ':'A','Ꞁ':'L','Ɯ':'M','Ʌ':'V','Ꝟ':'V','Ʋ':'V','Ⱳ':'W','Ƴ':'Y','Ỿ':'Y','Ɏ':'Y','Ⱬ':'Z','Ȥ':'Z','Ƶ':'Z','Œ':'OE','ᴀ':'A','ᴁ':'AE','ʙ':'B','ᴃ':'B','ᴄ':'C','ᴅ':'D','ᴇ':'E','ꜰ':'F','ɢ':'G','ʛ':'G','ʜ':'H','ɪ':'I','ʁ':'R','ᴊ':'J','ᴋ':'K','ʟ':'L','ᴌ':'L','ᴍ':'M','ɴ':'N','ᴏ':'O','ɶ':'OE','ᴐ':'O','ᴕ':'OU','ᴘ':'P','ʀ':'R','ᴎ':'N','ᴙ':'R','ꜱ':'S','ᴛ':'T','ⱻ':'E','ᴚ':'R','ᴜ':'U','ᴠ':'V','ᴡ':'W','ʏ':'Y','ᴢ':'Z','ᶏ':'a','ẚ':'a','ⱥ':'a','æ':'ae','ꜻ':'av','ɓ':'b','ᵬ':'b','ᶀ':'b','ƀ':'b','ƃ':'b','ɵ':'o','ɕ':'c','ƈ':'c','ȼ':'c','ȡ':'d','ɗ':'d','ᶑ':'d','ᵭ':'d','ᶁ':'d','đ':'d','ɖ':'d','ƌ':'d','ı':'i','ȷ':'j','ɟ':'j','ʄ':'j','ǆ':'dz','ⱸ':'e','ᶒ':'e','ɇ':'e','ꝫ':'et','ƒ':'f','ᵮ':'f','ᶂ':'f','ɠ':'g','ᶃ':'g','ǥ':'g','ⱨ':'h','ɦ':'h','ħ':'h','ƕ':'hv','ᶖ':'i','ɨ':'i','ꝺ':'d','ꝼ':'f','ᵹ':'g','ꞃ':'r','ꞅ':'s','ꞇ':'t','ꝭ':'is','ʝ':'j','ɉ':'j','ⱪ':'k','ꝃ':'k','ƙ':'k','ᶄ':'k','ꝁ':'k','ꝅ':'k','ƚ':'l','ɬ':'l','ȴ':'l','ⱡ':'l','ꝉ':'l','ŀ':'l','ɫ':'l','ᶅ':'l','ɭ':'l','ł':'l','ſ':'s','ẜ':'s','ẝ':'s','ɱ':'m','ᵯ':'m','ᶆ':'m','ȵ':'n','ɲ':'n','ƞ':'n','ᵰ':'n','ᶇ':'n','ɳ':'n','ꝋ':'o','ꝍ':'o','ⱺ':'o','ø':'o','ƣ':'oi','ɛ':'e','ᶓ':'e','ɔ':'o','ᶗ':'o','ȣ':'ou','ꝓ':'p','ƥ':'p','ᵱ':'p','ᶈ':'p','ꝕ':'p','ᵽ':'p','ꝑ':'p','ꝙ':'q','ʠ':'q','ɋ':'q','ꝗ':'q','ɾ':'r','ᵳ':'r','ɼ':'r','ᵲ':'r','ᶉ':'r','ɍ':'r','ɽ':'r','ↄ':'c','ꜿ':'c','ɘ':'e','ɿ':'r','ʂ':'s','ᵴ':'s','ᶊ':'s','ȿ':'s','ɡ':'g','ᴑ':'o','ᴓ':'o','ᴝ':'u','ȶ':'t','ⱦ':'t','ƭ':'t','ᵵ':'t','ƫ':'t','ʈ':'t','ŧ':'t','ᵺ':'th','ɐ':'a','ᴂ':'ae','ǝ':'e','ᵷ':'g','ɥ':'h','ʮ':'h','ʯ':'h','ᴉ':'i','ʞ':'k','ꞁ':'l','ɯ':'m','ɰ':'m','ᴔ':'oe','ɹ':'r','ɻ':'r','ɺ':'r','ⱹ':'r','ʇ':'t','ʌ':'v','ʍ':'w','ʎ':'y','ᶙ':'u','ᵫ':'ue','ꝸ':'um','ⱴ':'v','ꝟ':'v','ʋ':'v','ᶌ':'v','ⱱ':'v','ⱳ':'w','ᶍ':'x','ƴ':'y','ỿ':'y','ɏ':'y','ʑ':'z','ⱬ':'z','ȥ':'z','ᵶ':'z','ᶎ':'z','ʐ':'z','ƶ':'z','ɀ':'z','œ':'oe','ₓ':'x'};
    const stringWithoutAccents = string.normalize("NFD").replace(/[\u0300-\u036f]/g, '');
    return stringWithoutAccents.replace(/[^\u0000-\u007E]/g, character => unicodeToAsciiMap[character] || '');
}

function fileUpload() {
	$('#divPatienter2').removeClass('d-none');
	$('#file-select-button').off('click');
	$('#butSubmitPeriode').prop('disabled', true);
	var formData = new FormData();
	var fileData = $('#fichier').prop('files');
	var linkid = $('#link_id').val();
	var periodeid = $('#periode_id').val();
    var filename=$('#fichier')[0].files[0]['name'];
	filename = convertToAscii(filename);
	var filenamesize=$('#fichier')[0].files[0]['size'];
	var max_size_upload=$('#max_size_upload').val();
	if(filename && filenamesize < max_size_upload)
	{
		$('<div><a href="upload/files/'+linkid+'/'+filename+'" class="ellipsis fileupload" target="_blank" id="fichier_periode" style="float:left;">'+filename+'</a>&nbsp;<i class="fa fa-trash fa-fw" aria-hidden="true" onclick="fileRemove(\''+filename+'\',this.closest(\'div\'));" id="fileremovebutton" style="margin-top:4px;margin-left:4px;float:left;cursor:pointer;"></i></div>').insertBefore( $('#lastfile') );
		var json_files="";
		jQuery.each(jQuery('.fileupload'), function(i, file) {
			if (json_files != '') { json_files = json_files + ';'; }
			json_files = json_files + file.innerHTML;
		});
		$("#liste_fichiers").val(json_files);

		if (fileData)
		{	
			jQuery.each(jQuery('#fichier').prop('files'), function(i, file) {
				formData.append('fichier-'+i, file);
			});
			formData.append('linkid', linkid);
			formData.append('periodeid', periodeid);
			formData.append('fichiers', json_files);
			formData.append('type', 'upload');
			$.ajax({
				url			: 'process/upload.php',
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post',     
				success		: function(message){
								if (message)
								{
									alert(message);
									return false;
								}
					}
			});
		}
		$('#fichier').val('');
	}else
	{
		alert($('#max_size_upload_error').val());
	}
	$('#file-select-button').on('click',file_upload_click);
	$('#divPatienter2').addClass('d-none');
	$('#butSubmitPeriode').prop('disabled', false);
	return true;
};
	
function fileRemove(fichier,div) {
	var suppression_upload=$("#suppression_upload").val();
	var json_files="";
	var formData = new FormData();
	var linkid = $('#link_id').val();
	var periodeid = $('#periode_id').val();
	
	if(confirm(suppression_upload))
	{
		div.remove();
		liste_fichiers=$("#liste_fichiers").val();
		ret = liste_fichiers.replace('{"filename":"'+fichier+'"}','');
		jQuery.each(jQuery('.fileupload'), function(i, file) {
			if (json_files != '') { json_files = json_files + ';'; }
			json_files = json_files + file.innerHTML;
		});
		$("#liste_fichiers").val(json_files);

		formData.append('linkid', linkid);
		formData.append('periodeid', periodeid);
		formData.append('type', 'delete');
		formData.append('fichier_to_delete',fichier);
		formData.append('fichiers', json_files);
		$.ajax({
			url			: 'process/upload.php',
			cache       : false,
			contentType : false,
			processData : false,
			data        : formData,                         
			type        : 'post',     
			success		: function(message){
				if (message)
					{
						alert(message);
						return false;
					}
			}
		});
	}
}

function fileUploadBackup() {
	var formData = new FormData();
	var fileData = $('#fichier').prop('files');
	var filename=$('#fichier')[0].files[0]['name'];
	filename = convertToAscii(filename);
	var filenamesize=$('#fichier')[0].files[0]['size'];
	var max_size_upload=$('#max_size_upload').val();
	if(filename && filenamesize < max_size_upload)
	{
		if (fileData)
		{	
			jQuery.each(jQuery('#fichier').prop('files'), function(i, file) {
				formData.append('fichier-'+i, file);
			});
			formData.append('type', 'upload');
			formData.append('type_restauration', $("input[name='type_restauration']:checked").val());
			formData.append('type_fichier_import', $("#type_fichier_import").val());
			$.ajax({
				url			: 'process/upload_backup.php',
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post',     
				success		: function(message){
								$("#uploaddiv").hide();
								$("#ecrasementdiv").hide();
								$("#optionsdiv").hide();
								$("#configoptions-div").hide();
								$("#config-div").hide();
								$("#projets-div").hide();
								$("#user-div").hide();
								$("#lieux-div").hide();
								$("#ressources-div").hide();
								$("#taches-div").hide();
								if (message)
								{
									if (isJson(message))
									{
										var obj  = jQuery.parseJSON( message );
										$.each(obj, function(key, value) {
											if(value.file=='config.csv')
											{
												$("#config-div").show();
												$("#configoptions-div").show();
												$("#config-nb").text(value.nb);
											}
											if(value.file=='projet.csv' || value.file=='groupe.csv')
											{
												$("#projets-div").show();
												$("#projets-nb").text(value.nb);
											}
											if(value.file=='user.csv' || value.file=='user_groupe.csv')
											{
												$("#user-div").show();
												$("#user-nb").text(value.nb);
											}
											if(value.file=='lieu.csv')
											{
												$("#lieux-div").show();
												$("#lieux-nb").text(value.nb);
											}
											if(value.file=='ressource.csv')
											{
												$("#ressources-div").show();
												$("#ressources-nb").text(value.nb);
											}
											if(value.file=='periode.csv')
											{
												$("#taches-div").show();
												$("#taches-nb").text(value.nb);
											}
										});
										$("#uploaddiv").show();
										$("#ecrasementdiv").show();
										$("#optionsdiv").show();
										$("#bouton-restore").prop('disabled', false);
									}else
									{
										alert(message);
										$("#uploaddiv").hide();
										$("#ecrasementdiv").hide();
										$("#optionsdiv").hide();
										$("#configoptions-div").hide();
										$("#bouton-restore").prop('disabled', true);
										$('#fichier').val('');
									}
									return false;
								}
					}
			});
		}
	}
};

function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

function annulerSelectionTaches() {
	tabCellsSelected.forEach(function(item){
		$('#' + item).removeClass("bordureSelectionne");
	});
	tabCellsSelected = new Array();
}