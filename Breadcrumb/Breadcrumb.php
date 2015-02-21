<?php

namespace Outlandish\AcadOowpBundle\Breadcrumb;


class Breadcrumb
{
    private $label;
    private $url;
    private $hover;

    /**
     * @param string $label
     * @param string $url
     * @param string $hover
     */
    public function __construct($label, $url, $hover)
    {
        $this->label = $label;
        $this->url = $url;
        $this->hover = $hover;
    }
}