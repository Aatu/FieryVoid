jQuery.fn.elementlocation = function() {
    var curleft = 0;
    var curtop = 0;
    var obj = this;
 
    do {
		curleft += obj.attr('offsetLeft');
		curtop += obj.attr('offsetTop');
		obj = obj.offsetParent();

    } while ( obj.attr('tagName') != 'BODY' );
	
return ( {x:curleft, y:curtop} );
};