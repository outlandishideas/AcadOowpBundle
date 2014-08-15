<?php

namespace Outlandish\AcadOowpBundle\Command;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddAcfGroupCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command.
     */
    protected function configure() {
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
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $label = $input->getArgument('label');
        $name = $input->getOption('name');

        if (!$name) {
            $name = str_replace('', '_', $label);
        }

        $output->writeln('Adding ACF Group');

        $post = array(
            'ID'             => '',
              'post_name'      => 'acf_'.$name,
              'post_title'     => $label,
              'post_status'    => 'publish',
              'post_type'      => 'acf',
//              'post_author'    => [ <user ID> ] // The user ID number of the author. Default is the current user ID.
//              'ping_status'    => [ 'closed' | 'open' ] // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
//              'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
//              'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
//              'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
//              'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
//              'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
//              'guid'           => // Skip this and let Wordpress handle it, usually.
//              'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
//              'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
              'post_date'      => date('Y-m-d H:i:s'),
              'post_date_gmt'  => date('Y-m-d H:i:s')
//              'comment_status' => [ 'closed' | 'open' ] // Default is the option 'default_comment_status', or 'closed'.
//              'post_category'  => [ array(<category id>, ...) ] // Default empty.
//              'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
//              'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
//              'page_template'  => [ <string> ] // Requires name of template file, eg template.php. Default empty.
        );

        $output->writeln(wp_insert_post($post));

    }



}