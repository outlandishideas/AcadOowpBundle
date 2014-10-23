<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EventController extends ResourceController {

    /**
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Event:index.html.twig")
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
        //todo: show events properly
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Event:index.html.twig")
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
     * @param Request $request
     * @param mixed $name
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Event:post.html.twig")
     */
    public function singleAction(Request $request, $name) {
        return parent::singleAction($request, $name);
    }

    protected function getIndexPageId()
    {
        return Event::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Event::postType());
    }

    public function postTypes()
    {
        return array(Event::postType());
    }

}