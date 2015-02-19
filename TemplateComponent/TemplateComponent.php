<?php
/**
 * Created by PhpStorm.
 * User: outlander
 * Date: 19/02/2015
 * Time: 18:04
 */

namespace Outlandish\AcadOowpBundle\TemplateComponent;


interface TemplateComponent {

    /**
     * Gets all the arguments managed by this component and returns them as an array
     *
     * @return array
     */
    public function getArguments();

}