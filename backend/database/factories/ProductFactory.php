<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unsplashUrl = 'https://source.unsplash.com/random/800x600';

        $client = new Client();

        $imageContents = $client->get($unsplashUrl)->getBody()->getContents();
        $imageName = Str::random(10) . '.jpg';
        $imagePath = 'products/' . $imageName;

        Storage::disk('public')->put($imagePath, $imageContents);

        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'image' => $imagePath,
            'category_id' => Category::inRandomOrder()->first()->id
        ];
    }
}
