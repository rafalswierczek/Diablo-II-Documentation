<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Application\{User, UserRole};
use App\Service\DB\Application\RegisterDB;
use App\Service\Form\Type\UserType;

class RegisterController extends AbstractController
{
    /**
     * @Route("/{_locale}/register", name="register", requirements={"_locale" = "%d2d.locales%"})
     */
    public function register(Request $request, RegisterDB $registerDB)
    {
        if($request->isMethod('POST'))
        {
            $user = new User();

            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $documentationData = $form->getData();

                $result = $registerDB->registerUser($data, $request->getLocale()); 
                
                return $this->redirectToRoute('task_success');
            }
    
            return $this->render('task/new.html.twig', [
                'form' => $form->createView(),
            ]);
            
            $this->addFlash('success', 'Documentation has been created!');
            
            return $this->redirectToRoute('login');      
        }

        return $this->render('security/register.html.twig');
    }
}
