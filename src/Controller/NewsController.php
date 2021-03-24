<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\News;
use App\Service\DB\NewsDB;

class NewsController extends AbstractController
{
    /**
     * @Route("/{_locale}/news", name="news", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index(Request $request)
    {
        $news = $this->getDoctrine()->getRepository(News::class)->findBy(['Lang' => $request->getLocale()], ['AddDate' => 'DESC']);
        return $this->render("pages/news/news.html.twig", ['news' => $news]);
    }

    /**
     * @Route("/{_locale}/news/add", name="newsAdd", requirements={"_locale" = "%d2d.locales%"})
     */
    public function add(Request $request, NewsDB $newsDB)
    {
        $data = $request->request->all();
        $data['lang'] = $request->getLocale();

        $result = $newsDB->addMessage($data);
        if($result[0] === 'errors')
            foreach($result[1] as $error)
                $this->addFlash('error', $error);
        else if($result[0] === 'success')
            $this->addFlash('success', $result[1]);
                
        return $this->redirectToRoute('news');
    }
}