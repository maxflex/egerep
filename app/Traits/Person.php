<?php

    namespace App\Traits;

    /**
     * Человек
     *
     * обладает свойствами: телефоны, формат имени
     */
    trait Person
    {
        public static $phone_fields = ['phone', 'phone2', 'phone3', 'phone4'];

        public function __construct(array $options = [])
        {
            // добавить полное имя и массив телефонов в $appends = []
            $this->appends = array_merge($this->appends, ['full_name', 'phones']);

            // добавить телефоны и комментарии к fillable
            foreach (static::$phone_fields as $phone_field) {
                $this->fillable[] = $phone_field;
                $this->fillable[] = $phone_field . '_comment';
            }

            parent::__construct($options);
        }

        public function getFullNameAttribute()
        {
            return $this->getName();
        }

        /**
         * Номера телефонов в виде массива
         */
        public function getPhonesAttribute()
        {
            $phones = [];
            foreach (static::$phone_fields as $phone_field) {
                $phone = $this->{$phone_field};
                if (! empty($phone)) {
                    $phones[] = $phone;
                }
            }
            return $phones;
        }

        /**
         * Вставить следующий номер телефона, если не существует
         */
         public function addPhone($phone)
         {
             // если еще не все номера телефонов заполнены
             if (count($this->phones) < count(static::$phone_fields)) {
                 // если номер телефона еще не был добавлен
                 if (! in_array($phone, $this->phones)) {
                     $this->{static::$phone_fields[count($this->phones)]} = $phone;
                 }
             }
         }

        /**
         * Отсортировать имя
         */
        public function getName($order = 'fio')
    	{
    		if (empty(trim($this->last_name)) && empty(trim($this->first_name)) && empty(trim($this->middle_name))) {
    			return "Неизвестно";
    		}

            $name = [];

    		if ($this->last_name) {
    			$name[0] = $this->last_name;
    		}

    		if ($this->first_name) {
    			$name[1] = $this->first_name;
    		}

    		if ($this->middle_name) {
    			$name[2] = $this->middle_name;
    		}

    		$order_values = [
    			'f' => 0,
    			'i' => 1,
    			'o' => 2,
    		];

            $name_ordered = [];

            foreach (range(0, 2) as $i) {
                $name_ordered[] = @$name[$order_values[$order[$i]]];
            }

    		return trim(implode(" ", $name_ordered));
    	}
    }
