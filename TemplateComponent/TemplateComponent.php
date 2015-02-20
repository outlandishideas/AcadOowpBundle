<?php

namespace Outlandish\AcadOowpBundle\TemplateComponent;


interface TemplateComponent
{
    /**
     * Gets all the arguments managed by this component and returns them as an array
     *
     * @return array
     */
    public function getArguments();

}