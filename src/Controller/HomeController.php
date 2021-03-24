<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{_locale}/home", name="home", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index()
    {
        return $this->render("pages/home/home.html.twig");
    }
}