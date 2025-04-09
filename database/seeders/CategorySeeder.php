<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryKeyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default categories for uncategorized transactions
        $category = Category::create([
            'name' => 'Default',
            'slug' => Str::slug('Default'),
            'type' => 'expense',
            'is_default' => true,
        ]);

        
        $categories = [
            'expense' => [
                'Food' => ['wolt', 'foodpanda', 'burger king', 'mcdonalds', 'pizza hut', 'kfc', 'coop', 'auchan', 'lidl', 'aldi', 'spar', 'tesco'],
                'Transport' => ['public transport', 'train', 'taxi', 'uber', 'bolt', 'mass transit'],
                'Shopping' => ['clothes', 'clothing'],
                'Utilities' => ['electricity', 'water', 'gas', 'bill', 'payment'],
                'Entertainment' => ['cinema', 'netflix', 'disney', 'hbo max', 'apple tv', 'skyshowtime'],
                'Health' => ['pharmacy', 'medicine', 'doctor', 'health'],
                'Subscriptions' => ['spotify', 'youtube music', 'apple podcasts', 'tidal'],
                'Programming' => ['github', 'gitlab', 'bitbucket', 'heroku', 'vercel', 'netlify', 'cloudflare', 'aws', 'azure', 'digitalocean', 'linode', 'vps', 'chatgpt', 'copilot', 'cursor', 'rackforest'],
                'Car' => ['omv', 'shell', 'petrol', 'diesel']
            ],
            'income' => [
                'Income' => ['salary', 'wage'],
            ]
        ];

        foreach ($categories as $type => $subCategories ) {
            foreach ($subCategories as $name => $keywords) {
                $category = Category::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'type' => $type,
                ]);

                foreach ($keywords as $keyword) {
                    CategoryKeyword::firstOrCreate([
                        'category_id' => $category->id,
                        'keyword' => $keyword,
                    ]);
                }
            }
        }

    }
}
