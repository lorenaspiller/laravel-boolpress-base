<?php

namespace App\Http\Controllers;
use App\Post;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {   
        // filtro e prendo i dati che mi servono dal database (ultimi 5 post)
        $posts = Post::where('published', 1)->orderBy('date', 'asc')->limit(5)->get();

        return view('guest.index', compact('posts'));
    }

    public function show($slug)
    {
        // prendo i dati dal db
        $post = Post::where('slug', $slug)->first();

        if ($post == null) {
            abort(404);
        }
        // restituisco la pagina del post
        return view('guest.show', compact('post'));
    }
}
