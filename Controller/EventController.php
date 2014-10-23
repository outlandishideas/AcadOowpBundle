<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Event;
use Outlandish\SiteBundle\PostType\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EventController extends ResourceController {

    /**
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Event:index.html.twig")
     */
    public function indexAction(Request $request) {
        $request->query->set('meta_query', array(
            array(
                'key'=>'start_date',
                'value'=> date('Y/m/d'),
                'compare'=>'>',
                'type'=>'DATE'
            )
        ));
        $request->query->set('orderby', 'meta_value');
        $request->query->set('meta_key', 'start_date');
        $request->query->set('order', 'asc');
        $post = $this->querySingle(array(
            'page_id' => $this->getIndexPageId(),
            'post_type' => Page::postType()
        ));

        $items = Event::fetchFutureEvents();

        $pastItems = Event::fetchPastEvents();

        return array(
            'post' => $post,
            'items' => $items,
            'past_items' => $pastItems
        );
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