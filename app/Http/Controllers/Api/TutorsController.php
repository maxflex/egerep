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
            'state_counts'  => Tutor::stateCounts($request->user_id),
            'user_counts'   => Tutor::userCounts($request->state),
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
                        ->searchByLastNameAndPhone($request->global_search)
                        ->paginate(30)->toJson();
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
        return Tutor::find($id)->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // dd($request->input());
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

         $query = Tutor::has('markers');

         if (isset($id)) {
             $query->where('id', $id);
         }

         if (isset($last_name)) {
             $query->where('last_name', 'LIKE', "%{$last_name}%");
         }

         if (isset($first_name)) {
             $query->where('first_name', 'LIKE', "%{$first_name}%");
         }

         if (isset($middle_name)) {
             $query->where('middle_name', 'LIKE', "%{$middle_name}%");
         }

         if (isset($gender)) {
             $query->whereIn('gender', $gender);
         }

         if (isset($age_from)) {
             $birth_end = date("Y") - (isset($age_from) ? $age_from : 0);
             $query->where('birth_year', '<', $birth_end);
         }

         if (isset($age_to)) {
             $birth_start = date("Y") - (isset($age_to) ? $age_to : 200);
             $query->where('birth_year', '>', $birth_start);
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
         }

         $tutors = $query->get();

         foreach($tutors as $tutor) {
            # Количество учеников
            $tutor->append('clients_count');

            # Количество встреч
            $tutor->meeting_count = $tutor->getMeetingCount();

            # Получить минуты
            $tutor->minutes = $tutor->getMinutes($request->client_marker);
         }

         return $tutors;
     }
}
