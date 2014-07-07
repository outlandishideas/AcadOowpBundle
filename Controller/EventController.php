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

        /** @var AcaSearch $search */

        //only future events fetched here
        $items = Event::fetchAll(
            array (
                'posts_per_page' => 10,
                'meta_query'=>array(
                    array(
                        'key'=>'start_date',
                        'value'=> date('Y/m/d'),
                        'compare'=>'>',
                        'type'=>'DATE'
                    )
                ),
                'orderby' => 'meta_value',
                'meta_key' => 'start_date',
                'order' => 'asc'
            )
        );

        //only past events fetched here
        $sideItems = array(
            array(
                'title' => 'Past events',
                'items' => Event::fetchAll(
                    array (
                        'posts_per_page' => 3,
                        'meta_query'=>array(
                            array(
                                'key'=>'end_date',
                                'value'=> date('Y/m/d'),
                                'compare'=>'<=',
                                'type'=>'DATE'
                            )
                        ),
                        'orderby' => 'meta_value',
                        'meta_key' => 'start_date',
                        'order' => 'desc'
                    )
                 )
            )
        );

        $response['post'] = $post;
        $response['items'] = $items;
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

        $response['post'] = $post;
        return $response;
    }

}