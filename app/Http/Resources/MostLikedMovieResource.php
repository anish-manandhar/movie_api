<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MostLikedMovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            // 'id'            => $this->id,
            'movie'         => MovieResource::make($this->whenLoaded('movie')),
            'likes_count'   => $this->likes_count
        ];
    }
}
