<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PersonController extends ThemeController {

    protected $class = "Outlandish\\SiteBundle\\PostType\\Person";

    /**
     * @param Request $request
     * @return array
     *
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
     * @param Request $request
     * @param mixed $name
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Person:post.html.twig")
     */
    public function singleAction(Request, $request, $name) {
        return parent::singleAction($request, $name);
    }

    protected function getIndexPageId()
    {
        return Person::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Person::postType());
    }

    public function postTypes()
    {
        return array(Person::postType());
    }


}