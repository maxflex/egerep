/*
	 * Управление кареткой в разделе periods
	 * https://192.168.0.32/repetitors/periods/edit/?rep_id=5&id=2289
	 */
	caret = 0; // Позиция каретки
	function periodsCursor(y, x, event)
	{
		// Получаем начальный элемент (с которого возможно сдвинемся)
		original_element = $("#i-" + y + "-" + x);

		// Если был нажат 0, то подхватываем значение поля сверху
		if (original_element.val() == 0 && original_element.val().length) {
			for (var i = (y - 1); i > 0; i--) {
				// Поверяем существует ли поле сверху
				if ($("#i-" + i + "-" + x).length && $("#i-" + i + "-" + x).val()) {
					// Присваеваем текущему элементу значение сверху
					original_element.val($("#i-" + i + "-" + x).val());
					break;
				}
			}
		}

		// Если внутри цифр, то не прыгаем к следующему элементу
		if (original_element.caret() != caret) {
			caret = original_element.caret();
			return;
		}
		console.log(event.which);
		switch (event.which) {
			// ВЛЕВО
			case 37: {
				moveCursor(x, y, "left");
        break;
			}
			// ВВЕРХ
			case 38: {
				moveCursor(x, y, "up");
				break;
			}
			// ВПРАВО
			case 39: {
				moveCursor(x, y, "right");
				break;
			}
			// ВНИЗ
			case 13:
			case 40: {
				moveCursor(x, y, "down");
				break;
			}
		}
	}

	/*
	 * Перевести курсор, если элемент существует
	 */
	function moveCursor(x, y, direction)
	{
		// Определяем направление и изменяем координаты соответствующим образом
		switch (direction) {
			case "left": {
				x--;
				break;
			}
			case "right": {
				x++;
				break;
			}
			case "up": {
				y--;
				break;
			}
			case "down": {
				y++;
			}
		}


		// Если двигаемся в несуществующие поля
		if (x < 1 || y < 1) {
			return;
		}

		// Получаем новый элемент
		el = $("#i-" + y + "-" + x);

		// Если элемент существует, двигаемся туда
		if (el.length) {
			caret = 0;
			el.focus();
		} else {
			moveCursor(x, y, direction); // Если не получилось, пытаемся передвинуться еще раз (перепрыгнуть через несколько ячеек сразу)
		}
	}

	/*
	 * Подсветить строку в зависимости от выбранной ячейки
	 * https://192.168.0.32/repetitors/periods/edit/?rep_id=10&id=2272
	 */
	function selectRow(id_row)
	{
		$("tr[id^='tr-']").removeClass("selected");
		$("#tr-" + id_row).addClass("selected");
	}
