<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\PostType\Theme;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\AcadOowpBundle\PostType\Person;


class RelatedController extends BaseController{

	private function unsetByValue( &$array, $values) {
		if ( ! is_array( $values ) ) {
			$values = array( $values );
		}
		foreach( $values as $value ) {
			if ( ( $key = array_search( $value, $array ) ) !== false ) {
				unset( $array[$key] );
			}
		}
	}
	public function renderResourcesAction( $post ) {
		$types = Theme::childTypes( false );
		$this->unsetByValue( $types, array( 'person', 'role', 'theme', 'place' ) );

		$connected_types = $post->connectedTypes( $types );

		$items = $post->connected( $connected_types );

		return $this->render(
			'OutlandishAcadOowpBundle:Default:items.html.twig',
			array( 'items' => $items )
		);

	}

} 