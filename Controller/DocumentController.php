<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\PostType\Document;
use Outlandish\AcadOowpBundle\Controller\ResourceController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class DocumentController extends BaseController {

    /**
     * @Route("/documents/", name="documentsIndex")
     * @Template("OutlandishAcadOowpBundle:Document:documentIndex.html.twig")
     */
    public function indexAction(Request $request)
    {
        $response = array();

        $post = $this->querySingle(array('page_id' => '3563'));

        $items = Document::fetchAll();

        $response['post'] = $post;
        $response['items'] = $items;
        $response['sections'] = $post->sections();

        return $response;
    }

    /**
     * @Route("/documents/{name}/", name="documentsPost")
     * @Template("OutlandishAcadOowpBundle:Document:documentPost.html.twig")
     */
    public function singleAction($name)
    {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => Document::postType()));

        $sideItems = array(
            $post->connectedPeople('Authors'),
            $post->connectedThemes(),
            $post->connectedDocuments(),
            $post->connectedEvents(),
            $post->connectedPlaces()
        );

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;
        $response['attachment'] = $post->attachment();

        return $response;
    }

}