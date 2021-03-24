<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/{_locale}/info", name="info", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index()
    {
        return $this->render("pages/info/info.html.twig");
    }
}