<?php


namespace Outlandish\SiteBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PersonController extends BaseController {

	/**
	 * @Route("/people/{name}/", name="person")
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function personAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => Person::postType()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        $response['post'] = $post;
		return $response;
	}

    /**
     * @Route("/people/", name="person-index")
     * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
     */
    public function personIndexAction() {
        $response = array();

        $post = $this->querySingle(array('page_id' => Person::postTypeParentId()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        $people = Person::fetchAll();

        $response['post'] = $post;
        $response['items'] = $people;
        return $response;
    }

}