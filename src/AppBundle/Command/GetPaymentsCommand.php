<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPaymentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:get-payments')
            ->setDescription('Get payments.')
            ->setHelp('Get payments.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getManager()->getRepository('AppBundle:Payment');
        $payments = $repository->getUnsuccessfulEuroPayments();

        $table = new Table($output);
        $table
            ->setHeaders(array('Uid', 'User', 'Currency', 'Amount', 'UAH equivalent', 'Date'))
            ->setRows($payments);
        $table->render();
    }
}