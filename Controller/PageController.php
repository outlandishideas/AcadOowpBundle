<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\SiteBundle\PostType\Page;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class PageController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class PageController extends DefaultController {

    /**
     * action for display contact us page
     *
     * @return array
     * @Template("OutlandishAcadOowpBundle:Page:pageContact.html.twig")
     */
    public function contactUsAction() {
        /** @var Page $post */
        $post = $this->querySingle(array('page_id' => Page::CONTACT_US_ID));

        $response['post'] = $post;
        $response['map']  = $post->contactMap();
        $response['address'] = get_field('address', 'options');
        $response['contact_people'] = $post->contactPeople();

        return $response;
    }

}