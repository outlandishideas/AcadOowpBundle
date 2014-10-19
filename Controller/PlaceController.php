<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Document;
use Outlandish\RoutemasterBundle\Controller\BaseController;
use Outlandish\SiteBundle\PostType\Place;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;

class PlaceController extends BaseController {

	/**
	 * @Template("OutlandishAcadOowpBundle:Place:placeIndex.html.twig")
	 */
	public function indexAction() {
		$response = array();
		$post = $this->querySingle( array( 'page_id' => Place::postTypeParentId() ) );

		$response['post'] = $post;

		return $response;
	}

	/**
	 * @Template("OutlandishAcadOowpBundle:Place:placePost.html.twig")
	 */
	public function singleAction($name) {
		$response = array();
		$post = $this->querySingle(array('name' => $name, 'post_type' => Place::postType()));

		$response['post'] = $post;

		return $response;
	}
}