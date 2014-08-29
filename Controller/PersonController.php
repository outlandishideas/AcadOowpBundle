<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\ThemeController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PersonController extends BaseController {

    /**
     * @Route("/people/")
     * @Template("OutlandishAcadOowpBundle:Person:personIndex.html.twig")
     */
    public function indexAction(Request $request) {
        $response = array();

        $post = $this->querySingle(array('page_id' => Person::postTypeParentId()));

        /** @var AcaSearch $search */
        //todo: meed to sort our faceted search first
//        $search = $this->container->get('aca.search');

		$roles = Role::fetchAll();
		if ( $roles && $roles->post_count > 0 ) {
			foreach ( $roles as &$role ) {
				$role->people = $role->connected( Person::postType() );
			}
		} else {
			$people            = Person::fetchAll();
			$response['people'] = $people;
		}

        $response['post'] = $post;
        $response['roles'] = $roles;

        return $response;
    }

    /**
     * @Route("/people/{name}/", name="person")
     * @Template("OutlandishAcadOowpBundle:Person:personPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

		$post = $this->querySingle(array('name' => $name, 'post_type' => Person::postType()));

        $sideItems = $post->allConnectedPosts();

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;

        return $response;
    }



}