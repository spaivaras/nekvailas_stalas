<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

$app->get('/', function() use ($app) {
    return new Response("hi");
});

$app->get('/api/v1/events', function(){
    $data = [1 => "a", 2 => "b"];
    return new JsonResponse(["status" => "ok", "data" => $data]);
});

$app->post('/api/v1/event', function(Request $request) use ($app){
    if (!$data = $request->request->all()) {
        return new JsonResponse(["status" => "error", "message" => "bad request"], 400);
    }

    return new JsonResponse(["status" => "ok", "data" => $data]);
});