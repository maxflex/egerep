<?php

    namespace App\Traits;

    /**
     * Имеет маркеры
     *
     * @usage: добавить 'markers' в $fillable и $with
     */
    trait Markerable
    {
        public function markers()
        {
            return $this->morphMany('App\Models\Marker', 'markerable');
        }

        public function save(array $options = [])
        {
            $this->_saveMarkers();
            parent::save($options);
        }


        private function _saveMarkers()
        {
            $this->markers()->delete();
            foreach ($this->markers as $data) {
                $this->markers()->create($data);
            }
            unset($this->markers);
        }
    }
