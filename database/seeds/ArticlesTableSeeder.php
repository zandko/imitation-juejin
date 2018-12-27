<?php

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\Lable;
use App\Models\User;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = User::all()->pluck('id')->toArray();
        $user_name = User::all()->pluck('name')->toArray();
        $category_ids = Category::all()->pluck('id')->toArray();
        $lable_ids = Lable::all()->pluck('id')->toArray();

        $faker = app(Faker\Generator::class);

        $articles = factory(Article::class)
            ->times(500)
            ->make()
            ->each(function ($article, $index) use ($user_ids, $user_name,$category_ids, $lable_ids, $faker) {
                $article->user_id = $faker->randomElement($user_ids);
                $article->user_name = $faker->randomElement($user_name);
                $article->category_id = $faker->randomElement($category_ids);
                $article->lable_id = $faker->randomElement($lable_ids);
            });

        Article::insert($articles->toArray());
    }
}
