<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;


abstract class Post extends BasePost {

    /**
     * This is a new method
     * Created By: Hanna
     * Created On: 28/02/2014
     * Modified By: Hanna
     * Modified On: 29/02/2014
     * @return string
     */
    public function newMethod(){
        return "This is new";
    }
} 