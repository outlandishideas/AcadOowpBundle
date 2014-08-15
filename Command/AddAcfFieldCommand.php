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

        $args  = array(
            'numberposts' => 1,
            'post_type'   => 'acf',
            'post_status' => array( 'draft', 'publish', 'private', 'pending', 'future', 'auto-draft', 'trash' ),
            'p' => $id
        );
        $acfs = get_posts( $args );
        if (!$acfs) {
            throw new \RuntimeException('ACF group not found');
        }
        $field_types = apply_filters('acf/registered_fields', array());

        $found = false;

        foreach ($field_types as $group) {

            if (array_key_exists($type, $group)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \RuntimeException('Invalid type');
        }



//        $output->writeln(print_r($field_types, true));



        wp_reset_postdata();
        $acf = $acfs[0];
        $metadata = get_post_meta( $acf->ID );
        $count = 0;
        foreach ($metadata as $key=>$value) {
            if (substr($key, 0, 6) == 'field_') {
                $count++;
            }
        }

        $key = 'field_' . uniqid();
        $newField = array(
            'key' => $key,
            'label' => $label,
            'name' => $name,
            'type' => $type,
            'instructions' => $instructions,
            'required' => 0,
            'conditional_logic' => array(
                    'status' => 0,
                    'rules' => array(
                        array(
                            'field' => null,
                            'operator' => '=='
                        )
                    ),
                    'allorany' => 'all'
                ),
            'order_no' => $count
        );
        //todo: type-specific arguments
        add_post_meta($acf->ID, $key, $newField);
        $output->writeln(print_r($newField, true));
    }

    protected function addField($id, $name) {
        $this->output->writeln('Adding ' . $name);
    }
}