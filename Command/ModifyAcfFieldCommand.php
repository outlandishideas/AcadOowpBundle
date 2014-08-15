<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModifyAcfFieldCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command.
     */
    protected function configure() {
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
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $id = $input->getArgument('id');
        $name = $input->getArgument('name');

        $output->writeln('Converting (' . $action . ') ACF ID=' . $id . ', name=' . $name);

        $newName = $input->getOption('new-name');
        if (!$newName) {
            throw new \RuntimeException('rename requires new name');
        }
        $newLabel = $input->getOption('new-label');

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

        $this->output->writeln('Renaming ' . $name . ' to ' . $newName);

//        wp_reset_postdata();
        $acf = $acfs[0];
        $metadata = get_post_meta( $acf->ID );
        foreach ($metadata as $key=>$value) {
            if (substr($key, 0, 6) != 'field_') {
                continue;
            }
            $acfArgs = unserialize($value[0]);
            if ($acfArgs['name'] == $name) {
                $acfArgs['name'] = $newName;
                if ($newLabel) {
                    $acfArgs['label'] = $newLabel;
                }
                update_post_meta( $acf->ID, $key, $acfArgs );
                break;
            }
        }

        /** @var WordpressHelper $wpHelper */
        $wpHelper = $this->getContainer()->get('outlandish_oowp.helper.wp');
        $db = $wpHelper->db();
        $db->query($db->prepare("UPDATE {$db->postmeta} SET meta_key = %s WHERE meta_key = %s", $newName, $name));
        $db->query($db->prepare("UPDATE {$db->postmeta} SET meta_key = %s WHERE meta_key = %s", '_' . $newName, '_' . $name));
    }
}