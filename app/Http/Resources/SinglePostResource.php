<?php

namespace App\Http\Resources;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SinglePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_title' => $this->title,
            'post_content' => $this->content,
            'author_id' => $this->user_id,
            'published_at' => $this->created_at,
            'last_update' => $this->updated_at,
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'author' => User::find($this->user_id),
            'comment_count' => Comment::where('post_id',$this->id)->count(),
            'likes_count' => Like::where('post_id',$this->id)
            ->where('user_id',$this->user_id)->count(),
            'comments' => Comment::where('post_id',$this->id)->get(),
        ];
    }
}
