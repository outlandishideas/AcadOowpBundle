<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Document;
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

        //order posts by publication date
        //NB posts with no publication date will not be shown
        $items = Document::fetchAll(
            array(
                'meta_key' => 'publication_date',
                'orderby' => 'meta_value',
                'order' => 'DESC'
            )
        );

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
        $response['documentUrl'] = $post->documentUrl();
        $response['attachment'] = $post->attachment();

        return $response;
    }

}