<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;

use Outlandish\AcadOowpBundle\PostType\Page;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends BaseController {

	/**
	 * @Route("/", name="home")
	 * @Template("OutlandishAcadOowpBundle:Page:pageFront.html.twig")
	 */
	public function frontPageAction()
    {
        /** @var Page $post */
		$post = $this->querySingle(array('page_id' => get_option('page_on_front')));
        $sections = $this->sections($post->sections());
        $returnArgs = array(
            'post' => $post,
            'sections' => $sections
        );
		return $returnArgs;
    }

    /**
     * @Route("about-isci/contact-us/", name="contact-us")
     * @Template("OutlandishAcadOowpBundle:Page:pageContact.html.twig")
     */
    public function contactPostAction() {
        /** @var Page $post */
        $post = $this->querySingle(array('page_id' => Page::CONTACT_PAGE_ID));

        $response['post'] = $post;
        $response['map']  = $post->contactMap();
        $response['address'] = get_field('address', 'options');
        $response['contact_people'] = $post->contactPeople();

        return $response;
    }

}