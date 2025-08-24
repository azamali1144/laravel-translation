<?php
namespace App\Repositories\Contract;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface
{
    public function findOrCreate(string $name): Tag;
    public function all(): Tag;
    public function paginate(int $perPage = 25): LengthAwarePaginator;
}
