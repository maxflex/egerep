	angular.element(document).ready(function() {
		setTimeout(function() {
			moment.locale('ru-RU')
			rebindMasks()
			setScope()
			configurePlugins()
			console.log('here')
		}, 50)
	})

	/**
	 * Remove by id
	 */
	function removeById(object, id) {
		return _.without(object, _.findWhere(object, {id: id}))
	}

	/**
	 * Svg map debug function.
	 */
	function clickSt(id) {
		$('iframe').contents().find('#st' + id).click();
	}
	function selectLine(id) {
		return $('iframe').contents().find('#line' + id);
	}

	/**
	 * Helper funciton for selectpicker
	 */
	function sp(id, placeholder) {
		setTimeout(function() {
			$('#sp-' + id).selectpicker({
				noneSelectedText: placeholder
			})
		}, 50)
	}

	function spRefresh(id) {
		setTimeout(function() {
			$('#sp-' + id).selectpicker('refresh')
		}, 50)
	}

	function spe(element, placeholder) {
		setTimeout(function() {
			$(element).selectpicker({
				noneSelectedText: placeholder
			})
		}, 50)
	}

	function speRefresh(element) {
		setTimeout(function() {
			$(element).selectpicker('refresh')
		}, 50)
	}

	/**
	 * Helper functions to start/stop ajax requests
	 */
	function ajaxStart() {
		NProgress.start()
	}

	function ajaxEnd() {
		NProgress.done()
	}

	/**
	 * Configure plugins
	 */
	function configurePlugins() {

	}

	/**
	 * Переназначает маски для всех элементов, включая новые
	 *
	 */
	function rebindMasks(delay) {
		// Немного ждем, чтобы новые элементы успели добавиться в DOM
		setTimeout(function() {
			// Дата
			$('.bs-date').datepicker({
				language	: 'ru',
				orientation	: 'top left',
				autoclose	: true
			})

			// Дата, начиная с нынчашнего дня
			$('.bs-date-now').datepicker({
				language	: 'ru',
				orientation	: 'top left',
				startDate: '-0d',
				autoclose	: true
			})

			// Дата вверху
			$(".bs-date-top").datepicker({
				language	: 'ru',
				autoclose	: true,
				orientation	: 'bottom auto',
			})

			// $(".bs-datetime").datetimepicker({
			// 	format: 'YYYY-MM-DD HH:mm',
			// 	locale: 'ru',
			// })
			//
			// $(".bs-date-default").datetimepicker({
			// 	format: 'YYYY-MM-DD',
			// 	locale: 'ru',
			// })

			$(".passport-number").inputmask("Regex", {regex: "[a-zA-Z0-9]{0,12}"});
			$(".digits-year").inputmask("Regex", {regex: "[0-9]{0,4}"});

			// REGEX для полей типа "число" и "1-5"
			$(".digits-only-float").inputmask("Regex", {regex: "[0-9]*[.]?[0-9]+"});
			$(".digits-only-minus").inputmask("Regex", {regex: "[-]?[0-9]*"});
			$(".digits-only").inputmask("Regex", {regex: "[0-9]*"});

			$.mask.definitions['H'] = "[0-2]";
		    $.mask.definitions['h'] = "[0-9]";
		    $.mask.definitions['M'] = "[0-5]";
		    $.mask.definitions['m'] = "[0-9]";
			$(".timemask").mask("Hh:Mm", {clearIfNotMatch: true});

			// Маска телефонов
			$(".phone-masked")
				.mask("+7 (999) 999-99-99", { autoclear: false })
		}, (delay === undefined ? 100 : delay)  )
	}

	/**
	 * Нотифай с сообщением об ошибке.
	 *
	 */
	function notifyError(message) {
		$.notify({'message': message, icon: "glyphicon glyphicon-remove"}, {
			type : "danger",
			allow_dismiss : false,
			placement: {
				from: "top",
			}
		});
	}

	/**
	 * Нотифай с сообщением об успехе.
	 *
	 */
	function notifySuccess(message) {
		$.notify({'message': message, icon: "glyphicon glyphicon-ok"}, {
			type : "success",
			allow_dismiss : false,
			placement: {
				from: "top",
			}
		});
	}

	// Установить scope
	function setScope() {
		scope = angular.element('[ng-app=Egerep]').scope()
	}


/**
 * Инициализировать array перед push, если он не установлен, чтобы не было ошибки.
 *
 */
function initIfNotSet(arr) {
	if (!arr) {
		arr = []
	}
	return arr
}

/**
 * Инициализировать array перед push, если он не установлен, чтобы не было ошибки.
 *
 */
function initIfNotSetObject(obj) {
	if (!obj) {
		obj = {}
	}
	return obj
}


/**
 * Анимация аякса.
 *
 */
function frontendLoadingStart()
{
	$("#frontend-loading").fadeIn(300)
}
function frontendLoadingEnd()
{
	$("#frontend-loading").hide()
}

/**
 * Печать дива.
 *
 */
function printDiv(id_div) {
	var contents = document.getElementById(id_div).innerHTML;
	var frame1 = document.createElement('iframe');
	frame1.name = "frame1";
	frame1.style.position = "absolute";
	frame1.style.top = "-1000000px";

	document.body.appendChild(frame1);
	var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
	frameDoc.document.open();
	frameDoc.document.write('<html><head><title>ЕГЭ Центр</title>');
	frameDoc.document.write("<style type='text/css'>\
		h4 {text-align: center}\
		p {text-indent: 50px; margin: 0}\
	  </style>"
	);
	frameDoc.document.write('</head><body>');
	frameDoc.document.write(contents);
	frameDoc.document.write('</body></html>');
	frameDoc.document.close();
	setTimeout(function () {
		window.frames["frame1"].focus();
		window.frames["frame1"].print();
		document.body.removeChild(frame1);
	}, 500);
	return false;
}

function redirect(url) {
	window.location.href = url
}

/**
 * Переместить курсор редактирования в конец content-editable.
 *
 */
function setEndOfContenteditable(contentEditableElement)
{
    var range,selection;
    if(document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
    {
        range = document.createRange();//Create a range (a range is a like the selection but invisible)
        range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        selection = window.getSelection();//get the selection object (allows you to change selection)
        selection.removeAllRanges();//remove any selections already made
        selection.addRange(range);//make the range you have just created the visible selection
    }
    else if(document.selection)//IE 8 and lower
    {
        range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
        range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        range.select();//Select the range (make it the visible selection
    }
}

// кол-во смс
!function(){var $,SmsCounter;window.SmsCounter=SmsCounter=function(){function SmsCounter(){}SmsCounter.gsm7bitChars="@£$¥èéùìòÇ\\nØø\\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\\\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";SmsCounter.gsm7bitExChar="\\^{}\\\\\\[~\\]|€";SmsCounter.gsm7bitRegExp=RegExp("^["+SmsCounter.gsm7bitChars+"]*$");SmsCounter.gsm7bitExRegExp=RegExp("^["+SmsCounter.gsm7bitChars+SmsCounter.gsm7bitExChar+"]*$");SmsCounter.gsm7bitExOnlyRegExp=RegExp("^[\\"+SmsCounter.gsm7bitExChar+"]*$");SmsCounter.GSM_7BIT="GSM_7BIT";SmsCounter.GSM_7BIT_EX="GSM_7BIT_EX";SmsCounter.UTF16="UTF16";SmsCounter.messageLength={GSM_7BIT:160,GSM_7BIT_EX:160,UTF16:70};SmsCounter.multiMessageLength={GSM_7BIT:153,GSM_7BIT_EX:153,UTF16:67};SmsCounter.count=function(text){var count,encoding,length,messages,per_message,remaining;encoding=this.detectEncoding(text);length=text.length;if(encoding===this.GSM_7BIT_EX){length+=this.countGsm7bitEx(text)}per_message=this.messageLength[encoding];if(length>per_message){per_message=this.multiMessageLength[encoding]}messages=Math.ceil(length/per_message);remaining=per_message*messages-length;if(remaining == 0 && messages == 0){remaining = per_message; }return count={encoding:encoding,length:length,per_message:per_message,remaining:remaining,messages:messages}};SmsCounter.detectEncoding=function(text){switch(false){case text.match(this.gsm7bitRegExp)==null:return this.GSM_7BIT;case text.match(this.gsm7bitExRegExp)==null:return this.GSM_7BIT_EX;default:return this.UTF16}};SmsCounter.countGsm7bitEx=function(text){var char2,chars;chars=function(){var _i,_len,_results;_results=[];for(_i=0,_len=text.length;_i<_len;_i++){char2=text[_i];if(char2.match(this.gsm7bitExOnlyRegExp)!=null){_results.push(char2)}}return _results}.call(this);return chars.length};return SmsCounter}();if(typeof jQuery!=="undefined"&&jQuery!==null){$=jQuery;$.fn.countSms=function(target){var count_sms,input;input=this;target=$(target);count_sms=function(){var count,k,v,_results;count=SmsCounter.count(input.val());_results=[];for(k in count){v=count[k];_results.push(target.find("."+k).text(v))}return _results};this.on("keyup",count_sms);return count_sms()}}}.call(this);
