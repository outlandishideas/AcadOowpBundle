<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\OowpBundle\PostType\Post as BasePost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends \Outlandish\RoutemasterBundle\Controller\BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
     *
     * @param mixed $slugs
     * @return array
	 * @Template("OutlandishAcadOowpBundle:Default:post.html.twig")
	 */
	public function defaultPostAction($slugs)
    {
		$slugBits = explode('/', trim($slugs, '/'));
        /** @var BasePost $post */
		$post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);

		return array(
            'post' => $post,
        );
	}

}