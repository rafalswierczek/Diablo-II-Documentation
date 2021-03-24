<?php declare(strict_types=1);

namespace App\Service\DB\Application;

use App\Service\DB\DBService;
use App\Entity\Thread;
use App\Entity\User;

class ContactDB extends DBService
{
    public function createThread(string $messageText): array
    {
        $translator = $this->getTranslator();
        $connection = $this->getConnection();

        try
        {
            $connection->beginTransaction();

            $admin = $this->getEntityManager()->getRepository(User::class)->getUserByRole('ROLE_ADMIN');

            if($admin->getID() === $this->getUser()->getID()) return ['errors', [$translator->trans('contact.message.error.self')]];

            $thread = $this->getEntityManager()->getRepository(Thread::class)->getUniqueUserPair($admin->getID(), $this->getUser()->getID());
            if(!$thread)
            {
                $stmt = $connection->prepare("INSERT INTO `Thread` (User1ID, User2ID, AddDate, NewMessageDate) VALUES (:user1ID, :user2ID, :addDate, :newMessageDate)");
                $stmt->execute([
                    ':user1ID' => $this->getUser()->getID(),
                    ':user2ID' => $admin->getID(),
                    ':addDate' => (new \DateTime())->format('Y-m-d H:i:s'),
                    ':newMessageDate' => (new \DateTime())->format('Y-m-d H:i:s')
                ]);
                if($stmt->rowCount() !== 1)
                {
                    $connection->rollBack();
                    return ['errors', [$translator->trans('contact.message.error')]];
                }

                $threadID = $connection->lastInsertId();
            }
            else
                $threadID = $thread->getID();

            $stmt = $connection->prepare("INSERT INTO `Message` (`Text`, AddDate) VALUES (:text, :addDate)");
            $stmt->execute([
                ':text' => $messageText,
                ':addDate' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
            if($stmt->rowCount() !== 1)
            {
                $connection->rollBack();
                return ['errors', [$translator->trans('contact.message.error')]];
            }

            $messageID = $connection->lastInsertId();
            $userID = $this->getUser()->getID();

            $query = $connection->query("INSERT INTO `ThreadMessage` (ThreadID, MessageID, UserID) VALUES ($threadID, $messageID, $userID)");
            if($query->rowCount() !== 1)
            {
                $connection->rollBack();
                return ['errors', [$translator->trans('contact.message.error')]];
            }
            
            $connection->commit();

            return ['success', $translator->trans('contact.message.success')];
        }
        catch(\Exception $e)
        {
            $connection->rollBack();
            return ['errors', [$translator->trans('contact.message.error')]];
        }
    }
}