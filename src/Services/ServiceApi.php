<?php

namespace L0n3ly\LaravelRepositoryWithService\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use L0n3ly\LaravelRepositoryWithService\Contracts\BaseService;
use L0n3ly\LaravelRepositoryWithService\Traits\ResultService;

/**
 * @property mixed $mainRepository
 */
class ServiceApi implements BaseService
{
    use ResultService;

    protected string $title = '';

    protected string $create_message = 'created successfully';

    protected string $update_message = 'updated successfully';

    protected string $delete_message = 'deleted successfully';

    /**
     * find by id
     *
     * @return Model|ServiceApi|ResultService|null
     */
    public function find($id)
    {
        try {
            $result = $this->mainRepository->find($id);

            return $this->setData($result)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * find or fail by id
     *
     * @return ServiceApi|ResultService|mixed
     */
    public function findOrFail($id)
    {
        try {
            $result = $this->mainRepository->findOrFail($id);

            return $this->setData($result)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * all data
     *
     * @return Collection|ServiceApi|ResultService|null
     */
    public function all()
    {
        try {
            $result = $this->mainRepository->all();

            return $this->setData($result)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * create data
     *
     * @return Model|ServiceApi|ResultService|null
     */
    public function create($data)
    {
        try {
            $data = $this->mainRepository->create($data);

            return $this->setMessage($this->title . ' ' . $this->create_message)
                ->setCode(200)
                ->setData($data);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Update data
     *
     * @param  int|mixed  $id
     * @param  array|mixed  $data
     * @return bool|mixed
     */
    public function update($id, array $data)
    {
        try {
            $this->mainRepository->update($id, $data);

            return $this->setMessage($this->title . ' ' . $this->update_message)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * Delete data by id
     *
     * @param  int|Model  $id
     */
    public function delete($id)
    {
        try {
            $this->mainRepository->delete($id);

            return $this->setMessage($this->title . ' ' . $this->delete_message)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    /**
     * multiple delete
     *
     * @return mixed
     */
    public function destroy(array $id)
    {
        try {
            $this->mainRepository->destroy($id);

            return $this->setMessage($this->title . ' ' . $this->delete_message)
                ->setCode(200);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }
}
