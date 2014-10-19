<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Document;
use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\SiteBundle\PostType\Theme;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends BaseController {

	/**
	 * @Template("OutlandishAcadOowpBundle:Theme:themeIndex.html.twig")
	 */
	public function indexAction(Request $request) {
		$response = array();
		$post = $this->querySingle( array( 'page_id' => Theme::postTypeParentId() ) );

		$response['post'] = $post;

		return $response;
	}

	/**
	 * @Template("OutlandishAcadOowpBundle:Theme:themePost.html.twig")
	 */
	public function singleAction($name) {
		$response = array();
		$post = $this->querySingle(array('name' => $name, 'post_type' => Theme::postType()));

		$response['post'] = $post;

		return $response;
	}
}