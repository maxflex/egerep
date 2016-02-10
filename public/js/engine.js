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
	function rebindMasks() {
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

			$(".bs-datetime").datetimepicker({
				format: 'YYYY-MM-DD HH:mm',
				locale: 'ru',
			})

			$(".bs-date-default").datetimepicker({
				format: 'YYYY-MM-DD',
				locale: 'ru',
			})

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
				.on("keyup", function() {
					// console.log($(this).val())
					// console.log(scope.client.phone)
					// t = $(this)
					//
					// // если номер не заполнен -- выйти
					// if (!t.val()) {
					// 	return
					// }
					//
					// // если есть нижнее подчеркивание, то номер заполнен не полностью
					// not_filled = t.val().match(/_/)
					//
					// // если номер полностью заполнен
					// if (!not_filled) {
					// 	$.ajax({
					// 		type: "POST",
					// 		url: "ajax/checkPhone",
					// 		data: {'phone': t.val(), 'id_request': ang_scope.id_request},
					// 		success: function(response) {
					// 				if (response == "true") {
					// 					ang_scope.phone_duplicate = response
					// 					t.addClass("has-error-bold")
					// 				} else {
					// 					ang_scope.phone_duplicate = null
					// 					t.removeClass("has-error-bold")
					// 				}
					// 				ang_scope.$apply()
					// 			},
					// 		async: false
					// 	})
					// } else {
					// 	t.removeClass("has-error-bold")
					// 	ang_scope.phone_duplicate = null
					// 	ang_scope.$apply()
					// }
				})

			// FLOAT-LABEL
			// $(".floatlabel").floatlabel();
		}, 100)
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
