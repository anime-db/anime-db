
/**
 * Загрусчик для скриптов и стилей
 * 
 * @author	Peter Gribanov
 * @since	22.12.10
 * @version	1.2
 * @param	string
 * @param	function
 * @return	boolen
 */
function include(url, callback){
	if (/\.js(\?.*)?$/i.test(url)){
	    var tag = document.createElement('script');
		tag.type = 'text/javascript';
		tag.src = url;
	} else if (/\.css(\?.*)?$/i.test(url)){
	    var tag = document.createElement('link');
		tag.rel = 'stylesheet';
		tag.type = 'text/css';
		tag.media = 'screen';
		tag.href = url;
	} else {
		return false;
	}
	if (callback) tag.onload = tag.onreadystatechange = callback;
    document.getElementsByTagName('head')[0].appendChild(tag);
	return true;
}
