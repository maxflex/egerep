logout_interval = false

window.logoutCountdownClose = ->
	clearInterval(logout_interval)
	logout_interval = false
	$('#logout-modal').modal('hide')

window.logoutCountdown = ->
	seconds = 60
	$('#logout-seconds').html(seconds)
	$('#logout-modal').modal('show')
	logout_interval = setInterval ->
		seconds--
		$('#logout-seconds').html(seconds)
		if seconds <= 1
			clearInterval(logout_interval)
			# перезагружаем страницу, к этому времени должно выбить
			setTimeout ->
				location.reload()
			, 1000
	, 1000

window.continueSession = ->
	$.get "api/continue-session"
	logoutCountdownClose()