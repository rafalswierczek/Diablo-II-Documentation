<?php declare(strict_types=1);

namespace App\Service\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\Utils\NotificationHandler;

final class DocumentationValidator
{
    private NotificationHandler $notificationHandler;
    
    public function __construct(NotificationHandler $notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
    }
    
    /**
     * @param UploadedFile|null $file
     * @param string|null $expectedName Don't check if null
     * @param int|null $maxSize 3MB default. Don't check if null
     * 
     * @return bool valid(true)/invalid(false)
     */
    public function validateFile(?UploadedFile $file, ?string $expectedName = null, ?int $maxSize = 3145728): bool
    {
        if(empty($file))
        {
            $this->errorHandler->setError(['code' => 'file.empty', 'params' => ['fileName' => $expectedName ?? '']]);
            return false;
        }

        $fileName = $file->getClientOriginalName();
        $fileSize = filesize($file->getRealPath());

        if(!$file->isValid())
        {
            $this->errorHandler->setError(['code' => 'file.invalid', 'params' => ['fileName' => $fileName]]);
            return false;
        }

        if($expectedName && $fileName !== $expectedName)
        {
            $this->errorHandler->setError(['code' => 'file.invalidName', 'params' => ['fileName' => $fileName, 'expectedName' => $expectedName]]);
            return false;
        }

        if($maxSize && $fileSize > $maxSize)
        {
            $this->errorHandler->setError(['code' => 'file.oversized', 'params' => ['fileName' => $fileName, 'fileSize' => round($fileSize/1024/1024), 'maxSize' => round($maxSize/1024/1024)]]);
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $source
     * @param int $size
     * @param int|null $maxSize 512KB default. Don't check if null
     * @param string|null $nameRegex Don't check if null
     * 
     * @return bool valid(true)/invalid(false)
     */
    public function validateImage(string $name, string $source, int $size, ?int $maxSize = 524288, ?string $nameRegex = null): bool
    {
        if($maxSize && $size > $maxSize)
        {
            $this->errorHandler->setError(['code' => 'file.oversized', 'params' => ['fileName' => $name, 'fileSize' => round($size/1024/1024), 'maxSize' => round($maxSize/1024/1024)]]);
            return false;
        }

        if($nameRegex && !preg_match($nameRegex, $name))
        {
            $this->errorHandler->setError(['code' => 'file.invalidName.simple', 'params' => ['fileName' => $name]]);
            return false;
        }

        if(imagecreatefromstring($source) === false)
        {
            $this->errorHandler->setError(['code' => 'file.image.invalid', 'params' => ['fileName' => $name]]);
            return false;
        }

        return true;
    }
    
    /**
     * @param UploadedFile|null $file
     * @param int|null $maxSize 512KB default. Don't check if null
     * @param string|null $nameRegex Don't check if null
     * 
     * @return bool valid(true)/invalid(false)
     */
    public function validateUploadedFileImage(?UploadedFile $file, ?int $maxSize = 524288, string $nameRegex = '/^[a-zA-Z_]+\.(png|jpe?g|bmp|PNG|JPE?G|BMP)$/'): bool
    {
        $isValid = $this->validateFile($file, null, $maxSize);
        if(!$isValid)
            return false;

        $fileName = $file->getClientOriginalName();
        $source = file_get_contents($file->getPathName());

        if($nameRegex && !preg_match($nameRegex, $fileName))
        {
            $this->errorHandler->setError(['code' => 'file.invalidName.simple', 'params' => ['fileName' => $fileName]]);
            return false;
        }

        if(imagecreatefromstring($source) === false)
        {
            $this->errorHandler->setError(['code' => 'file.image.invalid', 'params' => ['fileName' => $fileName]]);
            return false;
        }
        
        return true;
    }
}