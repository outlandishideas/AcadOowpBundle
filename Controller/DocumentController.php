<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\PostType\Document;
use Outlandish\RoutemasterBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;

class DocumentController extends BaseController {

    const INDEX_PAGE_ID = 22;

    /**
     * @Route("/documents-index/", name="documents-index")
     * @Template
     */
    public function indexAction() {
        $post = $this->querySingle(array('page_id' => self::INDEX_PAGE_ID));

//        $slugBits = explode('/', trim($slugs, '/'));
//        $post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);

        return array(
            'documents' => Document::fetchAllHighlightedFirst(),
            'post' => $post
        );
    }

}