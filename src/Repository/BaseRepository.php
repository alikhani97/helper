<?php
declare(strict_types=1);

namespace Alikhani\Helper\Repository;

use Alikhani\Helper\Contracts\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BaseRepository implements BaseRepositoryInterface
{
    protected static string $entity = '';

    /** @var array<callable(Builder): Builder> */
    protected array $queryTaps = [];

    public function __get(string $name)
    {
        if ($name === 'model') {
            return static::instance();
        }
    }

    public static function instance(): Model
    {
        $entity = static::$entity;

        if ($entity === '' || !class_exists($entity)) {
            throw new Exception('Repository $entity (' . static::class . ') must be set to a valid Model FQCN.');
        }

        $instance = new $entity();

        if (!$instance instanceof Model) {
            throw new Exception("Class {$entity} must extend Illuminate\\Database\\Eloquent\\Model");
        }

        return $instance;
    }

    public function query(): Builder
    {
        $q = $this->model->newQuery()->select($this->defaultSelect());

        if ($with = $this->defaultWith()) {
            $q->with($with);
        }
        if ($withC = $this->defaultWithCount()) {
            $q->withCount($withC);
        }

        foreach ($this->defaultScopes() as $scope => $args) {
            $q->{$scope}(...(array)$args);
        }

        foreach ($this->queryTaps as $tap) {
            $q = $tap($q);
        }

        return $q;
    }

    public function withQuery(callable $tap): static
    {
        $this->queryTaps[] = $tap;
        return $this;
    }

    public function clearQuery(): static
    {
        $this->queryTaps = [];
        return $this;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function list(array $parameters, array $columns = ['*'], ?callable $tap = null): Builder|Collection|LengthAwarePaginator|array
    {
        $builder = $this->listQuery($this->query(), $parameters);
        $builder = $this->applyIncludes($builder, $parameters);
        $builder = $this->filterConditions($builder, $parameters);

        if ($tap) {
            $builder = $tap($builder) ?? $builder;
        }

        $builder = $this->sort($builder, $parameters);

        return $this->export($builder, $parameters, $columns);
    }

    protected function defaultWith(): array
    {
        return [];
    }

    protected function defaultWithCount(): array
    {
        return [];
    }

    protected function defaultSelect(): array
    {
        return ['*'];
    }

    protected function defaultScopes(): array
    {
        return [];
    }

    protected function listQuery(Builder $q, array $parameters): Builder
    {
        return $q;
    }

    protected function conditions(Builder $query): array
    {
        return [];
    }

    private function filterConditions(Builder $query, array $parameters): Builder
    {
        $conditions = $this->conditions($query);
        if (empty($parameters) || empty($conditions)) {
            return $query;
        }

        $commons = array_intersect(array_keys($parameters), array_keys($conditions));
        if (empty($commons)) {
            return $query;
        }

        foreach ($commons as $field) {
            $condition = $conditions[$field];
            $value = $parameters[$field];

            if (is_callable($condition)) {
                $query = $condition($query, $value) ?? $query;
                continue;
            }

            switch ($condition) {
                case 'like':
                    if ($value !== null && $value !== '') {
                        $query->where($field, 'like', "%{$value}%");
                    }
                    break;

                case 'in':
                    if (is_array($value) && !empty($value)) {
                        $query->whereIn($field, $value);
                    }
                    break;

                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween($field, [$value[0], $value[1]]);
                    }
                    break;

                case 'null':
                    $query->whereNull($field);
                    break;

                case 'not_null':
                    $query->whereNotNull($field);
                    break;

                default:
                    // default comparison (e.g. '=', '!=', '>', '<=')
                    if ($value === null) {
                        $query->whereNull($field);
                    } else {
                        $query->where($field, (string)$condition, $value);
                    }
            }
        }

        return $query;
    }

    protected function sort(Builder $query, array $parameters): Builder
    {
        $direction = strtolower((string)($parameters['sort_direction'] ?? 'desc'));
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $allowed = $this->sortableColumns();
        $sortBy = $parameters['sort_by'] ?? $this->model->getKeyName();
        $column = in_array($sortBy, $allowed, true) ? $sortBy : $this->model->getKeyName();

        return $query->orderBy($column, $direction);
    }

    protected function sortableColumns(): array
    {
        return [$this->model->getKeyName(), 'created_at'];
    }

    protected function export(Builder $query, array $parameters, array $columns = ['*']): Builder|Collection|LengthAwarePaginator|array
    {
        return match ($parameters['export'] ?? null) {
            'builder' => $query->select($columns),
            'collection' => $query->get($columns),
            'array' => $query->get($columns)->toArray(),
            default => $query->paginate(
                perPage: (int)($parameters['per_page'] ?? $this->model->getPerPage()),
                columns: $columns
            ),
        };
    }

    protected function applyIncludes(Builder $q, array $parameters): Builder
    {
        if (!empty($parameters['with'])) {
            $q->with((array)$parameters['with']);
        }
        if (!empty($parameters['with_count'])) {
            $q->withCount((array)$parameters['with_count']);
        }

        $usesSoftDeletes = in_array(
            'Illuminate\\Database\\Eloquent\\SoftDeletes',
            class_uses_recursive(get_class($this->model))
        );

        if ($usesSoftDeletes) {
            if (!empty($parameters['only_trashed'])) {
                $q->onlyTrashed();
            } elseif (!empty($parameters['with_trashed'])) {
                $q->withTrashed();
            }
        }

        return $q;
    }

    public function find(int|string $id, $columns = ['*']): ?Model
    {
        return $this->query()->find($id, $columns);
    }

    public function findOrFail(int|string $id, $columns = ['*']): Model
    {
        return $this->query()->findOrFail($id, $columns);
    }

    public function findByField(string $field, mixed $value, $columns = ['*']): ?Model
    {
        return $this->query()->where($field, $value)->first($columns);
    }

    public function findOrFailByField(string $field, mixed $value, $columns = ['*']): Model
    {
        return $this->query()->where($field, $value)->firstOrFail($columns);
    }

    public function store(array $parameters): Model
    {
        return $this->query()->create($parameters);
    }

    public function update(Model $model, array $parameters): Model
    {
        $model->update($parameters);
        return $model->refresh();
    }

    public function updateById(int|string $id, array $parameters): bool
    {
        $affected = $this->query()->whereKey($id)->update($parameters);

        if ($affected === 0) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), [$id]);
        }

        return $affected > 0;
    }

    public function destroy(Model $model): bool
    {
        return (bool)$model->delete();
    }

    public function destroyById(int|string $id): bool
    {
        return $this->query()->whereKey($id)->delete();
    }
}
