// bbCode control by
// subBlue design
// www.subBlue.com

// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);
var baseHeight;

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]','[/list]','[list=1]','[/list]','[*]','[img]','[/img]','[url]','[/url]','[file]','[/file]');
imageTag = false;

// Shows the help messages in the helpline window
function helpline(help) {
    document.forumpost.helpbox.value = eval(help + "_help");
}

// Replacement for arrayname.length property
function getarraysize(thearray) {
    for (i = 0; i < thearray.length; i++) {
        if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
            return i;
        }
    return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
    thearray[ getarraysize(thearray) ] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
    thearraysize = getarraysize(thearray);
    retval = thearray[thearraysize - 1];
    delete thearray[thearraysize - 1];
    return retval;
}


function checkForm() {

    formErrors = false;

    if (document.forumpost.comment.value.length < 2) {
        formErrors = "You must enter a message when posting.";
    }

    if (formErrors) {
        alert(formErrors);
        return false;
    } else {
        bbstyle(-1);
        //formObj.preview.disabled = true;
        //formObj.submit.disabled = true;
        return true;
    }
}

function emoticon(text) {

    bbfontstyle(text + " ",'');
}

/**
* bbstyle
*/
function bbstyle(bbnumber)
{
	if (bbnumber != -1)
	{
		bbfontstyle(bbtags[bbnumber], bbtags[bbnumber+1]);
	}
	else
	{
		insert_text('[*]');
		document.forms['forumpost'].elements['comment'].focus();
	}
}

/**
* Apply bbcodes
*/
function bbfontstyle(bbopen, bbclose)
{
	theSelection = false;

	var textarea = document.forms['forumpost'].elements['comment'];

	textarea.focus();

	if ((clientVer >= 4) && is_ie && is_win)
	{
		// Get text selection
		theSelection = document.selection.createRange().text;

		if (theSelection)
		{
			// Add tags around selection
			document.selection.createRange().text = bbopen + theSelection + bbclose;
			document.forms['forumpost'].elements['comment'].focus();
			theSelection = '';
			return;
		}
	}
	else if (document.forms['forumpost'].elements['comment'].selectionEnd && (document.forms['forumpost'].elements['comment'].selectionEnd - document.forms['forumpost'].elements['comment'].selectionStart > 0))
	{
		mozWrap(document.forms['forumpost'].elements['comment'], bbopen, bbclose);
		document.forms['forumpost'].elements['comment'].focus();
		theSelection = '';
		return;
	}

	//The new position for the cursor after adding the bbcode
	var caret_pos = getCaretPosition(textarea).start;
	var new_pos = caret_pos + bbopen.length;

	// Open tag
	insert_text(bbopen + bbclose);

	// Center the cursor when we don't have a selection
	// Gecko and proper browsers
	if (!isNaN(textarea.selectionStart))
	{
		textarea.selectionStart = new_pos;
		textarea.selectionEnd = new_pos;
	}
	// IE
	else if (document.selection)
	{
		var range = textarea.createTextRange();
		range.move("character", new_pos);
		range.select();
		storeCaret(textarea);
	}

	textarea.focus();
	return;
}

/**
* Insert text at position
*/
function insert_text(text, spaces, popup)
{
	var textarea;

	if (!popup)
	{
		textarea = document.forms['forumpost'].elements['comment'];
	}
	else
	{
		textarea = opener.document.forms['forumpost'].elements['comment'];
	}
	if (spaces)
	{
		text = ' ' + text + ' ';
	}

	if (!isNaN(textarea.selectionStart))
	{
		var sel_start = textarea.selectionStart;
		var sel_end = textarea.selectionEnd;

		mozWrap(textarea, text, '')
		textarea.selectionStart = sel_start + text.length;
		textarea.selectionEnd = sel_end + text.length;
	}
	else if (textarea.createTextRange && textarea.caretPos)
	{
		if (baseHeight != textarea.caretPos.boundingHeight)
		{
			textarea.focus();
			storeCaret(textarea);
		}

		var caret_pos = textarea.caretPos;
		caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == ' ' ? caret_pos.text + text + ' ' : caret_pos.text + text;
	}
	else
	{
		textarea.value = textarea.value + text;
	}
	if (!popup)
	{
		textarea.focus();
	}
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	var scrollTop = txtarea.scrollTop;

	if (selEnd == 1 || selEnd == 2)
	{
		selEnd = selLength;
	}

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);

	txtarea.value = s1 + open + s2 + close + s3;
	txtarea.selectionStart = selEnd + open.length + close.length;
	txtarea.selectionEnd = txtarea.selectionStart;
	txtarea.focus();
	txtarea.scrollTop = scrollTop;

	return;
}


// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
    if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

/**
* Caret Position object
*/
function caretPosition()
{
	var start = null;
	var end = null;
}


/**
* Get the caret position in an textarea
*/
function getCaretPosition(txtarea)
{
	var caretPos = new caretPosition();

	// simple Gecko/Opera way
	if(txtarea.selectionStart || txtarea.selectionStart == 0)
	{
		caretPos.start = txtarea.selectionStart;
		caretPos.end = txtarea.selectionEnd;
	}
	// dirty and slow IE way
	else if(document.selection)
	{

		// get current selection
		var range = document.selection.createRange();

		// a new selection of the whole textarea
		var range_all = document.body.createTextRange();
		range_all.moveToElementText(txtarea);

		// calculate selection start point by moving beginning of range_all to beginning of range
		var sel_start;
		for (sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++)
		{
			range_all.moveStart('character', 1);
		}

		txtarea.sel_start = sel_start;

		// we ignore the end value for IE, this is already dirty enough and we don't need it
		caretPos.start = txtarea.sel_start;
		caretPos.end = txtarea.sel_start;
	}

	return caretPos;
}

var newwindow;
function poptastic(url)
{
    newwindow=window.open(url,'name','height=500,width=700,resizable=yes,scrollbars=yes');
    if (window.focus) {newwindow.focus()}
}


function AddRowsToTable() {
     var tbl = document.getElementById('tblforumfile');
     var lastRow = tbl.rows.length;

     // if there is no header row in the table, then iteration = lastRow + 1

     var iteration = lastRow;
     var row = tbl.insertRow(lastRow);

     var cellRight = row.insertCell(0);
     var el = document.createElement('input');
     el.setAttribute('type', 'FILE');
     el.setAttribute('name', 'mfile_forum[]');
     el.setAttribute('size', '40');
     cellRight.appendChild(el);
}

function RemoveRowFromTable() {
     var tbl = document.getElementById('tblforumfile');
     var lastRow = tbl.rows.length;
     if (lastRow > 0) tbl.deleteRow(lastRow - 1);
}

