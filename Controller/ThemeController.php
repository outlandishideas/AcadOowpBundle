<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Theme;
use Outlandish\SiteBundle\PostType\Page;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends SearchController {

    protected $class = "Outlandish\\SiteBundle\\PostType\\Theme";

    /**
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Theme:index.html.twig")
     */
    public function indexAction(Request $request) {
        $post = $this->querySingle(array(
            'page_id' => $this->getIndexPageId(),
            'post_type' => Page::postType()
        ), true);

        $class = $this->getClass();
        $items = $class::fetchAll(array('orderby' => 'title', 'order' => 'ASC'))->posts;

        return array(
            'post' => $post,
            'items' => $items
        );
    }

    /**
     * @param Request $request
     * @param mixed $name
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Theme:post.html.twig")
     */
    public function singleAction(Request $request, $name) {
        $resources = parent::singleAction($request, $name);
        $class = $this->getClass();
        $request->query->set($class::postType(), array($resources['post']->ID));
        return array_merge($resources, $this->processSearch($request));
    }

    protected function getIndexPageId()
    {
        return Theme::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Theme::postType());
    }

    public function postTypes()
    {
        return array(Theme::postType());
    }

    public function getClass()
    {
        return $this->class;
    }
}