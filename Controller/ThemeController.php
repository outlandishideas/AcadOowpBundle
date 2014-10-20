<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Theme;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends Outlandish\AcadOowpBundle\Controller\DefaultController {

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


	public function postTypes()
	{
		return array(Theme::postType());
	}
}