<?php

namespace L0n3ly\LaravelRepositoryWithService\Implementations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use L0n3ly\LaravelRepositoryWithService\Contracts\Repository;

/**
 * @property Model $model
 */
class Eloquent implements Repository
{
    /**
     * Find an item by id
     *
     * @param  mixed  $id
     * @return Model|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * find Or Fail
     *
     * @return mixed
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Return all items
     *
     * @return Collection|null
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Create an item
     *
     * @param  array|mixed  $data
     * @return Model|null
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a model
     *
     * @param  int|mixed  $id
     * @param  array|mixed  $data
     * @return Model|null
     */
    public function update($id, array $data)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * destroy many item with primary key
     *
     * @param  int|Model  $id
     */
    public function destroy(array $id)
    {
        return $this->model->destroy($id);
    }

    /**
     * delete item
     *
     * @param  Model|int  $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * Update an existing model or create a new one
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * Find an existing model or create a new one
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return Model
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /**
     * Get a new query builder for the model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->model->newQuery();
    }
}
