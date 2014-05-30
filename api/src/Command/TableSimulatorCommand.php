<?php

/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.25
 * Time: 21.14
 */

namespace Command;

use Repositories\EventRepository;
use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TableSimulatorCommand extends Command
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('table:simulate');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $totalTime = 1 * 60;
        $startTime = time();
        $endTime = $startTime + $totalTime;

        $minTime = 1;
        $tableShake = 10;
        $autoGoal = 15;
        $cardSwipe = 15;

        $nextTableShake = $startTime + $tableShake;
        $nextGoal = $startTime + $autoGoal;
        $nextCardSwipe = $startTime + $cardSwipe;

        $currentTime = $startTime;
        $output->writeln('Command begin at: ' . date('h:i:s'));
        while ($currentTime < $endTime) {

            if ($currentTime >= $nextTableShake) {
                $res = $this->sendEvent(EventRepository::TYPE_TABLE_SHAKE, []);
                $output->writeln('<comment>TableShake:<comment> ' . date('h:i:s', $currentTime));
                $output->writeln($res);
                $nextTableShake = $currentTime + mt_rand($minTime, $tableShake);
            }
            if ($currentTime >= $nextGoal) {
                $res = $this->sendEvent(EventRepository::TYPE_GOAL_AUTO, ["team" => mt_rand(0, 1)]);
                $output->writeln('<info>AutoGoal:<info> ' . date('h:i:s', $currentTime));
                $output->writeln($res);
                $nextGoal = $currentTime + mt_rand($minTime, $autoGoal);
            }
            if ($currentTime >= $nextCardSwipe) {
                $res = $this->sendEvent(
                    EventRepository::TYPE_CARD_SWIPE,
                    ["team" => mt_rand(0, 1), "player" => mt_rand(0, 1), "card_id" => mt_rand(100, 999)]
                );
                $output->writeln('<question>CardSwipe:<question> ' . date('h:i:s', $currentTime));
                $output->writeln($res);
                $nextCardSwipe = $currentTime + mt_rand($minTime, $cardSwipe);
            }

            $currentTime = time();
        }
        $output->writeln('Command end at: ' . date('h:i:s'));
    }

    protected function sendEvent($eventName, $data)
    {
        $uri = '127.0.0.1/kickertable/api/v1/event';
        $handler = curl_init($uri);
        $parameters = json_encode(
            [
                [
                    "time" => ["sec" => time(), "usec" => 101010],
                    "type" => $eventName,
                    "data" => $data
                ]
            ]
        );

        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt(
            $handler,
            CURLOPT_HTTPHEADER,
            ['Content-type: application/json', 'Content-Length: ' . strlen($parameters)]
        );

        $response = curl_exec($handler);
        curl_close($handler);

        return "\t\t\t Request >>>  " . $parameters . PHP_EOL . "\t\t\t Response <<< " . $response;
    }
}
 