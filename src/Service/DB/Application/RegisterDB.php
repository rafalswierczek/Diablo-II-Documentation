<?php declare(strict_types=1);

namespace App\Service\DB\Application;

use App\Service\DB\DBMiddleware;
use App\Entity\User;

class RegisterDB extends DBMiddleware
{
    public function registerUser($data): bool
    {
        $connection = $this->getConnection('application');

        try
        {
            $connection->beginTransaction();
            
            if(false)
            {
                $connection->rollBack();
            }

            $userID = $connection->lastInsertId();

            if(false)
            {
                $connection->rollBack();
            }

            $token = bin2hex(random_bytes(5));
            $link = "?&token=$token&uid=$userID";
            $hash = password_hash($token, PASSWORD_BCRYPT);

            if(false)
            {
                $connection->rollBack();
            }
            
            $connection->commit();

            // try
            // {
            //     $message = (new \Swift_Message($translator->trans('confirmaccount.email.title')))
            //         ->setFrom('account@gsaproject.pl')
            //         ->setTo($data['email'])
            //         ->setBody($this->renderView('mail/confirmAccount'.ucfirst($request->getLocale()).'.html.twig', ['link' => $result[2], 'login' => $data['login'], 'name' => $data['name']]), 'text/html');

            //     $swiftMailer->send($message);
            // }
            // catch(\Exception $e)
            // {
            //     $this->addFlash('error', $translator->trans('confirmaccount.email.error'));
            // }

            return true;
        }
        catch(\Exception $e)
        {
            $connection->rollBack();
            return false;
        }
    }

    public function confirmAccount(int $userId): bool
    {
        $connection = $this->getConnection('application');

        try
        {
            $connection->beginTransaction();

            $connection->commit();
        }
        catch(\Exception $ex)
        {
            $connection->rollBack();
            return false;
        }

        return true;
    }
}