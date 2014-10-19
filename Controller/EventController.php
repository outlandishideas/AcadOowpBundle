<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\ResourceController as BaseController;

use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EventController extends BaseController {

    /**
     *
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Event:eventIndex.html.twig")
     */
    public function indexAction(Request $request) {
        $response = array();

        $post = $this->querySingle(array('page_id' => Event::postTypeParentId()));

        //fetch future events and sort by month
        $items = Event::fetchFutureEvents();
//        $itemsByMonth = Event::sortByMonth($items);

        //only past events fetched here
        $previousItems = array(
            array(
                'title' => 'Previous events',
                'items' => Event::fetchPastEvents()
            )
        );

        $response['post'] = $post;
        $response['items'] = $items;
        $response['sections'] = $post->sections();
        $response['previousItems'] = $previousItems;
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @Template("OutlandishAcadOowpBundle:Event:eventIndex.html.twig")
     */
    public function previousAction(Request $request) {
        $response = array();

        $post = $this->querySingle(array('page_id' => Event::PREVIOUS_EVENTS_PAGE_ID));

        $items = Event::fetchPastEvents();

        $response['post'] = $post;
        $response['items'] = $items;
        return $response;
    }

    /**
     * @Route("/events/{name}/", name="eventPost")
     * @Template("OutlandishAcadOowpBundle:Event:eventPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => Event::postType()));

        $response['post'] = $post;
        $response['event_latitude'] =  $post->latitude();
        $response['event_longitude'] = $post->longitude();

        return $response;
    }

}