<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModifyAcfFieldCommand
 * @package Outlandish\AcadOowpBundle\Command
 */
class ModifyAcfFieldCommand extends ContainerAwareCommand
{

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('acadoowp:acf:modify')
            ->addArgument('id', InputArgument::REQUIRED, 'ACF group ID')
            ->addArgument('name', InputArgument::REQUIRED, 'ACF Field Name')
            ->addOption('new-name', null, InputOption::VALUE_OPTIONAL, 'New name')
            ->addOption('new-label', null, InputOption::VALUE_OPTIONAL, 'New label')
            ->setDescription('Modifies ACF field');
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
        $id = $input->getArgument('id');
        $name = $input->getArgument('name');
        $newName = $input->getOption('new-name');
        $newLabel = $input->getOption('new-label');

        $action = null;
        $output->writeln('Converting (' . $action . ') ACF ID=' . $id . ', name=' . $name);

        $this->get('outlandish_acadoowps.acf')->modifyField($id, $name, $newName, $newLabel);
    }
}