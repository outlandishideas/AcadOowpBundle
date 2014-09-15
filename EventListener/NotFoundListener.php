<?php

namespace Outlandish\AcadOowpBundle\EventListener;

use Outlandish\OowpBundle\PostType\FakePost;
use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundListener {

	/** @var PhpEngine */
	private $templateEngine;

	function __construct($templateEngine = null) {
		$this->templateEngine = $templateEngine;
	}

	public function onKernelException(GetResponseForExceptionEvent $event) {
		if ($event->getException() instanceof NotFoundHttpException) {

			// replace the global post with a fake one, and show a 404 template
			global $post, $wp_query;
			$post = new FakePost();
            $wp_query->post = $post;
            $wp_query->is_404 = true;

            $content = $this->templateEngine->render('OutlandishAcadOowpBundle:Default:404.html.twig', array('post'=> $post));

            $event->setResponse(new Response($content));
		}
	}
}