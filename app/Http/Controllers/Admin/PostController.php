<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class PostController extends Controller
{   
    // validation array
    protected $validation = [
        'date' => 'required|date',
        'content' => 'required|string',
        'image' => 'nullable|url'
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
        $request->validate($this->validation);
        
        $data = $request->all();

        //passo true e false alla checkbox 
        $data['published'] = !isset($data['published']) ? 0 : 1;

        // salvo lo slug prima di fare l'assegnazione
        $data['slug'] = Str::slug($data['title'], '-');


        // mass assignment
        $newPost = Post::create($data);

        //aggiungo i tags con attach()
        $newPost->tags()->attach($data['tags']);

        //redirect a index
        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validation = $this->validation;
        $validation['title'] = 'required|string|max:255|unique:posts';

        //validazione 
        $request->validate($this->validation);

        $data = $request->all();

        //passo true e false alla checkbox 
        $data['published'] = !isset($data['published']) ? 0 : 1;

        // salvo lo slug prima di fare l'assegnazione
        $data['slug'] = Str::slug($data['title'], '-');


        // faccio update
        $post->update($data);

        //aggiorno i tags con il sync()
        $post->tags()->sync($data['tags']);

        //return show
        return redirect()->route('admin.posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {   
        //cancello i tags correlati ai post in tabella pivot con il detach()
        $post->tags()->detach();

        $post->delete();

        // redirect + toast 
        return redirect()->route('admin.posts.index')->with('message', 'Il post ' . $post->title . ' è stato ELIMINATO!');
    }
}
