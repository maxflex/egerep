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

        /**
         * Получить маркеры по типу
         */
        public function getMarkers($type)
        {
            return $this->markers()->where('type', $type)->get();
        }

        public function save(array $options = [])
        {
            $this->_saveMarkers();
            parent::save($options);
        }


        private function _saveMarkers()
        {
            // Пересохраняем маркеры лишь в том случае, если они были изменены
            if (is_object($this->markers)) {
                return;
            }

            $this->markers()->delete();
            foreach ($this->markers as $data) {
                $new_marker = $this->markers()->create($data);
                // сохраняем ближайшие станции метки
                foreach ($data['metros'] as $metro) {
                    // на время переноса
                    // $new_marker->metros()->create([
                    //     'minutes'   => $metro->minutes,
                    //     'meters'    => $metro->meters,
                    //     'station_id'=> $metro->station->id,
                    // ]);
                    // \на время переноса

                    $new_marker->metros()->create([
                        'minutes'   => $metro['minutes'],
                        'meters'    => $metro['meters'],
                        'station_id'=> $metro['station']['id'],
                    ]);
                }
            }
            unset($this->markers);
        }
    }
