<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\RoutemasterBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function defaultPostAction($slugs) {
		$slugBits = explode('/', trim($slugs, '/'));
		$post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);

		return array('post' => $post);
	}

	/**
	 * @Route("/")
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function frontPageAction() {
		$post = $this->querySingle(array('page_id' => get_option('page_on_front')));
		return array('post' => $post);
	}

}