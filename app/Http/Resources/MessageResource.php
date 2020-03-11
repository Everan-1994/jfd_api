<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'phone'      => $this->phone,
            'home_type'  => $this->home_type,
            'status'     => $this->status,
            'remake'     => $this->remake,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'title'      => !empty($this->article) ? $this->article->title : '文章已删除',
            'username'   => !empty($this->users) ? $this->users->name : '用户已删除'
        ];
    }
}
