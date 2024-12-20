<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SinglePostResource;
class PostController extends Controller
{
    //Add new post
    public function addNewPost(Request $request){
        $validated = Validator::make($request->all(),[
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = auth()->user()->id;
             // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images/post', 'public');
                $post->image_path = $path; // Store the path in the database
            }
            $post->save();
            // Generate the full URL for the image
            $fullImageUrl = $post->image_path ? url('storage/' . $post->image_path) : null;
             //return
             return response()->json([
                'message' => 'Post added successfully',
                'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user_id' => $post->user_id,
                'image_url' => $fullImageUrl, // Full image URL here
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ],
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }
    public function editPost(Request $request, $post_id)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 403);
        }

        try {
            // Find the post
            $post = Post::findOrFail($post_id);

            // Update the fields
            $post->title = $request->title;
            $post->content = $request->content;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($post->image_path && \Storage::disk('public')->exists($post->image_path)) {
                    \Storage::disk('public')->delete($post->image_path);
                }

                // Upload the new image
                $newImagePath = $request->file('image')->store('images/post', 'public');
                $post->image_path = $newImagePath;
            }

            // Save the updated post
            $post->save();

            // Generate the full image URL
            $fullImageUrl = $post->image_path ? url('storage/' . $post->image_path) : null;

            return response()->json([
                'message' => 'Post updated successfully',
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'image_url' => $fullImageUrl, // Full image URL
                    'updated_at' => $post->updated_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
     //retrieve all posts
     public function getAllPosts(){
        try {
            $posts = Post::all();
            $formattedPosts = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'user_id' => $post->user_id,
                    'image_url' => $post->image_path ? url('storage/' . $post->image_path) : null,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });
            return response()->json([
                'posts' =>  $formattedPosts
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    //Retrive Single post
    //fetch single post
    public function getPost($post_id){
        try {
            $post = Post::find($post_id);
            // $post = Post::with('user','comment','likes')->where('id',$post_id)->first();
            $post_data = new SinglePostResource($post);
            return response()->json([
                'post' => $post_data
            ],200);
        } catch (\Exception $th) {
                        return response()->json(['error' => $th->getMessage()],403);
        }
    }

    public function deletePost(Request $request, $post_id){
        try {
            $post = Post::find($post_id);
            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }
             // Delete the associated image if it exists
            if ($post->image_path && \Storage::disk('public')->exists($post->image_path)) {
                \Storage::disk('public')->delete($post->image_path);
            }
            $post->delete();
            return response()->json([
                'message' => 'post deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
