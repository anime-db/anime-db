
/**
 * Загрусчик для скриптов и стилей
 * 
 * @author	Peter Gribanov
 * @since	22.12.10
 * @version	2.6
 */
var Source = {

	/**
	 * Список загруженных элиментов
	 * 
	 * @var	Array
	 */
	_loaded : [],

	/**
	 * Объект XMLHttpRequest
	 * 
	 * @var	XMLHttpRequest
	 */
	_request : null,

	/**
	 * Браузер IE
	 * 
	 * @var	boolen
	 */
	_ie : /MSIE/i.test(navigator.userAgent),

	/**
	 * Загружает код
	 * 
	 * @param	string
	 * @param	function
	 * @return	boolen
	 */
	include : function(url, callback){
		// не удалось создать объект XMLHttpRequest
		if (!this._request) return null;
		// скрипт уже загружен
		if (url in this._loaded){
			// вызов обработчика загруски
			(callback ? callback : function(){})();
			return null;
		}
		// определение 
		if(/\.js(\?.*)?$/i.test(url)){
		} else if (/\.css(\?.*)?$/i.test(url)){
			tag = document.createElement('link');
			tag.rel = 'stylesheet';
			tag.type = 'text/css';
			tag.media = 'screen';
			tag.href = url;
			if (callback) tag.onload = tag.onreadystatechange = callback;
			document.getElementsByTagName('head')[0].appendChild(tag);
			return null;
		} else {
			return null;
		}
		// загруска
		this._request.open('get', url, true);
		this._request.onreadystatechange = function(){
			try {
				if (Source._request.readyState==4 && Source._request.status==200){
					// получение и выполнение результата
					var code = Source._request.responseText;
					if (code) Source._ie ? window.execScript(code) : window.eval(code);
					// добавление в список загруженных
					Source._loaded[url] = true;
					// вызов обработчика загруски
					(callback ? callback : function(){})();
				}
			} catch(e){}
		};
		// загружаем код
		this._request.send(null);
		return null;
	}

};


(function(){
	// создаем объект XMLHttpRequest
	try {
		Source._request = new XMLHttpRequest();
	} catch(e){
		var versions = new Array(
			'MSXML2.XMLHTTP.6.0', 'MSXML2.XMLHTTP.5.0',
			'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0',
			'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'
		);
		for (var i=0; i<versions.length && !Source.request; i++){
			try {
				Source._request = new ActiveXObject(versions[i]);
			} catch(e){}
		}
		versions = null;
	}
})();
