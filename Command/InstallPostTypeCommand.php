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
 * 1. Renames all posts of a class
 * 2. Renames all ACF connections that the posts have to use new post type name
 * 3. Renames any P2P connections that the posts have to use new post type name
 *
 */
class InstallPostTypeCommand extends ContainerAwareCommand {

    /** @var  OutputInterface */
    protected $output;

    /**
     * Configures the current command
     * @input Old name of post type
     * @input New name of post type
     */
    protected function configure() {
        $this->setName('acadoowp:install:post-type')
            ->addArgument('inherit', InputArgument::REQUIRED, 'Post Type to inherit from')
            ->addArgument('name', InputArgument::OPTIONAL, 'name of the new Post Type class')
            ->addArgument('friendlyName', InputArgument::OPTIONAL, 'friendly name of the new Post Type class')
            ->addArgument('friendlyNamePlural', InputArgument::OPTIONAL, 'friendly name plural of the new Post Type class')
            ->setDescription('Adds a new Post Type to @OutlandishSiteBundle that inherits from $inherit');
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

        $inherit = $input->getArgument('inherit');
        $name = $input->getArgument('name');
        $friendlyName = $input->getArgument('friendlyName');
        $friendlyNamePlural = $input->getArgument('friendlyNamePlural');

        //check to see that the SiteBundle exists. If not exit with error
        if(!$this->siteBundleExists()) return false;

        //find best fit for $inheirt PostType
        $inherit = $this->findPostType($inherit);

        //create PostType dir in @OutlandishSiteBundle if it doesn't exist
        //todo: mkdir PostTypes ...

        //if file does not exist

            //create file with $name name
            //todo: create file @OutlandishSiteBundle/PostTypes/$name

        //else if file inherits from $inherit
            //if class is abstract
                //make class not abstract
                //todo: remove abstract from file
                //todo: exit with success code and message
            //else
                //class already exists and is instantiated
                //todo: exit with success code and message
        //else
            //class already exists but is set up differently
            //todo: exit with error and message

        //open file and add contents
        //todo: open file created above

        //todo: create contents of file

        //todo: close file and save

        //check that class exists
        //todo: find out how we can check that the class exists as we expect it to

    }

    /**
     * checks to see whether @OutlandishSiteBundle exists
     * @return bool
     */
    protected function siteBundleExists()
    {
        //todo: check to see if site bundle exists return as bool
        return true;
    }

    protected function findPostType($postType)
    {
        //todo: return full reference to post type given the name above
        return "Outlandish/AcadOowpBundle/PostTypes/Post";
    }

}