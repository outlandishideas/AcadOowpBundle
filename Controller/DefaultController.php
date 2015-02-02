<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\OowpBundle\PostType\Post as BasePost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
     *
     * @param mixed $slugs
     * @return array
	 */
	public function defaultPostAction($slugs)
    {
		$post = $this->queryPost($slugs, 'any', true);
		return $this->render('OutlandishAcadOowpBundle:Default:post.html.twig', array(
            'post' => $post
        ));
	}

}