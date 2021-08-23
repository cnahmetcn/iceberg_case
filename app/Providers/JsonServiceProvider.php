<?php


namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class JsonServiceProvider
{
    /**
     * @param array $resource
     * @param int $code
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function success($resource = [], $code = Response::HTTP_OK)
    {
        return $this->putAdditionalMeta($resource, 'success')
            ->response()
            ->setStatusCode($code);
    }

    /**
     * @param array $resource
     * @param int $code
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function fail($resource = [], $code = 422)
    {
        return $this->putAdditionalMeta(["error" => $resource], 'fail')
            ->response()
            ->setStatusCode($code);
    }

    /**
     * @param string $resource
     * @param int $code
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function unauthorized($resource = "", $code = 401)
    {
        empty($resource) ? $resource = __("response.unauthorized") : '';
        return $this->putAdditionalMeta($resource, __("response.unauthorized"))
            ->response()
            ->setStatusCode($code);
    }

    /**
     * @param $resource
     * @param $status
     * @return JsonResource
     */
    private function putAdditionalMeta($resource, $status)
    {
        $meta = [
            "status" => $status,
            "execution_time" => number_format(microtime(true), 4)
        ];

        $merged = array_merge($resource->additional ?? [], $meta);

        if ($resource instanceof JsonResource) {
            return $resource->additional($merged);
        }

        if (is_array($resource)) {
            return (new JsonResource(collect($resource)))->additional($merged);
        }

        if (is_string($resource)) {
            return (new JsonResource(collect($resource)))->additional($merged);
        }

        throw new Exception('Invalid type of resource');
    }
}
