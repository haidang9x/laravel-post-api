<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as CustomValidator;
use App\Http\Resources\Post as PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use JWTAuth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            $arr = [
                'status' => 'error',
                'message' => 'Required input!',
                'data' => $validator->errors()
            ];
            return response()->json($arr, 200);
        }
        $user = Auth::user();
        $post = Post::create([
            'title' => $input['title'],
            'content' => $input['content'],
            'user_id' => $user->id
        ]);
        $arr = [
            'status' => 'success',
            'message' => "Success!",
            'data' => new PostResource($post)
        ];
        return response()->json($arr, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required'
        ]);
        $validator->after(function (CustomValidator $validators) use ($post) {
            if (
                Gate::denies('post-update', $post)
            ) {
                $validators->errors()->add(
                    'user_id',
                    'You are not owner!'
                );
            }
        });
        if ($validator->fails()) {
            $arr = [
                'status' => 'error',
                'message' => 'Error!',
                'data' => $validator->errors()
            ];
            return response()->json($arr, 200);
        }
        $post->title = $input['title'];
        $post->content = $input['content'];
        $post->save();
        $arr = [
            'status' => 'success',
            'message' => 'The post have updated!',
            'data' => new PostResource($post)
        ];
        return response()->json($arr, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        if (
            Gate::denies('post-delete', $post)
        ) {
            $arr = [
                'status' => 'error',
                'message' => '',
                'data' =>  new PostResource($post)
            ];
            return response()->json($arr, 200);
        }
        $post->delete();
        $arr = [
            'status' => 'success',
            'message' => 'the post have deleted!',
            'data' => [],
        ];
        return response()->json($arr, 200);
    }
}
