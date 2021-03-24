<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Handler;

use App\Service\Utils\Container;
use App\Service\Validation\DocumentationValidator;
use App\Service\DB\Documentation\DocumentationDB;
use App\Service\Documentation\Table\Tool\{TblReader, Hardcoded};
use App\Service\Documentation\Table\Handler\{
    AnimDataHandler,
    ItemsCommon,
    ItemsUnique
};

class DocumentationHandler
{
    private Container $container;
    private DocumentationValidator $documentationValidator;
    private DocumentationDB $documentationDB;
    private TblReader $tblReader;
    private Hardcoded $hardcoded;
    private ItemsCommon $itemsCommon;
    private ItemsUnique $itemsUnique;
    private AnimDataHandler $animDataHandler;

    public function __construct(
        Container $container,
        DocumentationValidator $documentationValidator,
        DocumentationDB $documentationDB,
        TblReader $tblReader,
        Hardcoded $hardcoded,
        ItemsCommon $itemsCommon,
        ItemsUnique $itemsUnique,
        AnimDataHandler $animDataHandler
    )
    {
        $this->container = $container;
        $this->documentationValidator = $documentationValidator;
        $this->documentationDB = $documentationDB;
        $this->tblReader = $tblReader;
        $this->hardcoded = $hardcoded;
        $this->itemsCommon = $itemsCommon;
        $this->itemsUnique = $itemsUnique;
        $this->animDataHandler = $animDataHandler;
    }

    public function createDocumentation(): bool
    {
        $docDirPublic = $this->parameterBagInterface->get('d2d.doc_dir_public');
        $docDirPrivate = $this->parameterBagInterface->get('d2d.doc_dir_private');
        $docLimit = (int)$this->parameterBagInterface->get('d2d.doc_limit');

        $languages = explode('|', $this->parameterBagInterface->get('d2d.locales'));

        $docName = trim($this->request->get('name') ?? '');

        if(empty($docName))
            $this->errorHandler->setError(['code' => 'documentationName.empty']);
        else if(!preg_match('/^[A-Za-z0-9][A-Za-z0-9 ]*$/', $docName)) // [letters or numbers] followed by [letters or numbers or spaces or nothing]
            $this->errorHandler->setError(['code' => 'documentationName.invalid']);
        else if($this->documentationDB->docExists($docName))
            $this->errorHandler->setError(['code' => 'documentationName.exists']);

        $defaultLang = $this->request->get('defaultLang');

        if(empty($defaultLang))
            $this->errorHandler->setError(['code' => 'defaultLang.empty']);
        else if(!in_array($defaultLang, $languages))
            $this->errorHandler->setError(['code' => 'defaultLang.invalid']);

        $logo = $this->request->files->get('logo');
        $this->documentationValidator->validateUploadedFileImage($logo);

        $this->documentationValidator->validateFile($this->request->files->get('images'), null, 5242880); // 5 MB
        $this->documentationValidator->validateFile($this->request->files->get('animdata'), 'AnimData.txt', 2097152); // 2 MB
        $this->documentationValidator->validateFile($this->request->files->get('monstats'), 'MonStats.txt', 1048576); // 1 MB
        $this->documentationValidator->validateFile($this->request->files->get('properties'), 'Properties.txt', 262144); // 256 KB
        $this->documentationValidator->validateFile($this->request->files->get('itemstatcost'), 'ItemStatCost.txt', 524288); // 512 KB
        $this->documentationValidator->validateFile($this->request->files->get('skills'), 'Skills.txt', 524288); // 512 KB
        $this->documentationValidator->validateFile($this->request->files->get('skilldesc'), 'SkillDesc.txt', 524288); // 512 KB
        $this->documentationValidator->validateFile($this->request->files->get('misc'), 'Misc.txt', 524288); // 512 KB
        $this->documentationValidator->validateFile($this->request->files->get('armor'), 'Armor.txt', 524288); // 512 KB
        $this->documentationValidator->validateFile($this->request->files->get('weapons'), 'Weapons.txt', 1048576); // 1 MB
        $this->documentationValidator->validateFile($this->request->files->get('uniqueitems'), 'UniqueItems.txt', 2097152); // 2 MB

        foreach($languages as $lang) // .tbl files
        {
            $string = $this->request->files->get('string'.strtoupper($lang));
            $expansionstring = $this->request->files->get('expansionstring'.strtoupper($lang));
            $patchstring = $this->request->files->get('patchstring'.strtoupper($lang));

            if(!empty($string) || !empty($expansionstring) || !empty($patchstring))
            {
                if(
                    $this->documentationValidator->validateFile($string, 'string.tbl', 5242880, $lang) &&
                    $this->documentationValidator->validateFile($expansionstring, 'expansionstring.tbl', 5242880, $lang) &&
                    $this->documentationValidator->validateFile($patchstring, 'patchstring.tbl', 5242880, $lang)
                )
                    $tblContentLang[$lang] = $this->tblReader->getTblContent($string->getRealPath(), $expansionstring->getRealPath(), $patchstring->getRealPath());
            }
        }

        try
        {
            if($this->documentationDB->countDocumentationsByUser($this->userId) >= $docLimit)
                $this->errorHandler->setError(['code' => 'documentationsLimit', 'params' => ['docLimit' => $docLimit]]);

            if(empty($tblContentLang))
                $this->errorHandler->setError(['code' => 'tblFIles.cannotGetContent']);
                
            if($this->errorHandler->hasErrors())
                return false;
            
            $docName_ = str_replace(' ', '_', $docName);
            $docDir = "$docDirPublic/$docName_";

            foreach($tblContentLang as $lang => $tblContent)
                $itemTypesLang[$lang] = $this->hardcoded->getItemTypes($tblContent, $lang);
           
            die('dochandler.php');
            $pathList['images'] = $this->request->files->get('images')->getPathName();
            $pathList['properties'] = $this->request->files->get('properties')->getRealPath();
            $pathList['itemstatcost'] = $this->request->files->get('itemstatcost')->getRealPath();
            $pathList['uniqueitems'] = $this->request->files->get('uniqueitems')->getRealPath();
            $pathList['animdata'] = $this->request->files->get('animdata')->getRealPath();
            $pathList['weapons'] = $this->request->files->get('weapons')->getRealPath();
            $pathList['armor'] = $this->request->files->get('armor')->getRealPath();
            $pathList['misc'] = $this->request->files->get('misc')->getRealPath();
            $pathList['skills'] = $this->request->files->get('skills')->getRealPath();
            $pathList['skilldesc'] = $this->request->files->get('skilldesc')->getRealPath();
            $pathList['monstats'] = $this->request->files->get('monstats')->getRealPath();
            $pathList['elemtypes'] = "";//$this->request->files->get('elemtypes')->getRealPath();

            $commonWeaponsTable = $this->itemsCommon->getCommonWeapons($pathList['weapons'], $tblContentLang, $itemTypesLang);

            // if(!empty($this->getErrors(true)['errors']))
            //     return false;

            $attackSpeedTable = $this->animData->getWeaponsAttackSpeed($pathList['animdata'], $commonWeaponsTable);
            $commonArmorTable = $this->itemsCommon->getCommonArmor($pathList['armor'], $tblContentLang, $itemTypesLang);
            $miscTable = $this->itemsCommon->getMisc($pathList['misc'], $tblContentLang, $itemTypesLang);
            $uniqueItemsTable = $this->itemsUnique->getUniqueItems($pathList, $tblContentLang);
            
            //if(!empty($this->getErrors(true)['errors']))
                //return false;

            ////// IMAGES //////
            try
            {
                if(!copy($logo->getPathName(), "$docDir/$docName_".'_logo.'.$logo->getClientOriginalExtension()))
                {
                    $this->deleteDocumentationDir($docDir);
                    return $this->setSessionError("Cannot copy documentation logo");
                }

                $zipArchive = new \ZipArchive();
                $result = $zipArchive->open($pathList['images']);

                if($result !== true)
                    return $this->setSessionError("Cannot extract images from .zip archive!");
            
                if(!is_dir("$docDir/images"))
                    mkdir("$docDir/images", 0777, true);

                for($fileN = 0; $fileN < $zipArchive->numFiles; $fileN++)
                {
                    $imageData = $zipArchive->statIndex($fileN);
                    $imageSource = $zipArchive->getFromIndex($fileN);

                    // if(!$this->validateImage($imageData['name'], $imageData['size'], 20971.52, $imageSource, '/^(inv|INV)[a-zA-Z0-9_]+\.(png|jpe?g|bmp|PNG|JPE?G|BMP)$/'))
                    // {
                    //     $zipArchive->close();
                    //     $this->deleteDocumentationDir($docDir);
                    //     return false;
                    // }
                    
                    // if(!copy("zip://".$pathList['images']."#".$imageData['name'], "$docDir/images/".strtolower($imageData['name'])))
                    // {
                    //     $zipArchive->close();
                    //     $this->deleteDocumentationDir($docDir);
                    //     return $this->setSessionError("Cannot extract image '{$imageData['name']}' from .zip archive.");
                    // }

                    $directoryInvfiles[] = pathinfo($imageData['name'], PATHINFO_FILENAME);
                }

                $zipArchive->close();

                $invfiles = [];
                $this->setInvfiles($invfiles, $commonWeaponsTable);
                $this->setInvfiles($invfiles, $commonArmorTable);
                $this->setInvfiles($invfiles, $miscTable);
                $this->setInvfiles($invfiles, $uniqueItemsTable);

                $invfiles = array_unique($invfiles);
                
                $missingImageNames = array_udiff($invfiles, $directoryInvfiles, fn($a, $b) => $a<=>$b);

                if(!empty($missingImageNames))
                {
                    $this->deleteDocumentationDir($docDir);
                    return $this->setSessionError("Found missing images in ".$this->request->files->get('images')->getClientOriginalName().". List of missing image names: ".implode(', ', $missingImageNames));
                }

                // foreach($entries as $entry) // delete all images that aren't used by any item
                // {
                //     if($entry === '.' || $entry === '..')
                //         continue;
                    
                //     if(!in_array(pathinfo($entry, PATHINFO_FILENAME), $invfiles))
                //         unlink("$docDir/images/$entry");
                // }

                // each item must have invfile so UniqueItems ~ ItemsCommon invfile relation must be implemented here
                $this->setUniqueItemsInvfiles($uniqueItemsTable, $commonWeaponsTable);
                $this->setUniqueItemsInvfiles($uniqueItemsTable, $commonArmorTable);
                $this->setUniqueItemsInvfiles($uniqueItemsTable, $miscTable);

                foreach($uniqueItemsTable as $row)
                    if(empty($row['invfile']))
                        $emptyUniqueItemsInvfiles[] = $row['invfile'];

                if(!empty($emptyUniqueItemsInvfiles))
                    $this->errorHandler->setError(['code' => 'uniqueItems.emptyInvfiles', 'params' => ['invfiles' => implode(', ', $emptyUniqueItemsInvfiles)]]);
            
            }
            catch(\Exception $e)
            {
                $this->deleteDocumentationDir($docDir);
                return $this->setSessionError("Error occurred during images processing!");
            }
            ////// IMAGES //////
            
            ////// DB //////
            if(!$this->documentationDB->createDocumentation(
                $this->userId,
                $docName,
                "$docDir/images",
                $defaultLang,
                $tblContentLang,
                $commonWeaponsTable,
                $commonArmorTable,
                $miscTable,
                $attackSpeedTable,
                $uniqueItemsTable
            ))
            {
                $this->deleteDocumentationDir($docDir);
                return $this->setSessionError('Cannot insert documentation in database!');
            }
            ////// DB //////

            ////// PRIVATE //////
            try
            {
                foreach($this->request->files as $file)
                    if($file) // if there are no uploaded .tbl files then don't move them because they don't exist
                        $file->move("$docDirPrivate/$docName_", $file->getClientOriginalName());

                return true;
            }
            catch(\Exception $e)
            {
                $this->deleteDocumentationDir($docDir);
                return $this->setSessionError("Unable to create documentation!");
            }
            /////// PRIVATE //////
        }
        catch(\Throwable $ex)
        {
            dump($ex);die;
            $this->logger->error($ex->getMessage());
            $this->errorHandler->setErrors(['errors' => ['code' => 'documentation.cannotCreate']]);

            return false;
        }
    }

    private function setUniqueItemsInvfiles(array &$itemsUnique, array $table)
    {
        foreach($itemsUnique as &$itemsUniqueRow)
            if(empty($itemsUniqueRow['invfile']))
                foreach($table as $tableRow)
                    if($itemsUniqueRow['code'] === $tableRow['code'])
                        $itemsUniqueRow['invfile'] = $tableRow['invfile'];
    }

    private function setInvfiles(array &$invfiles, array $table)
    {
        foreach($table as $row)
            if(!empty($row['invfile']))
                $invfiles[] = $row['invfile'];
    }

    private function deleteDocDirPublic(string $docPath): bool
    {
        try
        {
            $entries = scandir($docPath);

            foreach($entries as $entry)
            {
                if($entry === '.' || $entry === '..') continue;
                $result = is_dir("$docPath/$entry") ? $this->DeleteDocDirPublic("$docPath/$entry") : unlink("$docPath/$entry");

                if(!$result) return false;
            }

            return rmdir($docPath); // \DirectoryIterator locks iterated directory so rmdir returns throws warning that it cannot remove directory because it is not empty (but it's empty)
        }
        catch(\Exception $e){ return false;}
    }

    private function deleteDocumentationDir(string $docPath)
    {
        //if(!$this->deleteDocDirPublic($docPath))
            //$this->setErrors(['errors' => ["Cannot delete documentation folder!"]], true);
    }

    private function setSessionError(string $error): bool
    {
        //$this->setErrors(['errors' => [$error]], true);

        return false;
    }
}