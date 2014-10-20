<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Place;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class PlaceController extends Outlandish\AcadOowpBundle\Controller\ThemeController {

	/**
	 * @Template("OutlandishAcadOowpBundle:Place:index.html.twig")
	 */
	public function indexAction(Request $request) {
		$response = array();
		$post = $this->querySingle( array( 'page_id' => Place::postTypeParentId() ) );

		$response['post'] = $post;

		return $response;
	}

	/**
	 * @Template("OutlandishAcadOowpBundle:Place:post.html.twig")
	 */
	public function singleAction($name) {
		$response = array();
		$post = $this->querySingle(array('name' => $name, 'post_type' => Place::postType()));

		$response['post'] = $post;

		return $response;
	}


	public function postTypes()
	{
		return array(Place::postType());
	}
}