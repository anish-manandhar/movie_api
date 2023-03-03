<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavMovieResource;
use App\Models\FavMovie;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required|string',
            'description' => 'bail|required|string',
            'release_date' => 'bail|required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $validated = $validator->validated();
        $validated['poster'] = auth()->id();

        Movie::create($validated);

        return response()->json([
            'message' => 'New Movie Successfully Added.',
            'status' => 200
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'movie_id' => 'bail|required|exists:movies,id',
            'title' => 'bail|required|string',
            'description' => 'bail|required|string',
            'release_date' => 'bail|required|date'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $movie = Movie::find($request->movie_id);

        if($movie){
            $movie->update($validator->validated());

            return response()->json([
                'message' => 'Movie Successfully Updated.',
                'status' => 200
            ]);
        }
        else{
            return response()->json([
                'message' => 'No Movie Found!',
                'status' => 200
            ]);
        }
    }

    public function users_fav_movie(Request $request){
        $validator = Validator::make($request->all(), [
            'start_date' => 'bail|nullable|date',
            'end_date' => 'bail|nullable|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $validated = $validator->validated();

        $movie = FavMovie::when($validated, function ($query) use($validated){
                $query->whereHas('movie', function ($query) use ($validated){
                    $query->whereDate('release_date', '>=', $validated['start_date'])->whereDate('release_date', '<=', $validated['end_date']);
                });
            })
            ->with(['user','movie'])
            ->get();

        return response()->json([
            'movies' => FavMovieResource::collection($movie),
            'status' => 200
        ]);
    }

    public function publish(Request $request){
        $validator = Validator::make($request->all(), [
            'movie_id' => 'bail|required|exists:movies,id',
            'is_published' => 'bail|required|boolean',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $movie = Movie::find($request->movie_id);

        if($movie){
            $movie->update([
                'is_published' => $validator->validated()['is_published']
            ]);

            return response()->json([
                'message' => 'Movie Successfully Published.',
                'status' => 200
            ]);
        }
        else{
            return response()->json([
                'message' => 'No Movie Found!',
                'status' => 200
            ]);
        }
    }
}
