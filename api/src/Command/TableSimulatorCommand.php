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

        $minTime = 5;
        $tableShake = 15;
        $autoGoal = 20;
        //$cardSwipe = 90;

        $nextTableShake = $startTime + $tableShake;
        $nextGoal = $startTime + $autoGoal;

        $currentTime = $startTime;
        $output->writeln('Command begin at: ' . date('h:i:s'));
        while ($currentTime < $endTime) {

            if ($currentTime >= $nextTableShake) {
                $this->sendEvent(EventRepository::TYPE_TABLE_SHAKE);
                $output->writeln('TableShake: ' . date('h:i:s', $currentTime));
                $nextTableShake = $currentTime + mt_rand($minTime, $tableShake);
            }
            if ($currentTime >= $nextGoal) {
                $this->sendEvent(EventRepository::TYPE_GOAL_AUTO);
                $output->writeln('AutoGoal: ' . date('h:i:s', $currentTime));
                $nextGoal = $currentTime + mt_rand($minTime, $autoGoal);
            }

            $currentTime = time();
        }
        $output->writeln('Command end at: ' . date('h:i:s'));
    }

    protected function sendEvent($eventName)
    {
        //TODO: send event to /api/v1/event
    }
}
 