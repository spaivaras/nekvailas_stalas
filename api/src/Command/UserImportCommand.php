<?php

/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.27
 * Time: 21.25
 */
namespace Command;

use Models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserImportCommand extends Command
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('table:user:import')
            ->addArgument('full-path', InputArgument::REQUIRED, 'Full path to file');
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
        $path = $input->getArgument('full-path');
        if (!file_exists($path)) {
            $output->writeln('File not found: ' . $path);
            return;
        }

        $progress = new ProgressHelper();
        $progress->start($output);
        $row = 1;
        $handle = fopen($path, "r");
        if ($handle !== false) {
            $columnCount = 0;
            $keys = [];
            while (($data = fgetcsv($handle, 0, ";")) !== false) {
                if ($row === 1) {
                    $keys = $data;
                    $columnCount = count($data);
                } elseif ($columnCount == count($data)) {
                    $this->addUser(array_combine($keys, $data));
                }
                $row++;
                $progress->advance();
            }
            fclose($handle);
            $progress->finish();
        }
    }

    protected function addUser($data)
    {
        $user = new User();
        $fullName = explode(' ', $data['Darbuotojas']);
        $firstName = $fullName[0];
        $lastName = isset($fullName[1]) ? $fullName[1] : '';
        $user->assign(['userId' => $data['Intranet ID'], 'firstName' => $firstName, 'lastName' => $lastName]);
        //TODO: save in DB
    }
}
 