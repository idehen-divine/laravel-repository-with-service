<?php

namespace L0n3ly\LaravelRepositoryWithService\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface Repository
{
    /**
     * Find an item by id
     *
     * @param  mixed  $id
     * @return Model|null
     */
    public function find($id);

    /**
     * find or fail
     *
     * @param  mixed  $id
     * @return mixed
     */
    public function findOrFail($id);

    /**
     * Return all items
     *
     * @return Collection
     */
    public function all();

    /**
     * Create an item
     *
     * @param  array|mixed  $data
     * @return Model|null
     */
    public function create($data);

    /**
     * Update a model
     *
     * @param  int|mixed  $id
     * @param  array|mixed  $data
     * @return Model|null
     */
    public function update($id, array $data);

    /**
     * Delete a model
     *
     * @param  int|Model  $id
     */
    public function delete($id);

    /**
     * multiple delete
     *
     * @return mixed
     */
    public function destroy(array $id);

    /**
     * Update an existing model or create a new one
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Find an existing model or create a new one
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return Model
     */
    public function firstOrCreate(array $attributes, array $values = []);

    /**
     * Get a new query builder for the model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query();
}
