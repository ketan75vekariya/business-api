<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //Add new post
    public function addNewPost(Request $request){
        $validated = Validator::make($request->all(),[
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = auth()->user()->id;
            $post->save();

             //return
             return response()->json([
                'message' => 'Post added successfully',
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

     //edit a post
    //  public function editPost(Request $request){
    //     $validated = Validator::make($request->all(),[
    //         'title' => 'required|string',
    //         'content' => 'required|string',
    //         'post_id' => 'required|integer',
    //     ]);

    //     if ($validated->fails()) {
    //         return response()->json($validated->errors(),403);
    //     }

    //     try {
    //         $post_data = Post::find($request->post_id);

    //        $updatePost = $post_data->update([
    //             'title' => $request->title,
    //             'content' => $request->content,
    //         ]);
    //          //return
    //          return response()->json([
    //             'message' => 'Post updated successfully',
    //             'updated_post' => $updatePost,
    //         ],200);

    //     } catch (\Exception $th) {
    //         return response()->json(['error' => $th->getMessage()],403);
    //     }
    // }

    //edit a post approach 2
    public function editPost(Request $request,$post_id){
        $validated = Validator::make($request->all(),[
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        try {
            $post_data = Post::find($post_id);

           $updatePost = $post_data->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
             //return
             return response()->json([
                'message' => 'Post updated successfully',
                'updated_post' => $updatePost,
            ],200);

        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);
        }
    }

     //retrieve all posts
     public function getAllPosts(){
        try {
            $posts = Post::all();
            return response()->json([
                'posts' => $posts
            ],200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()],403);
        }
    }

    public function deletePost(Request $request, $post_id){
        try {
            $post = Post::find($post_id);
            $post->delete();
            return response()->json([
                'message' => 'post deleted successfully'
            ],200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()],403);

        }
    }
}
