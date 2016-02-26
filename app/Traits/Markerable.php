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
                $new_marker = $this->markers()->create($data);
                // сохраняем ближайшие станции метки
                foreach ($data['metros'] as $metro) {
                    // на время переноса
                    $new_marker->metros()->create([
                        'minutes'   => $metro->minutes,
                        'meters'    => $metro->meters,
                        'station_id'=> $metro->station->id,
                    ]);
                    // \на время переноса

                    // $new_marker->metros()->create([
                    //     'minutes'   => $metro['minutes'],
                    //     'meters'    => $metro['meters'],
                    //     'station_id'=> $metro['station']['id'],
                    // ]);
                }
            }
            unset($this->markers);
        }
    }
