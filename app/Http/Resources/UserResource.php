<?php

namespace App\Http\Resources;

class UserResource extends Resource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->when(!empty($this->phone), $this->phone),
            'identify'      => $this->identify,
            'remake'        => $this->remake,
            'sex'           => $this->sex,
            'status'        => $this->status,
            'article_count' => $this->article_count ?: 0,
            'created_at'    => $this->created_at->toDateTimeString()
        ];
    }
}
