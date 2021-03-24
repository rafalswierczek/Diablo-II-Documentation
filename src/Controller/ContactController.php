<?php declare(strict_types = 1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DB\ContactDB;

class ContactController extends AbstractController
{
    /**
     * @Route("/{_locale}/contact", name="contact", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index()
    {
        return $this->render("pages/contact/contact.html.twig");
    }

    /**
     * @Route("/{_locale}/contact/send", name="contactSend", requirements={"_locale" = "%d2d.locales%"})
     * @Security("is_granted('ROLE_USER')", statusCode=403, message="Access denied!")
     */
    public function send(Request $request, ContactDB $contactDB)
    {
        if($request->isMethod('POST'))
        {
            $messageText = $request->get('message');
            $result = $contactDB->createThread($messageText);
            if($result[0] === 'errors')
                foreach($result[1] as $error)
                    $this->addFlash('error', $error);
            else if($result[0] === 'success')
                $this->addFlash('success', $result[1]);
 
            return $this->redirectToRoute('contact');
        }
    }
}