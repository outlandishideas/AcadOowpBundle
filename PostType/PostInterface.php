<?php

namespace Outlandish\AcadOowpBundle\PostType;


interface PostInterface
{

    public static function postType();

    public function parent();

    public function title();

    public function permalink();

}