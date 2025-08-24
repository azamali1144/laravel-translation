<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contract\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepository implements TagRepositoryInterface
{
    public function findOrCreate(string $name): Tag
    {
        return Tag::firstOrCreate(['name' => $name]);
    }

    public function all()
    {
        return Tag::all();
    }

    public function paginate(int $perPage = 25): LengthAwarePaginator
    {
        return Tag::paginate($perPage);
    }
}
