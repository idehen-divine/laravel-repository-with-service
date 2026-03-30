<?php

namespace L0n3ly\LaravelRepositoryWithService\Traits;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

trait ResultService
{
    private $data = null;

    private $message = null;

    private $code = null;

    private $errors = null;

    /**
     * @deprecated version
     */
    private $status = null;

    /**
     * set status
     *
     * @deprecated version
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * get status
     *
     * @deprecated version
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * set result output
     *
     * @deprecated version
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->data = $result;

        return $this;
    }

    /**
     * get result
     *
     * @deprecated version
     *
     * @return null
     */
    public function getResult()
    {
        return $this->data;
    }

    /**
     * set data output
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * get data
     *
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * set message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * get message
     *
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * set code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * get code
     *
     * @return null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * set errors
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->errors = $error;

        return $this;
    }

    /**
     * get errors
     *
     * @return array
     */
    public function getError()
    {
        return $this->errors;
    }

    /**
     * Exception Response
     *
     * @return ResultService
     */
    public function exceptionResponse(Exception $exception)
    {
        if ($exception instanceof QueryException) {
            if ($exception->errorInfo[1] == 1451) {
                return $this->setMessage('This data cannot be removed because it is still in use.')
                    ->setCode(400);
            }
        }
        if ($exception instanceof ModelNotFoundException) {
            if (! request()->expectsJson()) {
                return abort(404);
            }

            return $this->setMessage('Data not found')
                ->setCode(404);
        }
        if (config('app.debug')) {
            $message = (object) [
                'exception' => 'Error',
                'error_message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];

            return $this->setError($message)
                ->setMessage('internal server error')
                ->setCode(500);
        }

        return $this->setMessage('internal server error')
            ->setCode(500);
    }

    /**
     * response to json
     *
     * @return JsonResponse
     */
    public function toJson()
    {
        if (is_null($this->getCode())) {
            $http_code = 200;
        } else {
            $http_code = $this->getCode();
        }

        if ($this->status !== null) {
            return response()->json([
                'success' => $this->getStatus(),
                'code' => $http_code,
                'message' => $this->getMessage(),
                'data' => $this->getData(),
                'errors' => $this->getError(),
            ], $http_code);
        } else {
            return response()->json(array_filter([
                'code' => $http_code,
                'message' => $this->getMessage(),
                'data' => $this->getData(),
                'errors' => $this->getError(),
            ]), $http_code);
        }
    }
}
