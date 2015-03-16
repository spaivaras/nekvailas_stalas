<?php

namespace Controllers;

use Services\OpenApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OpenApiController
{
    /**
     * @var OpenApiService
     */
    protected $openApiService;

    /**
     * @param OpenApiService $openApiService
     */
    public function __construct($openApiService)
    {
        $this->openApiService = $openApiService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $lastRows = (int)$request->query->get('rows', 0);
        $fromRecord = (int)$request->query->get('from-id', 0);

        $response = [
            "status"  => "ok",
            "message" => "Api is up. There are parameters: rows - last records 1 to 100 or rows count; " .
                "from-id - record id from which get rows; If missed returns last records;"
        ];

        if ($lastRows < 0 && $lastRows > 100) {
            $response = ["status"  => "error",
                         "message" => "Bad request. Parameter 'rows' must be between 1 and 100"
            ];
        }

        if ($fromRecord < 0) {
            $response = ["status"  => "error",
                         "message" => "Bad request. Parameter from-id must be greater then 0"
            ];
        }

        if ($lastRows != 0) {
            $data = $this->openApiService->getLastRows($lastRows, $fromRecord);
            $response = ["status" => "ok", "records" => $data];
        }

        return new JsonResponse($response, 200);
    }
}
