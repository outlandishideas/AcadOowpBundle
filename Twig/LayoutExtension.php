<?php

namespace Outlandish\AcadOowpBundle\Twig;

class LayoutExtension extends \Twig_Extension {
	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('wp_head', array($this, 'getHead')),
		);
	}

	public function getHead()	{
		return wp_head();
	}

	public function getName() {
		return 'layout_extension';
	}
}