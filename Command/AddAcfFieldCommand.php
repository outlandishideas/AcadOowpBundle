<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddAcfFieldCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command.
     */
    protected function configure() {
        $this->setName('acadoowp:acf:addField')
            ->addArgument('id', InputArgument::REQUIRED, 'ACF group ID')
            ->addArgument('label', InputArgument::REQUIRED, 'ACF Field Label')
            ->addArgument('name', InputArgument::REQUIRED, 'ACF Field Name')
            ->addArgument('type', InputArgument::REQUIRED, 'ACF Field Type')
            ->addOption('instructions', null, InputOption::VALUE_OPTIONAL, 'Instructions for authors. Shown when submitting data')
            ->setDescription('Adding ACF field to group');
    }

    /**
     * Executes the current command.
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int     null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $id = $input->getArgument('id');
        $label = $input->getArgument('label');
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');
        $instructions = $input->getOption('instructions');

        $output->writeln('Adding ACF ID=' . $id . ', name=' . $name);

        $newField = $this->get('outlandish_acadoowps.acf')->addField($id, $label, $name, $type, $instructions);

        $output->writeln(print_r($newField, true));
    }

}