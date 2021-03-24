<?php declare(strict_types = 1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Thread;
use App\Entity\Message;
use App\Entity\User;
use App\Service\DB\MessagesDB;

class MessagesController extends AbstractController
{
    /**
     * @Route("/{_locale}/messages", name="messages", requirements={"_locale" = "%d2d.locales%"})
     * @Security("is_granted('ROLE_USER')", statusCode=403, message="Access denied!")
     */
    public function index(Request $request)
    {
        $threads = $this->getDoctrine()->getManager()->getRepository(Thread::class)->getThreadsByUserID($this->getUser()->getID());
        return $this->render("pages/messages/messages.html.twig", ['threads' => $threads]);
    }

    /**
     * @Route("/{_locale}/thread/{id}", name="threadPage", requirements={"_locale" = "%d2d.locales%"})
     * @Security("is_granted('ROLE_USER')", statusCode=403, message="Access denied!")
     */
    public function threadPage($id, Request $request, MessagesDB $messageDB)
    {
        $id = (int)$id;

        if($request->getMethod() === 'POST')
        {
            $messageText = $request->get('message');
            $result = $messageDB->sendMessage($id, $messageText);

            if($result[0] === 'errors')
                foreach($result[1] as $error)
                    $this->addFlash('error', $error);
            else if($result[0] === 'success')
                $this->addFlash('success', $result[1]);
 
            return $this->redirectToRoute('threadPage', ['id' => $id]);
        }

        $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->getMessagesByThread($id);
        if(!empty($messages))
        {
            $thread = $messages[0]->getThread();
            $receiverID = $thread->getUser1ID() === $this->getUser()->getID() ? $thread->getUser2ID() : $thread->getUser1ID();
            $receiverName = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['ID' => $receiverID])->getName();
        }
        return $this->render("pages/messages/threadPage.html.twig", ['messages' => $messages, 'receiverName' => $receiverName ?? null]);
    }
}