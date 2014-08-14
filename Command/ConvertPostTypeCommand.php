<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\OowpBundle\PostType\Post;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertPostTypeCommand
 * @package Outlandish\AcadOowpBundle\Command
 *
 * Renames a post type
 */
class ConvertPostTypeCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    protected $output;


    /**
     * Configures the current command
     * @input Old name of post type
     * @input New name of post type
     */
    protected function configure() {
        $this->setName('acadoowp:convert:post-type')
            ->addArgument('old', InputArgument::REQUIRED, 'Old post type')
            ->addArgument('new', InputArgument::REQUIRED, 'New post type')
            ->setDescription('Converts post types');
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

        $oldName = $input->getArgument('old');
        $newName = $input->getArgument('new');

        $output->writeln("Converting $oldName to $newName");

        $this->convertPosts($oldName, $newName);
        $this->convertAcf($oldName, $newName);
        $this->fixP2P($oldName, $newName);
    }

    protected function convertPosts($oldName, $newName) {
        $args  = array(
            'numberposts' => - 1,
            'post_type'   => $oldName,
            'post_status' => array( 'draft', 'publish', 'private', 'pending', 'future', 'auto-draft', 'trash' )
        );
        $posts = get_posts( $args );
        wp_reset_postdata();

        if ( count( $posts ) > 0 ) {
            foreach ( $posts as $post ) {
                $this->output->writeln('Converting ' . $post->ID . ' to ' . $newName);
                set_post_type( $post->ID, $newName );
            }
        }
    }

    protected function convertAcf($oldName, $newName) {
        $args  = array(
            'numberposts' => - 1,
            'post_type'   => 'acf',
            'post_status' => array( 'draft', 'publish', 'private', 'pending', 'future', 'auto-draft', 'trash' )
        );
        $acfs = get_posts( $args );

        if ( count( $acfs ) > 0 ) {

            foreach ( $acfs as $acf ) {

                $rules = get_post_meta( $acf->ID, 'rule' );

                foreach ( $rules as $rule ) {
                    if ( isset( $rule['param'] ) && isset( $rule['value'] ) && $rule['param'] == 'post_type' && $rule['value'] == $oldName ) {
                        $old_rule = $rule;
                        $rule['value'] = $newName;
                        update_post_meta( $acf->ID, 'rule', $rule, $old_rule );
                        $this->output->writeln('Updating ACF ' . $acf->post_name);
                    }
                }
            }
        }
    }

    protected function fixP2P() {
        /** @var $wpdb \wpdb */
        global $wpdb;
        $brokenP2p = $wpdb->get_results("SELECT j.*, f.post_type as from_type, t.post_type as to_type
            FROM {$wpdb->p2p} AS j
            INNER JOIN {$wpdb->posts} AS f ON j.p2p_from = f.id
            INNER JOIN {$wpdb->posts} AS t ON j.p2p_to = t.id
            WHERE j.p2p_type <> CONCAT(f.post_type, '_', t.post_type)
            AND j.p2p_type <> CONCAT(t.post_type, '_', f.post_type)");
        $update = "UPDATE {$wpdb->p2p} SET p2p_type = %s WHERE p2p_id = %d";
        /** @var PostManager $postManager */
        $postManager = $this->getContainer()->get('outlandish_oowp.post_manager');
        $postManager->init();
        foreach ($brokenP2p as $row) {
            /** @var Post $fromType */
            $fromType = $postManager->postTypeClass($row->from_type);
            $toType = $postManager->postTypeClass($row->to_type);
            if ($fromType && $toType) {
                $name = $fromType::getConnectionName($row->to_type);
                $wpdb->query($wpdb->prepare( $update, $name, $row->p2p_id ) );
                $this->output->writeln('Correcting p2p ' . $row->p2p_id);
            }
        }
    }
}