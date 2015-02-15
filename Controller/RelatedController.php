<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Theme;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class RelatedController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class RelatedController extends Controller
{
    /**
     * @param array $array
     * @param array|mixed $values
     */
    private function unsetByValue(&$array, $values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        foreach ($values as $value) {
            if (($key = array_search($value, $array)) !== false) {
                unset($array[$key]);
            }
        }
    }

    /**
     * @param OowpPost $post
     * @return mixed
     */
    public function renderResourcesAction(OowpPost $post)
    {
        $types = Theme::childTypes(false);
        $this->unsetByValue($types, array('person', 'role', 'theme', 'place'));
        $connectedTypes = $post->connectedTypes($types);
        $items = $post->connected($connectedTypes);

        return $this->render(
            'OutlandishAcadOowpBundle:Partial:items.html.twig',
            ['items' => $items]
        );
    }
} 