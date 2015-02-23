<?php

namespace Outlandish\AcadOowpBundle\Breadcrumb;


class Breadcrumb
{
    public $label;
    public $url;
    public $title;

    /**
     * @param string $label
     * @param string $url
     * @param string $title
     */
    public function __construct($label, $url, $title)
    {
        $this->label = $label;
        $this->url = $url;
        $this->title = $title;
    }
}