<?php
    function preType($Object)
    {
        echo '<pre>';
        print_r($Object);
        echo '</pre>';
    }

    /**
	 * Форматировать дату в наш формат.
	 *
	 */
	function dateFormat($date, $notime = false)
	{
		$date = date_create($date);
		return date_format($date, $notime ? "d.m.Y" : "d.m.Y в H:i");
	}

    /**
     * Возвратить чистый номер телефона.
     *
     */
    function cleanNumber($number)
    {
        return preg_replace("/[^0-9]/", "", $number);
    }

    /**
     * Пребежать по номерам телефона
     * функция должна возвращать номер телефона
     * @example throughNumbers($tutor, function($number)) {
     *              return $number . '123';
     *          }
     *
     */
    function throughNumbers(&$object, $func)
    {
        foreach (['phone', 'phone2', 'phone3'] as $phone_field) {
            $object->{$phone_field} = $func($object->{$phone_field});
        }
    }

    /**
     * Очистить номера телефонов у объекта
     */
    function cleanNumbers(&$object)
    {
        throughNumbers($object, 'cleanNumber');
    }

    /*
	 * В формат ангуляра
	 */
	function ngInitOne($name, $Object)
	{
		return $name." = ".htmlspecialchars(json_encode($Object, JSON_NUMERIC_CHECK)) ."; ";
	}

	/*
	 * Инициализация переменных ангуляра
	 * $array – [var_name = {var_values}; ...]
	 * @return строка вида 'a = {test: true}; b = {var : 12};'
	 */
	function ngInit($array)
	{
        $return = '';
        
		foreach ($array as $var_name => $var_value) {
			// Если значение не установлено, то это пустой массив по умолчанию
			if (!$var_value && !is_int($var_value)) {
				$var_value = "[]";
			} else {
				// иначе кодируем объект в JSON
				$var_value = htmlspecialchars(json_encode($var_value, JSON_NUMERIC_CHECK));
			}
			$return .= $var_name." = ". $var_value ."; ";
		}

		return $return;
	}
