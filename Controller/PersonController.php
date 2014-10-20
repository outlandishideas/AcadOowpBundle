<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PersonController extends ThemeController {

    /**
     * @Template("OutlandishAcadOowpBundle:Person:index.html.twig")
     */
    public function indexAction(Request $request) {
        $response = array();
        $post = $this->querySingle(array('page_id' => Person::postTypeParentId()));
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
     * @Template("OutlandishAcadOowpBundle:Person:post.html.twig")
     */
    public function singleAction($name) {
        $response = array();

		$post = $this->querySingle(array('name' => $name, 'post_type' => Person::postType()));

        $sideItems = array (
            $post->connectedPeople(),
            $post->connectedEvents(),
            $post->connectedPlaces()
        );

        $bottomItems = array(
            $post->connectedDocuments(),
            $post->connectedNews()
        );

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;
        $response['bottomItems'] = $bottomItems;

        return $response;
    }


    public function postTypes()
    {
        return array(Person::postType());
    }


}