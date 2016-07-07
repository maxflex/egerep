<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Metro;

class TutorsController extends Controller
{
    /**
     * Get list counts
     */
    public function counts(Request $request)
    {
        return [
            'state_counts'      => Tutor::stateCounts($request->user_id, $request->published_state),
            'user_counts'       => Tutor::userCounts($request->state, $request->published_state),
            'published_counts'  => Tutor::publishedCounts($request->state, $request->user_id),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Tutor::searchByState($request->state)
                    ->searchByUser($request->user_id)
                    ->searchByDebtor($request->debtor)
                    ->searchByLastNameAndPhone($request->global_search)
                    ->searchByPublishedState($request->published_state)
                    ->paginate(30, ['clients_count'])
                    ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Tutor::create($request->input());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Tutor::with(['markers'])->find($id)->append('banned')->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Tutor::find($id)->update($request->input());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Tutor::find($id)->delete();
    }

    /**
     * Get a list of only tutor_id => tutor full name
     */
     public function lists()
     {
         return Tutor::selectRaw("CONCAT_WS(' ', last_name, first_name, middle_name) as name, id")
            ->pluck('name', 'id');
     }

     public function deletePhoto($id)
     {
        $tutor = Tutor::find($id);
        Tutor::where('id', $id)->update(['photo_extension' => '']);

        @unlink($tutor->photoPath());
        @unlink($tutor->photoPath('_original'));
        @unlink($tutor->photoPath('@2x'));
     }

     public function filtered(Request $request)
     {
         extract(array_filter($request->search));

         // анализируем только не закрытых преподавателей с метками
         $query = Tutor::has('markers')->where('state', '<>', 3);

         if (isset($id)) {
             $query->where('id', $id);
         }

         if (isset($last_name)) {
             $query->where('last_name', 'like', "%{$last_name}%");
         }

         if (isset($first_name)) {
             $query->where('first_name', 'like', "%{$first_name}%");
         }

         if (isset($middle_name)) {
             $query->where('middle_name', 'like', "%{$middle_name}%");
         }

         if (isset($gender)) {
             $query->whereIn('gender', $gender);
         }

         if (isset($age_from)) {
             $query->where('birth_year', '<', Tutor::getAge($age_from));
         }

         if (isset($age_to)) {
             $query->where('birth_year', '>', Tutor::getAge($age_to));
         }

         if (isset($grades)) {
             $rawSql = '';
             foreach ($grades as $k => $grade) {
                 $rawSql .= ($k ? ' OR ' : '')." FIND_IN_SET('$grade',grades) ";
             }
             $rawSql = ' ('.$rawSql.') ';
             $query->whereRaw($rawSql);
         }

         if (isset($subjects)) {
             $rawSql = '';
             foreach ($subjects as $k => $subject) {
                 $rawSql .= ($k ? ' OR ' : '')." FIND_IN_SET('$subject',subjects) ";
             }
             $rawSql = ' ('.$rawSql.') ';
             $query->whereRaw($rawSql);
         }

         if (isset($tb_from)) {
             $query->where('tb', '>=', $tb_from);
         }

         if (isset($lk_from)) {
             $query->where('lk', '>=', $lk_from);
         }

         if (isset($js_from)) {
             $query->where('js', '>=', $js_from);
         }

         if (isset($lesson_price_to)) {
             $query->where('public_price', '<=', $lesson_price_to);
         }

         if (isset($state)) {
             $query->whereIn('state', $state);
         }

         # Оставляем только зеленые маркеры, если клиент едет к репетитору
         if ($destination == "k_r") {
             # отсеиваем репетиторов без зеленых маркеров
             $query->whereHas('markers', function($query) {
               $query->where('type', 'green');
             });
             # оставляем только зеленые маркеры, если у репетитора есть и те, и другие
             $query->with(['markers' => function($query) {
               $query->where('type', 'green');
             }]);
         } else {
             # если "Клиент едет к репетитору", то только репетиторы с картой выезда
             $query->where('svg_map', '<>', '');
         }

         # выбираем только нужные поля для ускорения запроса
         $tutors = $query->get([
             'id',
             'first_name',
             'last_name',
             'middle_name',
             'svg_map',
             'photo_extension',
             'birth_year',
             'subjects',
             'tb',
             'lk',
             'js',
         ] + Tutor::$phone_fields);

         foreach($tutors as $tutor) {
            # Количество учеников, Количество встреч
            $tutor->append(['clients_count', 'meeting_count']);

            # Получить минуты
            $tutor->minutes = $tutor->getMinutes($request->client_marker);
         }

         return $tutors;
     }
}
