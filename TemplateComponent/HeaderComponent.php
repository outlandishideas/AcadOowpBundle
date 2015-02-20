<?php

namespace Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\OowpBundle\Helper\WordpressHelper;

/**
 * Class HeaderComponent
 * @package Outlandish\AcadOowpBundle\TemplateComponent
 */
class HeaderComponent implements TemplateComponent
{
    /**
     * @var WordpressHelper
     */
    private $wpHelper;

    /**
     * @param WordpressHelper $wpHelper
     */
    public function __construct(WordpressHelper $wpHelper)
    {

        $this->wpHelper = $wpHelper;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return [
            'headerImage' => $this->wpHelper->acfOption('header_image'),
            'headerText' => $this->wpHelper->acfOption('header_text')
        ];
    }
}