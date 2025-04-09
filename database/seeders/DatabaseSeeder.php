<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $this->call([
            CategorySeeder::class,
            MappingProfileSeeder::class,
        ]);

        $numOfCategories = Category::count();

        for ($i = 0; $i < 1000; $i++) {
                Transaction::create([
                    'user_id' => 1,
                    'category_id' => rand(1, $numOfCategories),
                    'amount' => fake()->randomFloat(2, -1000, 1000),
                    'type' => fake()->randomElement(['income', 'expense']),
                    'transaction_title' => fake()->name(),
                    'description' => fake()->words(3, true),
                    'counterparty' => fake()->company(),
                    'transaction_date' => fake()->dateTimeBetween('-1 year', 'now'),
                    'source' => fake()->name(),
                    'reference_id' => fake()->uuid(),
                    'metadata' => '{}',
                    'comment' => fake()->words(3, true),
                    'card_number' => fake()->randomNumber(4, true) . 'xxxx' . fake()->randomNumber(4, true),
                ]);
        }
    }
}
