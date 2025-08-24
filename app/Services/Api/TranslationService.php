<?php
namespace App\Services\Api;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contract\TranslationServiceInterface;
use App\Repositories\Contract\TranslationRepositoryInterface;

class TranslationService implements TranslationServiceInterface
{
    protected TranslationRepositoryInterface $repository;

    public function __construct(TranslationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Create or upsert by locale_code + key
    public function store(string $localeCode, string $key, ?string $content, array $tags = []): Translation
    {
        $locale = Locale::whereCode($localeCode)->firstOrFail();

        $trans = null;
        DB::transaction(function() use ($locale, $localeCode, $key, $content, $tags, &$trans) {
            $trans = $this->repository->upsertByLocaleAndKey($locale->id, $key, $content);

            if (isset($content)) {
                $this->repository->updateContent($trans, $content);
            }

            if (!empty($tags)) {
                $this->repository->syncTags($trans, $tags);
            }
        });

        return Translation::with($this->repository->withRelations())->findOrFail(
            $this->repository->whereLocaleAndKey($locale->id, $key)?->id ?? $trans->id
        );
    }

    public function list(array $filters, int $perPage = 25)
    {
        $query = Translation::with(['tags','locale']);

        if (!empty($filters['locale'])) {
            $locale = Locale::whereCode($filters['locale'])->first();
            if ($locale) {
                $query->where('locale_id', $locale->id);
            }
        }

        if (!empty($filters['key'])) {
            $query->where('key', 'like', '%' . $filters['key'] . '%');
        }

        if (!empty($filters['content'])) {
            $query->where('content', 'like', '%' . $filters['content'] . '%');
        }

        if (!empty($filters['tags'])) {
            $tags = explode(',', $filters['tags']);
            $query->whereHas('tags', function($q) use ($tags){
                $q->whereIn('name', $tags);
            });
        }

        return $this->repository->paginate($query, $perPage);
    }

    public function show(int $id): Translation
    {
        return $this->repository->findOrFail($id);
    }

    public function update(int $id, ?string $content, ?array $tags): Translation
    {
        $trans = Translation::findOrFail($id);

        DB::transaction(function() use ($trans, $content, $tags) {
            if (isset($content)) {
                $trans->content = $content;
                $trans->save();
            }

            if (!is_null($tags)) {
                $tagNames = $tags; // already array of strings
                $tagIds = [];
                foreach ($tagNames as $name) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => strtolower(trim($name))]);
                    $tagIds[] = $tag->id;
                }
                $trans->tags()->sync($tagIds);
            }
        });

        return $trans->load('tags','locale');
    }

    public function destroy(int $id): void
    {
        $this->repository->delete($id);
    }

    public function search(array $filters, int $perPage = 25)
    {
        $query = Translation::with(['tags','locale'])->query();

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function($qq) use ($q){
                $qq->where('key', 'like', "%$q%")
                   ->orWhere('content', 'like', "%$q%");
            });
        }

        if (!empty($filters['tags'])) {
            $tags = explode(',', $filters['tags']);
            $query->whereHas('tags', function($qq) use ($tags){
                $qq->whereIn('name', $tags);
            });
        }

        return $this->repository->paginate($query, $perPage);
    }
}
