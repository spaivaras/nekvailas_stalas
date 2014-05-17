<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.26
 */

namespace Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

class PageController
{

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    protected $app;

    public function __construct($twigService, $app)
    {
        $this->twig = $twigService;
        $this->app = $app;
    }

    /**
     * Main page to say hello and show table status via json api
     *
     * @return string
     */
    public function index()
    {
        return $this->twig->render('index.twig');
    }

    /**
     * Get table status.
     *
     * returns table status:
     * status: ok
     * message: table free|table busy
     *
     * @return JsonResponse
     */
    public function status()
    {
        $idleTimeFrame = 50; // 50 sec
        $sql = "SELECT timeSec, type, data
            FROM kickertable
            WHERE timeSec > (SELECT MAX(timeSec) FROM kickertable WHERE type = 'TableReset')
            ORDER BY timeSec";
        $data = $this->app['db']->fetchAll($sql);

        if ($data && $data[count($data) - 1]['timeSec'] > time() - $idleTimeFrame) {
            $users = Yaml::parse(__DIR__ . "/users.yml");
            $returnDataEmpty = [];
            $goals = 0;
            $players = [
                0 => $users["0"],
                1 => $users["0"],
            ];
            $teams = [
                0 => [
                    'players' => $players,
                    'goals' => $goals
                ],
                1 => [
                    'players' => $players,
                    'goals' => $goals
                ]
            ];
            $returnDataEmpty['teams'] = $teams;

            $returnData = $returnDataEmpty;
            foreach ($data as $event) {
                // check for idle time frame gap
                // if so reset game
                // if ($event['timeSec'] < time() - $idleTimeFrame) {
                //     $returnData = $returnDataEmpty;
                // }

                $eventData = json_decode($event['data']);
                switch ($event['type']) {
                    case 'CardSwipe':
                        // if goals eq 10 - reset game
                        if ($returnData['teams'][$eventData->team]['goals'] >= 10 || $returnData['teams'][(1 - $eventData->team)]['goals'] >= 10) {
                            $returnData = $returnDataEmpty;
                            $this->app['db']->insert(
                                'kickertable',
                                [
                                    "timeSec" => $event['timeSec'] - 1,
                                    "usec" => 0,
                                    "type" => "TableReset",
                                    "data" => "[]"
                                ]
                            );
                        }
                        // check for dublicate users reset user id
                        if ($returnData['teams'][0]['players'][0] == $users[$eventData->card_id]) {
                            $returnData['teams'][0]['players'][0] = $users["0"];
                        }
                        if ($returnData['teams'][0]['players'][1] == $users[$eventData->card_id]) {
                            $returnData['teams'][0]['players'][1] = $users["0"];
                        }
                        if ($returnData['teams'][1]['players'][0] == $users[$eventData->card_id]) {
                            $returnData['teams'][1]['players'][0] = $users["0"];
                        }
                        if ($returnData['teams'][1]['players'][1] == $users[$eventData->card_id]) {
                            $returnData['teams'][1]['players'][1] = $users["0"];
                        }
                        // write user id
                        if (isset($users[$eventData->card_id])) {
                            $returnData['teams'][$eventData->team]['players'][$eventData->player] =
                                $users[$eventData->card_id];
                        } else {
                            $returnData['teams'][$eventData->team]['players'][$eventData->player] = $users["1"];
                        }
                        break;
                    case 'AutoGoal':
                        // if goals eq 10 - reset game
                        if ($returnData['teams'][$eventData->team]['goals'] >= 10 || $returnData['teams'][(1 - $eventData->team)]['goals'] >= 10) {
                            $returnData = $returnDataEmpty;
                            $this->app['db']->insert(
                                'kickertable',
                                [
                                    "timeSec" => $event['timeSec'] - 1,
                                    "usec" => 0,
                                    "type" => "TableReset",
                                    "data" => "[]"
                                ]
                            );
                        }
                        $returnData['teams'][$eventData->team]['goals'] += 1;
                        break;
                }
            }

            return new JsonResponse(["status" => "ok", "table" => "busy", "data" => $returnData]);
        }

        return new JsonResponse(["status" => "ok", "table" => "free"]);
    }
} 