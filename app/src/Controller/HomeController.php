<?php
/**
 * @author hR.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** HomeController */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="htcintranet_home")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('htcintranet_login');
    }
}
