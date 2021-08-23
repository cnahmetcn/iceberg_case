<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Providers\CurlServiceProvider;
use App\Providers\JsonServiceProvider;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected $response;
    protected $curlServiceProvider;
    protected $postCode;
    protected $postCodeApi;
    protected $googleMapApi;

    public function __construct()
    {
        $this->response = new JsonServiceProvider();
        $this->curlServiceProvider = new CurlServiceProvider();
        $this->postCode = $_ENV['POST_CODE'];
        $this->postCodeApi = $_ENV['POST_CODE_API'];
        $this->googleMapApi = $_ENV['GOOGLE_DISTANCE_API_KEY'];
    }

    protected function removePagination($data): array
    {
        $keysToRemove = ['per_page', 'path', 'last_page', 'last_page_url', 'links', 'next_page_url', 'from', 'first_page_url', 'to', 'current_page', 'total'];
        $count = $data['last_page'];
        foreach ($keysToRemove as $key) {
            unset($data[$key]);
        }
        return [$data['data'], $count];
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Support\MessageBag
     */

    protected function checkValidator(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $validator = $this->getValidationFactory()->make(
            $request->all(),
            $rules,
            $messages,
            $customAttributes
        );

        if ($validator->fails()) {
            return $validator->errors();
        }
    }
}
