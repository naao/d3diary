Array.prototype.contains = function (ele) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == ele) {
            return true;
        }
    }
    return false;
};

Array.prototype.remove = function (ele) {
    var arr = new Array();
    var count = 0;
    for (var i = 0; i < this.length; i++) {
        if (this[i] != ele) {
            arr[count] = this[i];
            count++;
        }
    }
    return arr;
};

function addonload(addition) {
    window.onload = function () {
        addition();
    };
}

addonload(
    function () {
        var taglist = document.getElementById('tags');
        if ( taglist !== null) {
       		var tags = taglist.value.match(/\[.+?\]/g);
               	var populartags = document.getElementById('popularTags').getElementsByTagName('span');
        
        	for (var i = 0; i < populartags.length; i++) {
        		popTag = getUnescapedTag(populartags[i].innerHTML);
            	    if (tags !== null && tags.contains('[' + popTag + ']')) {
                	populartags[i].className = 'sTag';
            	    } else {
                	populartags[i].className = 'uTag';
            	    }
        
        	}
        }
    }
);

function getUnescapedTag(oldTag) {
	var tag = oldTag;
	tag = tag.replace(/&#39;/g, "'");
	tag = tag.replace(/&amp;/g, "&");
	tag = tag.replace(/&lt;/g, "<");
	tag = tag.replace(/&gt;/g, ">");
	tag = tag.replace(/&quot;/g, "\"");
	return tag;
}

function addTag(ele) {
    var thisTag = "[" + ele.innerHTML + "]";
    var taglist = document.getElementById('tags');
    var tags = taglist.value.match(/\[.+?\]/g);
    thisTag = getUnescapedTag(thisTag);
   
    // If tag is already listed, remove it
    if (tags === null) {
    	tags = [thisTag];
        ele.className = 'sTag';
    } else if (tags.contains(thisTag)) {
        tags = tags.remove(thisTag);
        ele.className = 'uTag';
    
    // Otherwise add it
    } else {
        tags.push(thisTag);
        ele.className = 'sTag';
    }

    var i = 0;
    taglist.value = "";
    while(tags[i]){
    	taglist.value = taglist.value + tags[i];
    	i++;
    }

    
    document.getElementById('tags').focus();
}

function chgtagotherdisp() {
	if(document.getElementById('rgn_content').style.display=='none'){
		document.getElementById('rgn_content').style.display='block';
		document.getElementById('rgn_button').innerHTML='- CLOSE';
	}else{
		document.getElementById('rgn_content').style.display='none';
		document.getElementById('rgn_button').innerHTML='+ OPEN';
	}
}
function chgtagaction(act) {
	if (act == 2){
		document.getElementById('rev_tag').style.display='block';
	} else {
		document.getElementById('rev_tag').style.display='none';
	}

	if (act == 3){
		document.getElementById('tags_list').style.display='block';
	} else {
		document.getElementById('tags_list').style.display='none';
	}

	if (act > 0){
		document.getElementById('tags_submit').style.display='block';
	} else {
		document.getElementById('tags_submit').style.display='none';
	}
}
