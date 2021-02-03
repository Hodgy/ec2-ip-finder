<?php

namespace Hodgy\Ec2IpFinder\Command;

use Aws\Ec2\Ec2Client;
use Aws\Exception\CredentialsException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FindCommand extends Command
{
    protected static $defaultName = 'find';

    protected function configure()
    {
        $this->addArgument('env', InputArgument::REQUIRED, 'Which environment');
        $this->addArgument('role', InputArgument::REQUIRED, 'Which role');
        $this->addOption('region', 'r', InputOption::VALUE_OPTIONAL, 'Which region', 'eu-west-1');
        $this->setDescription("Find the private IP address of an EC2 instance using Environment and Role tags");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $environment = $this->getFullEnvironmentPath($input->getArgument('env'));
        $role = strtoupper($input->getArgument('role'));
        $region = $input->getOption('region');

        $ec2 = new Ec2Client([
            'region' => $region,
            'version' => '2016-11-15'
        ]);

        try {
            $results = $ec2->describeInstances([
                'Filters' => [
                    [
                        'Name' => 'tag:Role',
                        'Values' => [$role]
                    ],
                    [
                        'Name' => 'tag:Environment',
                        'Values' => [$environment]
                    ]
                ]
            ]);
        } catch (CredentialsException $exception) {
            $io->error("Unable to authenticate with AWS. Please ensure you have setup your credentials");
            return Command::FAILURE;
        } catch (Exception $exception) {
            $io->error("There was an error attempting to find an instance");
            return Command::FAILURE;
        }

        $instance = $results->toArray()['Reservations'][0]['Instances'][0] ?? null;

        if (empty($instance)) {
            $io->error("Unable to find instance for $environment environment with role $role");
            return Command::FAILURE;
        }

        $privateIpAddress = $instance['NetworkInterfaces'][0]['PrivateIpAddress'] ?? null;

        if (empty($privateIpAddress)) {
            $io->error("Unable to find private ip for instance in $environment environment with role $role");
            return Command::FAILURE;
        }

        $io->success($privateIpAddress);
        return Command::SUCCESS;
    }

    private function getFullEnvironmentPath(string $environment): string
    {
        $prefix = getenv('EIF_ENV_PREFIX');
        return $prefix . strtoupper($environment);
    }
}
