<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavMovieResource;
use App\Http\Resources\MostLikedMovieResource;
use App\Mail\SendEmail;
use App\Models\FavMovie;
use App\Models\MostLikedMovie;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function most_liked_movies(){
        $movies = MostLikedMovie::with('movie')->orderBy('likes_count','desc')->get();

        return response()->json([
            'fav_movies' => MostLikedMovieResource::collection($movies),
            'status' => 200
        ]);
    }

    public function listing_fav_movies(Request $request){
        $validator = Validator::make($request->all(), [
            'movie_id' => 'bail|required|exists:movies,id',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $validated = $validator->validated();

        $validated['user_id'] = auth()->id();

        if(FavMovie::where([
            'user_id' => $validated['user_id'],
            'movie_id' => $validated['movie_id'],
        ])->exists()){
            return response()->json([
                'message' => 'This movie is already in your list.',
                'status' => 200
            ]);
        }
        else{
            FavMovie::create($validated);

            $movie = Movie::find($validated['movie_id']);
            $liked_movie = MostLikedMovie::where('movie_id',$validated['movie_id'])->first();

            if($liked_movie){
                $liked_movie->update([
                    'likes_count' => $liked_movie->likes_count + 1
                ]);
            }else{
                MostLikedMovie::create([
                    'movie_id' => $validated['movie_id'],
                    'likes_count' => 1
                ]);
            }

            $user = User::find($validated['user_id']);

            $mailData = [
                'message' => 'You have added ' .$movie->title . ' to your favorite movie list. Thank You.'
            ];

            Mail::to($user->email)->send(new SendEmail($mailData));

            return response()->json([
                'message' => 'Successfully Added Movie in list.',
                'status' => 200
            ]);
        }
    }

    public function users_fav_movie(){
        $fav_movie = FavMovie::where('user_id',auth()->id())->with(['user','movie'])->get();

        if($fav_movie){
            return response()->json([
                'fav_movies' => FavMovieResource::collection($fav_movie),
                'status' => 200
            ]);
        }
        else{
            return response()->json([
                'message' => 'No Movie in your list.',
                'status' => 200
            ]);
        }
    }
}
