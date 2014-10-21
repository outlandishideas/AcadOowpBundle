<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Place;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends ThemeController {

	/**
	 * @Template("OutlandishAcadOowpBundle:Project:index.html.twig")
	 */
	public function indexAction(Request $request) {
		$response = array();
		$post = $this->querySingle( array( 'page_id' => Project::postTypeParentId() ) );

		$response['post'] = $post;

		return $response;
	}

	/**
	 * @Template("OutlandishAcadOowpBundle:Project:post.html.twig")
	 */
	public function singleAction($name) {
		$response = array();
		$post = $this->querySingle(array('name' => $name, 'post_type' => Project::postType()));

		$response['post'] = $post;

		return $response;
	}


	public function postTypes()
	{
		return array(Project::postType());
	}
}