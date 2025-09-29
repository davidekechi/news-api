<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name'        => 'News API.org',
                'api_handler' => 'newsapiorg',
                'base_url'    => 'https://newsapi.org/v2',
            ],
            [
                'name'        => 'The Guardian',
                'api_handler' => 'guardian',
                'base_url'    => 'https://content.guardianapis.com',
            ],
            [
                'name'        => 'New York Times',
                'api_handler' => 'nytimes',
                'base_url'    => 'https://api.nytimes.com/svc',
            ],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate(
                ['api_handler' => $source['api_handler']],
                $source
            );
        }
    }
}
