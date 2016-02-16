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
     * @todo: функция пока не работает. не было тестов.
     */
    function throughNumbers(&$object, $fun)
    {
        foreach(['phone', 'phone2', 'phone3'] as $phone_field) {
            $object->{$phone_field} = $fun($object->{$phone_field});
        }
    }

    /**
     * Очистить номера телефонов у объекта
     */
    function cleanNumbers(&$object)
    {
        foreach(['phone', 'phone2', 'phone3'] as $phone_field) {
            $object->{$phone_field} = cleanNumber($object->{$phone_field});
        }
    }
