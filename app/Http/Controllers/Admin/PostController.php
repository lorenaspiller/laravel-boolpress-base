<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{   
    // validation array
    protected $validation = [
        'date' => 'required|date',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        //mi passo i tags dal model
        $tags = Tag::all();

        return view('admin.posts.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validation = $this->validation;
        $validation['title'] = 'required|string|max:255|unique:posts';
        
        //validazione 
        $request->validate($validation);
        
        $data = $request->all();

        //passo true e false alla checkbox 
        $data['published'] = !isset($data['published']) ? 0 : 1;

        // salvo lo slug prima di fare l'assegnazione
        $data['slug'] = Str::slug($data['title'], '-');

        // upload file image
        if (isset($data['image'])) {
            $data['image'] = Storage::disk('public')->put('images', $data['image']);
        }


        // mass assignment
        $newPost = Post::create($data);

        //aggiungo i tags con attach() 
        if (isset($data['tags'])) {
            $newPost->tags()->attach($data['tags']);
        }
        

        //redirect a index
        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {   
        $tags = Tag::all();

        return view('admin.posts.edit', compact('post', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validation = $this->validation;
        $validation['title'] = 'required|string|max:255|unique:posts';

        //validazione 
        $request->validate($validation);

        $data = $request->all();

        //passo true e false alla checkbox 
        $data['published'] = !isset($data['published']) ? 0 : 1;

        // salvo lo slug prima di fare l'assegnazione
        $data['slug'] = Str::slug($data['title'], '-');


        // faccio update
        $post->update($data);

        //aggiorno i tags con il sync()
        if (!isset($data['tags'])) {
            $data['tags'] = [];
        }
        $post->tags()->sync($data['tags']);

        //return show
        return redirect()->route('admin.posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {   
        $post->delete();

        // redirect + toast 
        return redirect()->route('admin.posts.index')->with('message', 'Il post ' . $post->title . ' è stato ELIMINATO!');
    }
}
