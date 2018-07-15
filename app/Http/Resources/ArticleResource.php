<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'author_id'   => $this->author_id,
            'title'       => $this->title,
            'description' => $this->description,
            'keywords'    => $this->keywords,
            'subtitle'    => $this->subtitle,
            'up_body'     => $this->up_body,
            'down_body'   => $this->down_body,
            'status'      => $this->status,
            'phone'       => $this->phone,
            'views'       => $this->views ?: 0,
            'true_views'  => $this->true_views ?: 0,
            'asks'        => $this->asks ?: 0,
            'true_asks'   => $this->true_asks ?: 0,
            'tags'        => TagResource::collection($this->tags),
            'house_type'  => HouseTypeResource::collection($this->house_type),
            'created_at'  => $this->created_at->toDateTimeString()
        ];
    }
}
