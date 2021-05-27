<?php

namespace App\Http\Controllers;
use App\Post;
use App\Comment;
use App\Tag;


use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {   
        // filtro e prendo i dati che mi servono dal database (ultimi 5 post)
        $posts = Post::where('published', 1)->orderBy('date', 'asc')->limit(5)->get();

        // prendo tutti i tags
        $tags = Tag::all();

        return view('guest.index', compact('posts', 'tags'));
    }

    public function show($slug)
    {
        // prendo i dati dal db
        $post = Post::where('slug', $slug)->first();

        //prendo tutti i tags
        $tags = Tag::all();


        if ($post == null) {
            abort(404);
        }
        // restituisco la pagina del post
        return view('guest.show', compact('post', 'tags'));
    }

    public function addComment(Request $request, Post $post)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'content' => 'required|string'
        ]);

        $newComment = new Comment();

        $newComment->name = $request->name;
        $newComment->content = $request->content;
        $newComment->post_id = $post->id;

        $newComment->save();

        return back();

    }

    public function filterTag($slug)
    {
        //prendo tutti i tags
        $tags = Tag::all();

        $tag = Tag::where('slug', $slug)->first();
        if ($tag == null) {
            abort(404);
        }

        $posts = $tag->posts()->where('published', 1)->get();

        return view('guest.index', compact('posts', 'tags'));

    }
}
