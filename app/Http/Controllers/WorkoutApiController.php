<?php

namespace App\Http\Controllers;

use App\User;
use App\Workout;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class WorkoutApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $search_term = $request->input('search');
        $limit = $request->input('limit') ? $request->input('limit') : 5;

        if ($search_term) {
            $workouts = Workout::orderBy('id', 'DESC')->where('workout_description', 'LIKE', "%$search_term%")->with(
                    array('User' => function($query) {
                        $query->select('id', 'name');
                })
            )->select('id', 'workout_description', 'user_id')->paginate($limit);

            $workouts->appends(array(
                'search' => $search_term,
                'limit' => $limit
            ));
        } else {
            $workouts = Workout::orderBy('id', 'DESC')->with(
                array('User' => function($query) {
                    $query->select('id', 'name');
                })
            )->select('id', 'workout_description', 'user_id')->paginate($limit);
        }

        $workouts->appends(array(
            'limit' => $limit
        ));

        return Response::json($this->transformCollection($workouts), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if(!$request->description || !$request->user_id) {
            return Response::json([
                'error' => [
                    'message' => 'Please provide both description and user_id'
                ]
            ], 422);
        }

        $workout = Workout::create($request->all());

        return Response::json([
            'message' => 'Workout created successfully.',
            'data' => $this->transformCollection($workout)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $workout = Workout::with(
            array('User' => function($query) {
                $query->select('id', 'name');
            })
        )->find($id);

        if (!$workout) {
            return Response::json([
                'error' => [
                    'message' => 'Workout does not exist'
                ]
            ], 404);
        }

        // get previous workout id
        // $previous = Workout::where('id', '<', $workout->id)->max('id');

        //get next workout id
        $next = Workout::where('id', '>', $workout->id)->min('id');

        return Response::json([
            'previous_workout_id' => $previous,
            'next_workout_id' => $next,
            'data' => $this->transform($workout)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (!$request->description || !$request->user_id) {
            return Response::json([
                'error' => [
                    'message' => 'Please provide both description and user_id'
                ]
            ], 422);
        }

        $workout = Workout::find($id);
        $workout->description = $request->body;
        $workout->user_id = $request->user_id;
        $workout->save();

        return Response::json([
            'message' => 'Workout updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Workout::destroy($id);
    }

    private function transformCollection($workouts) {
        $workoutsArray = $workouts->toArray();
        return [
            'total' => $workoutsArray['total'],
            'per_page' => intval($workoutsArray['per_page']),
            'current_page' => $workoutsArray['current_page'],
            'last_page' => $workoutsArray['last_page'],
            'next_page_url' => $workoutsArray['next_page_url'],
            'prev_page_url' => $workoutsArray['prev_page_url'],
            'from' => $workoutsArray['from'],
            'to' => $workoutsArray['to'],
            'data' => array_map([$this, 'transform'], $workoutsArray['data'])
        ];
    }

    private function transform($workout) {
        return [
            'workout_id' => $workout['id'],
            'workout' => $workout['workout_description'],
            'submitted_by' => $workout['user_id']['name']
        ];
    }
}
