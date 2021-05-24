<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Comment;
use App\Post;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // seleziono solo i post che sono pubblicati
        $posts = Post::where('published', 1)->get();
        
        //ciclo su ogni post dell'array posts
        foreach ($posts as $post) {
            
            // creo i commenti con un numero random da 1 a 3
            // se il numero è 0 non avrò un commento
            for ($i=0; $i < rand(0, 3); $i++) { 
                
                $newComment = new Comment();

                $newComment->post_id = $post->id;
                // se la colonna è nullable non aggiungo il nome
                if (rand(0, 1)) {
                    $newComment->name = $faker->name();
                }
                $newComment->content = $faker->text();

                $newComment->save();
            }
        }
    }
}
