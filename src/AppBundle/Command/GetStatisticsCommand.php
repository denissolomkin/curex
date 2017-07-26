<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetStatisticsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:get-statistics')
            ->setDescription('Get statistics.')
            ->setHelp('Get statistics.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Payment');
        $statistics = $repository->getWeeklySuccessStatistics();

        $table = new Table($output);
        $table
            ->setHeaders(array('Day', 'UAH equivalent'))
            ->setRows($statistics);
        $table->render();

    }
}