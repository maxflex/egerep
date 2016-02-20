<?php

    namespace App\Traits;

    /**
     * Человек
     *
     * обладает свойствами: телефоны, формат имени
     */
    trait Person
    {
        public function __construct(array $options = [])
        {
            $this->appends[] = 'full_name';
            parent::__construct($options);
        }

        public function getFullNameAttribute()
        {
            return $this->getName();
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
