<?php declare(strict_types=1);

namespace App\Service\DB\Application;

use App\Service\DB\DBService;
use App\Entity\News;

class NewsDB extends DBService
{
    public function addMessage(array $data): array
    {
        $translator = $this->getTranslator();
        $validator = $this->getValidator();
        $connection = $this->getConnection();

        $news = new News();
        $news->setText($data['text']);
        $news->setLang($data['lang']);
        $news->setAddDate(new \DateTime());
        $constraintViolationList = $validator->validate($news);
        if($constraintViolationList->count() > 0) return ['errors', $this->translateValidationErrors($constraintViolationList)];

        $stmt = $connection->prepare("INSERT INTO `News` (`Text`, Lang, AddDate) VALUES (:text, :lang, :addDate)");
        $stmt->execute([
            ':text' => $news->getText(),
            ':lang' => $news->getLang(),
            ':addDate' => $news->getAddDate()->format('Y-m-d H:i:s')
        ]);
        if($stmt->rowCount() !== 1)
            return ['errors', [$translator->trans('news.error')]];

        return ['success', $translator->trans('news.success')];
    }
}