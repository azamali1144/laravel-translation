<?php
namespace App\Repositories\Eloquent;

use App\Models\Translation;
use App\Models\Locale;
use App\Models\Tag;
use App\Repositories\Contract\TranslationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function findById(int $id): Translation
    {
        return Translation::with(['tags','locale'])->findOrFail($id);
    }

    public function findOrFail(int $id): Translation
    {
        return Translation::with(['tags','locale'])->findOrFail($id);
    }

    public function whereLocaleAndKey(int $localeId, string $key): ?Translation
    {
        return Translation::whereLocaleId($localeId)->where('key', $key)->first();
    }

    public function upsertByLocaleAndKey(int $localeId, string $key, ?string $content): Translation
    {
        return Translation::firstOrCreate(
            ['locale_id' => $localeId, 'key' => $key],
            ['content' => $content ?? null]
        );
    }

    public function updateContent(Translation $trans, ?string $content): void
    {
        if (isset($content)) {
            $trans->content = $content;
            $trans->save();
        }
    }

    public function syncTags(Translation $trans, array $tagNames): void
    {
        if (!empty($tagNames)) {
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => strtolower(trim($name))]);
                $tagIds[] = $tag->id;
            }
            $trans->tags()->sync($tagIds);
        }
    }

    public function delete(int $id): void
    {
        Translation::findOrFail($id)->delete();
    }

    public function withRelations(): array
    {
        return ['tags', 'locale'];
    }

    public function paginate($query, int $perPage = 25)
    {
        return $query->paginate($perPage);
    }
}
