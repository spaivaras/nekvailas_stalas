<?php

namespace Controllers;

use Services\OpenApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OpenApiController
{
    /**
     * @var int
     */
    private $dayInTS = 86400;

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
        $response = [];

        $lastRows = (int)$request->query->get('rows', 0);
        $fromRecord = (int)$request->query->get('from-id', 0);

        $fromTs = (int)$request->query->get('from-ts', 0);
        $tillTs = (int)$request->query->get('till-ts', 0);


        if (count($request->query->all()) ==0) {
            $response = [
                "status" => "ok",
                "message" => "Api is up. There are parameters: rows - last records 1 to 100 or rows count; " .
                    "from-id - record id from which get rows; If missed returns last records; " .
                    "from-ts, till-ts -  unix timestamp from and till which get rows. Can be used with parameters 'from-id' and 'rows'. " .
                    "If missed 'till-ts' then it will be set to 24h after 'from-ts'"
            ];

        } elseif ($lastRows < 1 || $lastRows > 100) {
            $response = ["status"  => "error",
                "message" => "Bad request. Parameter 'rows' must be between 1 and 100"
            ];

        } elseif ($fromRecord < 0) {
            $response = ["status"  => "error",
                "message" => "Bad request. Parameter from-id must be greater then 0"
            ];

        } elseif ($tillTs < $fromTs && $tillTs != 0) {
            $response = ["status"  => "error",
                "message" => "Bad request. Parameter 'till-ts' must be equal or greater than 'from-ts'"
            ];

        } elseif ($fromTs < 0 || $tillTs < 0) {
            $response = ["status"  => "error",
                "message" => "Bad request. Parameter 'from-ts' must be greater than 0. " .
                    "If you specify 'till-ts' than it must be equal or greater than 'from-ts'"
            ];
        }

        if (count($response) == 0) {
            if ($fromTs != 0 && $tillTs == 0) {
                $tillTs = ($fromTs + $this->dayInTS);  // +24h
            }

            $data = $this->openApiService->getLastRows($lastRows, $fromRecord, $fromTs, $tillTs);
            $response = ["status" => "ok", "records" => $data];
        }

        return new JsonResponse($response, 200);
    }
}
