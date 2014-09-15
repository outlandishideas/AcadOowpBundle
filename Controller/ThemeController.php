<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\PostType\Document;
use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\PostType\Theme;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends BaseController {

	/**
	 * @Route("/themes/")
	 * @Template("OutlandishAcadOowpBundle:Theme:themeIndex.html.twig")
	 */
	public function indexAction(Request $request) {
		$response = array();
		$post = $this->querySingle( array( 'page_id' => Theme::postTypeParentId() ) );

		$response['post'] = $post;

		return $response;
	}

	/**
	 * @Route("/themes/{name}/", name="theme")
	 * @Template("OutlandishAcadOowpBundle:Theme:themePost.html.twig")
	 */
	public function singleAction($name) {
		$response = array();
		$post = $this->querySingle(array('name' => $name, 'post_type' => Theme::postType()));

		$response['post'] = $post;

		return $response;
	}
}