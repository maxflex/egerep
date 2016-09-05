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

        public function saveMarkers()
        {
            // Пересохраняем маркеры лишь в том случае, если они были изменены
            if (is_object($this->markers)) {
                return;
            }
            foreach ($this->markers as $data) {
                // сохраняем лишь в том случае, если маркер не был добавлен ранее
                if (! isset($data['server_id'])) {
                    $new_marker = $this->markers()->create($data);
                    // сохраняем ближайшие станции метки
                    foreach ($data['metros'] as $metro) {
                        $new_marker->metros()->create([
                            'minutes'   => $metro['minutes'],
                            'meters'    => $metro['meters'],
                            'station_id'=> $metro['station']['id'],
                        ]);
                    }
                }
            }
            unset($this->markers);
        }
    }
