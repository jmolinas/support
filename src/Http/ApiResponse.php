<?php

namespace JMolinas\Support\Http;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Build Response
     *
     * @param array $response
     * @param string $code
     * @param string $header
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function buildResponse($response = null, int $code = 200, array $header = [])
    {
        $apiHeader = !empty($header) ?
            array_merge(['Content-Type' => 'application/vnd.api+json'], $header) :
            ['Content-Type' => 'application/vnd.api+json'];

        return JsonResponse::create($response, $code, $apiHeader);
    }

    /**
     * Json Paginate
     *
     * @param LengthAwarePaginator $collection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonPaginate($collection)
    {
        if (!$collection instanceof LengthAwarePaginator) {
            throw new \Exception('Collection must be instace of LengthAwarePaginator');
        }
        $header = [
            'X-Pagination-Total-Count' => $collection->total(),
            'X-Pagination-Page-Count' => $collection->lastPage(),
            'X-Pagination-Current-Page' => $collection->currentPage(),
            'X-Pagination-Per-Page' => $collection->perPage(),
            'X-Pagination-Next-Page' => $collection->nextPageUrl(),
            'X-Pagination-Prev-Page' => $collection->previousPageUrl()
        ];
        return $this->buildResponse($collection, Response::HTTP_OK, $header);
    }

    /**
     * Get Response
     *
     * @param mixed $message
     * @param mixed $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($message = null, $data = [])
    {
        $response = [
            'success' => true,
            'status' => Response::HTTP_OK,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }
        return $this->buildResponse($response, Response::HTTP_OK);
    }

    /**
     * Created Response
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function created($data = [])
    {
        $status = Response::HTTP_CREATED;
        $response = [
            'success' => true,
            'status' => $status,
            'message' => Response::$statusTexts[$status]
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return $this->buildResponse($response, $status);
    }

    /**
     * Accepted Response
     *
     * @param mixed $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function accepted($data = [])
    {
        $status = Response::HTTP_ACCEPTED;
        $response = [
            'success' => true,
            'status' => $status,
            'message' => Response::$statusTexts[$status]
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return $this->buildResponse($response, $status);
    }
}
