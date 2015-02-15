<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddAcfGroupCommand
 * @package Outlandish\AcadOowpBundle\Command
 */
class AddAcfGroupCommand extends ContainerAwareCommand
{

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('acadoowp:acf:addGroup')
            ->addArgument('label', InputArgument::REQUIRED, 'ACF Group Label')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Name of field.')
            ->setDescription('Adding ACF group');
    }

    /**
     * Executes the current command.
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $label = $input->getArgument('label');
        $name = $input->getOption('name');

        $this->get('outlandish_acadoowps.acf')->addGroup($label, $name);

        $output->writeln('Adding ACF Group');

        $output->writeln($this->get('outlandish_acadoowps.acf')->addGroup($label, $name));

    }



}