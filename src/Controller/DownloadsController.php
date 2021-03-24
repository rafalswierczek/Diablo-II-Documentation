<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class DownloadsController extends AbstractController
{
    /**
     * @Route("/{_locale}/downloads", name="downloads", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index()
    {
        return $this->render("pages/downloads/downloads.html.twig");
    }

    /**
     * @Route("/downloads/{fileName}", name="download")
     */
    public function download($fileName)
    {
        $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT']."/build/downloads/$fileName");

        $response = new Response($fileContent);
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}