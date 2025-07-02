<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
   

    public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'body' => $this->body,
        'author_name' => $this->author->name ?? 'N/A',
        'category' => [
            'id' => $this->category->id ?? null,
            'name' => $this->category->name ?? 'N/A',
        ],
        'created_at' => $this->created_at->toDateTimeString(),
    ];
}

}
