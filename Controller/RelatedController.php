<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Theme;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class RelatedController extends Controller{

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
			'OutlandishAcadOowpBundle:Partial:items.html.twig',
			array( 'items' => $items )
		);

	}

} 