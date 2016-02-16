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
