<?php
    function dateRange($strDateFrom, $strDateTo)
    {
        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }

    function preType($Object)
    {
        echo '<pre>';
        print_r($Object);
        echo '</pre>';
    }

    function emptyObject()
    {
        return (object)[];
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
     * Деформатировать дату
     */
    function fromDotDate($date, $add_year = null)
    {
        $parts = explode('.', $date);
        if ($add_year !== null) {
            $parts[2] = $add_year . $parts[2];
        }
        return implode('-', array_reverse($parts));
    }

    /**
     * Возвратить чистый номер телефона.
     *
     */
    function cleanNumber($number)
    {
        $number = preg_replace("/[^0-9]/", "", $number);
        if ($number && $number[0] != '7') {
            $number .= '7';
        }
        return $number;
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
        foreach (App\Traits\Person::$phone_fields as $phone_field) {
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
			// if (!$var_value && !is_int($var_value)) {
			// 	$var_value = "[]";
			// } else {
				// иначе кодируем объект в JSON
				// $var_value = htmlspecialchars(json_encode($var_value, JSON_NUMERIC_CHECK));
				$var_value = htmlspecialchars(json_encode($var_value));
			// }
			$return .= $var_name." = ". $var_value ."; ";
		}

		return ['nginit' => $return];
	}

    function isProduction()
    {
        return app()->environment() == 'production';
    }

    /**
     * Возвратить user id из сесси или 0 (system)
     */
    function userIdOrSystem()
    {
        return \App\Models\User::loggedIn() ? \App\Models\User::fromSession()->id : 0;
    }

    function now($no_time = false)
    {
        return date('Y-m-d' . ($no_time ? '' : ' H:i:s'));
    }

    function isBlank($value) {
        return empty($value) && !is_numeric($value);
    }

    function notBlank($value) {
        return ! isBlank($value);
    }

    function isFilled($value)
    {
        return (isset($value) && ! empty($value));
    }

    /**
     * Разбить enter'ом
     */
    function breakLines($array)
    {
        return implode('

', array_filter($array));
    }


    /**
     * Удалить пустые строки
     */
    function filterParams($a)
    {
        return (object)array_filter((array)$a, function($e) {
            return $e !== '';
        });
    }

    function pluralize($one, $few, $many, $n)
	{
		$text = $n%10==1&&$n%100!=11?$one:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$few:$many);
        return $n . ' ' . $text;
	}

    function minutesTillNextDay()
    {
        return (strtotime('tomorrow') - time()) / 60;
    }

    function secondsTillNextDay()
    {
        return strtotime('tomorrow') - time();
    }

    function wrapString($value)
    {
        return "'" . $value. "'";
    }

    function set_active($path, $active = 'active') {
       return "href={$path}" . (call_user_func_array('Request::is', (array)$path) ? " class={$active}" : '');
   }

   function allowed($right, $return_int = false)
   {
       $allowed = \App\Models\User::fromSession()->allowed($right);
       return $return_int ? (int)$allowed : $allowed;
   }

   /**
     * Factory connection helper
     */
    function dbFactory($table)
    {
        return \DB::connection('factory')->table($table);
    }

    /**
      *
      * EGCRM connection helper
      */
     function dbEgecrm($table)
     {
         return \DB::connection('egecrm')->table($table);
     }

    function fileExists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if( $httpCode == 200 ){return true;}
    }

    // @todo это неправильно – subjects хранится в двух местах: в factory и здесь
    function getSubjectString($subject_ids)
    {
        $subject_ids = explode(',', $subject_ids);
        $subject_ids = array_filter($subject_ids);
        $subjects = [
            1   => 'МАТ',
            2   => 'ФИЗ',
            3   => 'ХИМ',
            4   => 'БИО',
            5   => 'ИНФ',
            6   => 'РУС',
            7   => 'ЛИТ',
            8   => 'ОБЩ',
            9   => 'ИСТ',
            10  => 'АНГ',
            11  => 'ГЕО'
        ];
        return implode('+', array_map(function($subject_id) use ($subjects) {
            return $subjects[$subject_id];
        }, $subject_ids));
    }
