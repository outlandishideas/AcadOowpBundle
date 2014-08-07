<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\AcaSearch\AcaSearch;
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
    public function indexAction() {
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

        $response['post'] = $post;

        return $response;
    }



}