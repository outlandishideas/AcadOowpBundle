<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NewsController extends ResourceController {

    /**
     * @Template("OutlandishAcadOowpBundle:News:index.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request) {
        $post = $this->querySingle(array('page_id' => News::postTypeParentId()));
        $response = $this->indexResponse($post, $request, $this->postTypes());
        return $response;
    }

    /**
     * @Template("OutlandishAcadOowpBundle:News:post.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => News::postType()));

        $sideItems = array(
            $post->connectedPeople('Authors'),
            $post->connectedThemes(),
            $post->connectedDocuments(),
            $post->connectedEvents(),
            $post->connectedPlaces(),
        );

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;
        return $response;
    }

    public function postTypes()
    {
        return array(News::postType());
    }

}