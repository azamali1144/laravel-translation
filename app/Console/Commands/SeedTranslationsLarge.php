<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Locale;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Support\Str;

class SeedTranslationsLarge extends Command
{
    protected $signature = 'db:seed-translations-large {count=100000}';
    protected $description = 'Populate translations table with a large dataset for performance testing';

    public function handle()
    {
        $locales = Locale::whereIn('code', ['en','fr','es'])->get();
        if ($locales->isEmpty()) {
            Locale::insert([
                ['code' => 'en', 'name' => 'English', 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'fr', 'name' => 'French', 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'es', 'name' => 'Spanish', 'created_at' => now(), 'updated_at' => now()],
            ]);
            $locales = Locale::whereIn('code', ['en','fr','es'])->get();
        }

        $tagNames = ['mobile','web','desktop','admin','dashboard'];
        foreach ($tagNames as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
        $tags = Tag::all();

        $count = (int) $this->argument('count');
        $bar = $this->output->createProgressBar($count);

        foreach ($locales as $locale) {
            for ($i = 0; $i < intdiv($count, max(1, $locales->count())); $i++) {
                $key = 'sample.' . $locale->code . '.' . Str::random(8);
                Translation::create([
                    'locale_id' => $locale->id,
                    'key' => $key,
                    'content' => 'Sample content for ' . $key,
                ]);
                $bar->advance(1);
            }
            Translation::where('locale_id', $locale->id)
                ->inRandomOrder()
                ->limit(max(1, (int)($count / 20)))
                ->each(function ($t) use ($tags) {
                    $t->tags()->sync($tags->random(rand(1, 3))->pluck('id')->toArray(), false);
                });
        }

        $bar->finish();
        $this->info("\nSeed complete.");
    }
}
