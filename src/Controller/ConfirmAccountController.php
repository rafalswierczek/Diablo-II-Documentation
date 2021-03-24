<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\ConfirmAccount;
use App\Service\DB\RegisterDB;

class ConfirmAccountController extends AbstractController
{
    /**
     * @Route("/{_locale}/confirm-account", name="confirm_account", requirements={"_locale" = "%d2d.locales%"})
     */
    public function index(Request $request, RegisterDB $registerDB, TranslatorInterface $translator)
    {
        if($request->isMethod('GET'))
        {
            $token = $request->get('token');
            $uid = $request->get('uid');
            $hash = $this->getDoctrine()->getManager()->getRepository(ConfirmAccount::class)->findOneBy(['UserID' => $uid])->getHash();
            if(password_verify($token, $hash))
            {
                $result = $registerDB->confirmAccount($uid);
                if($result)
                {
                    $this->addFlash('success', $translator->trans('confirmaccount.success'));
                    return $this->redirectToRoute('login');
                }
                else $this->addFlash('error', $translator->trans('confirmaccount.error'));
            }
            else
                $this->addFlash('error', $translator->trans('confirmaccount.error'));
        }

        return $this->render("pages/home/home.html.twig");
    }
}