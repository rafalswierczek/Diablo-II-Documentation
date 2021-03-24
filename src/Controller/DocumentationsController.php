<?php declare(strict_types = 1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\DB\DocumentationDB;
use App\Service\Utils\NotificationHandler;
use App\Service\Form\DocumentationForm;
use App\Service\Documentation\DocumentationHandler;


class DocumentationsController extends AbstractController
{
    private $errors;

    /**
     * @Route("/{_locale}/documentations", name="documentations", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index(DocumentationDB $documentationDB)
    {
        //dump($documentationDB->deleteDocumentation(103));die;
        $documentations = $this->getDoctrine()->getRepository(Documentation::class)->findAll();

        foreach($documentations ?? [] as $documentation)
        {
            $logoPath = $this->getLogoPath($documentation->getName());
            $author = $documentation->getUser()->getName();

            $documentationsData[] = [
                'name' => $documentation->getName(),
                'author' => $author,
                'addDate' => $documentation->getAddDate()->format('d-m-Y'),
                'lastUpdate' => $documentation->getAddDate()->format('d-m-Y'),
                'logoPath' => $logoPath
            ];
        }

        return $this->render("pages/documentations/documentations.html.twig", ['documentationsData' => $documentationsData ?? null]);
    }

    /**
     * @Route("/{_locale}/documentations/{docName}", name="showDocumentation", requirements={"_locale" = "%d2d.locales%"})
    */
    public function showDocumentation($docName, Request $request, DocumentationDB $documentationDB)
    {
        $documentation = $this->getDoctrine()->getRepository(Documentation::class)->findOneBy(['Name' => $docName]);
        if($documentation)
        {
            $logoPath = $this->getLogoPath($documentation->getName());
            $languages = $documentationDB->getLanguages($documentation->getID());

            if(!empty($languages))
                if(in_array($request->getLocale(), $languages))
                    return $this->render('pages/documentations/documentation.html.twig', ['documentation' => $documentation, 'logoPath' => $logoPath]);
        }
        
        return $this->redirectToRoute('documentations');
    }

    /**
     * @Route("/{_locale}/documentation/create", name="createDocumentation", requirements={"_locale" = "%d2d.locales%"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_CREATOR')", statusCode=403, message="Access denied!")
     */
    public function create(Request $request, DocumentationHandler $documentationHandler)
    {
        if($request->getMethod() === 'POST')
        {
            if(!$documentationService->createDocumentation($request, $this->getUser()->getID()))
            {
                $this->addFlash('error', "Documentation couldn't be created!");

                return $this->redirectToRoute('createDocumentation');
            }
            else
            {
                $documentationForm = new DocumentationForm();

                $form = $this->createForm(TaskType::class, $documentationForm);
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid())
                {
                    $documentationData = $form->getData();

                    $documentationHandler->insertDocumentation($documentationData);
                    
                    return $this->redirectToRoute('task_success');
                }
        
                return $this->render('task/new.html.twig', [
                    'form' => $form->createView(),
                ]);
                
                $this->addFlash('success', 'Documentation has been created!');
                
                return $this->redirectToRoute('documentations');
            }
        }
        
        return $this->render("pages/documentations/create.html.twig");
    }

    private function getLogoPath(string $docName): ?string
    {
        $docName = str_replace(' ', '_', $docName);
        $logoPath = glob("build/documentations/$docName/$docName".'_logo.{png,jpg,jpeg,bmp}', GLOB_BRACE)[0] ?? null;

        return $logoPath ? "/build/documentations/$docName/".basename($logoPath) : null; // get absolute path
    }
}