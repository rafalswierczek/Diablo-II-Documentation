<?php declare(strict_types=1);

namespace App\Service\DB\Application;

use App\Service\DB\DBService;

class MessagesDB extends DBService
{
    public function sendMessage(int $threadID, string $messageText): array
    {
        $translator = $this->getTranslator();
        $connection = $this->getConnection();

        try
        {
            $connection->beginTransaction();

            $stmt = $connection->prepare("INSERT INTO `Message` (`Text`, AddDate) VALUES (:text, :addDate)");
            $stmt->execute([
                ':text' => $messageText,
                ':addDate' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
            if($stmt->rowCount() !== 1)
            {
                $connection->rollBack();
                return ['errors', [$translator->trans('messages.error')]];
            }

            $messageID = $connection->lastInsertId();
            $userID = $this->getUser()->getID();

            $stmt = $connection->prepare("INSERT INTO `ThreadMessage` (ThreadID, MessageID, UserID) VALUES (:threadID, :messageID, :userID)");
            $stmt->execute([
                ':threadID' => $threadID,
                ':messageID' => $messageID,
                ':userID' => $userID
            ]);
            if($stmt->rowCount() !== 1)
            {
                $connection->rollBack();
                return ['errors', [$translator->trans('messages.error')]];
            }

            $connection->commit();

            return ['success', $translator->trans('messages.success')];
        }
        catch(\Exception $e)
        {
            $connection->rollBack();
            return ['errors', [$translator->trans('messages.error')]];
        }
    }
}