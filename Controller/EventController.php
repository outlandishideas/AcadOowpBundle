<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\AcaSearch\AcaSearch;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Outlandish\SiteBundle\PostType\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EventController extends BaseController {

    /**
     * @Route("/events/", name="eventsIndex")
     * @Template("OutlandishAcadOowpBundle:Event:eventIndex.html.twig")
     */
    public function indexAction() {
        $response = array();

        $post = $this->querySingle(array('page_id' => Event::postTypeParentId()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        /** @var AcaSearch $search */

        $items = Event::fetchAll();

        //todo: fetch only past events here
        $sideItems = array(
            array(
                'title' => 'Past events',
                'items' => Event::fetchAll(array('posts_per_page' => 3))
            )
        );

        $response['post'] = $post;
        $response['items'] = $items;
        //todo: fetch only future events here
        $response['sections'] = $post->sections();
        $response['sideItems'] = $sideItems;
        return $response;
    }

    /**
     * @Route("/events/{name}/", name="eventPost")
     * @Template("OutlandishAcadOowpBundle:Event:eventPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => Event::postType()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        $response['post'] = $post;
        return $response;
    }

}