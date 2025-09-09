<?php
declare(strict_types=1);

namespace Alikhani\Helper\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public static function instance(): Model;

    public function all(array $columns = ['*']): Collection;

    public function list(array $parameters, array $columns = ['*'], ?callable $tap = null): Builder|Collection|LengthAwarePaginator|array;

    public function query(): Builder;

    public function withQuery(callable $tap): static;

    public function clearQuery(): static;

    public function find(int|string $id, array $columns = ['*']): ?Model;

    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    public function findByField(string $field, mixed $value, array $columns = ['*']): ?Model;

    public function findOrFailByField(string $field, mixed $value, array $columns = ['*']): Model;

    public function store(array $parameters): Model;

    public function update(Model $model, array $parameters): Model;

    public function updateById(int|string $id, array $parameters): bool;

    public function destroy(Model $model): bool;

    public function destroyById(int|string $id): bool;
}
