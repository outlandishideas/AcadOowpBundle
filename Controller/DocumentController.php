<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class DocumentController extends ResourceController {

    /**
     * action for index
     *
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Document:index.html.twig")
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * @param mixed $slug
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Document:post.html.twig")
     */
    public function singleAction($slug)
    {
        $response = array();

        $post = $this->querySingle(array('name' => $slug, 'post_type' => Document::postType()));

        $sideItems = array(
            $post->connectedPeople('Authors'),
            $post->connectedThemes(),
            $post->connectedDocuments(),
            $post->connectedEvents(),
            $post->connectedPlaces()
        );

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;
        $response['documentUrl'] = $post->documentUrl();
        $response['attachment'] = $post->attachment();

        return $response;
    }
    protected function getIndexPageId()
    {
        return Document::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Document::postType());
    }

    public function postTypes()
    {
        return array(Document::postType());
    }
}