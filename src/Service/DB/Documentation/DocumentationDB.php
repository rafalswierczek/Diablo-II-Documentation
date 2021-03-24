<?php declare(strict_types=1);

namespace App\Service\DB\Documentation;

use App\Entity\Documentation\Documentation;
use App\Exception\InvalidQueryException;
use App\Service\DB\DBMiddleware;

class DocumentationDB extends DBMiddleware
{
    public function insertDocumentation(Documentation $documentation): void
    {
        $entityManager = $this->getEntityManager('documentation');
        $entityManager->persist($documentation);
        $entityManager->flush();
    }


    public function getData(int $documentationID, string $lang, string $path): ?array
    {
        $con = $this->getConnection();
        $lang = ucfirst($lang);

        try
        {
            switch($path)
            {
                case 'unique-armors':
                {
                    $query = $con->query("
                        SELECT 
                            ln_u.$lang as uniqueName,
                            ln_c.$lang as commonName,
                            ui.Level, ui.LevelReq,
                            ui.Invfile,
                            lp.En,
                            lp.Pl
                        FROM UniqueItems ui
                        INNER JOIN Common c ON c.Code = ui.CommonCode
                        INNER JOIN LangName ln_u ON ln_u.Code = ui.`Index`
                        INNER JOIN LangName ln_c ON ln_c.Code = c.Code
                        LEFT JOIN Properties p ON p.PropID = ui.PropID
                        LEFT JOIN LangProperties lp ON lp.ID = p.LangPropertiesID
                        WHERE
                            c.DocumentationID = $documentationID AND
                            ui.DocumentationID = $documentationID AND
                            ln_u.DocumentationID = $documentationID AND
                            ln_c.DocumentationID = $documentationID AND
                            p.DocumentationID = $documentationID
                    ");
                    return [$path, $query->fetchAll()];
                }
                case 'common-weapons':
                {
                    break;
                }
                case 'common-armor':
                {
                    break;
                }
            }

            return null;
        }
        catch(\Exception $e)
        {
            return null;
        }
    }

    public function getLanguages(int $docID)
    {
        $stmt = $this->connection->prepare("SELECT `Language` FROM DocumentationLanguages WHERE DocumentationID = ?");
        $stmt->execute([$docID]);
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param int $userID
     * 
     * @return int documentation number for specified user
     * @throws InvalidQueryException when fetch result is false
     */
    public function countDocumentationsByUser(int $userID): int
    {
        $stmt = $this->connection->prepare("SELECT count(*) FROM Documentation WHERE UserID = ?");
        $stmt->execute([$userID]);

        $result = $stmt->fetch(\PDO::FETCH_COLUMN);

        if($result === false)
            throw InvalidQueryException::unexpectedQueryResult("Cannot fetch created documentations number for user ID: $userID");

        return (int)$result;
    }

    public function docExists(string $docName): bool
    {
        $stmt = $this->connection->prepare("SELECT 1 FROM Documentation WHERE Name = ?");
        $stmt->execute([$docName]);

        $result = $stmt->fetch(\PDO::FETCH_COLUMN);

        return $result === false ? false : true;
    }

    // public function getContributors($documentationID)
    // {
    //     $this->connection = $this->getConnection();

    //     $query = $this->connection->query("SELECT `Name` FROM DocumentationContributors WHERE DocumentationID = $documentationID");
    //     return $query->fetchAll();
    // }

    public function deleteDocumentation(int $docID)
    {
        $stmt = $this->connection->prepare("CALL DeleteDocumentation(?)");
        $stmt->execute([$docID]);
        
        return $stmt->fetch();
    }

    public function createDocumentation(
        int $userID,
        string $docName,
        string $imagesPath,
        string $defaultLang,
        array $tblContentLang,
        array $commonWeaponsTable,
        array $commonArmorTable,
        array $miscTable,
        array $attackSpeedTable,
        array $uniqueItemsTable
    ): bool
    {
        $con = $this->connection;
        $images = scandir($imagesPath);

        try
        {
            $con->beginTransaction();

        // Documentation //
            $stmt = $con->prepare("INSERT INTO `Documentation` (UserID, `Name`, DefaultLanguage, AddDate) VALUES (:userID, :name, :defaultLang, :addDate)");
            $stmt->execute([
                ':userID' => $userID,
                ':name' => $docName,
                ':defaultLang' => $defaultLang,
                ':addDate' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
            if($stmt->rowCount() !== 1)
            {
                $con->rollBack();
                return false;
            }

            $documentationID = $con->lastInsertId();
            // Documentation //

        // DocumentationLanguage //
            $stmt = $con->prepare("INSERT INTO `DocumentationLanguages` (DocumentationID, `Language`) VALUES (:documentationID, :language)");
            $stmt->bindParam(':documentationID', $documentationID);
            foreach($tblContentLang as $lang => $value)
            {
                $stmt->bindParam(':language', $lang);
                $stmt->execute();
            }
            if($stmt->rowCount() !== 1)
            {
                $con->rollBack();
                return false;
            }
            // DocumentationLanguage //

        // LangName //
            $stmt = $con->prepare("INSERT INTO `LangName` (Code, DocumentationID, Pl, En) VALUES (:code, :documentationID, :pl, :en)");
            $langNames = $this->getSession()->get('d2d_langNames');
            $langNames = $this->getUniqueTableByColumnName($langNames, 'code');
            foreach($langNames as $row)
            {
                $stmt->execute([
                    ':code' => $row['code'],
                    ':documentationID' => $documentationID,
                    ':pl' => $row['pl'],
                    ':en' => $row['en']
                ]);
            }
            // LangName //

        // LangType //
            $stmt = $con->prepare("INSERT INTO `LangType` (`Type`, DocumentationID, Pl, En) VALUES (:type, :documentationID, :pl, :en)");
            $typeNames = $this->getSession()->get('d2d_typeNames');
            $typeNames = $this->getUniqueTableByColumnName($typeNames, 'type');
            foreach($typeNames as $row)
            {
                $stmt->execute([
                    ':type' => $row['type'],
                    ':documentationID' => $documentationID,
                    ':pl' => $row['pl'],
                    ':en' => $row['en']
                ]);
            }
            // LangType //

        // Common //
            $stmt = $con->prepare("INSERT INTO `Common` 
                (Code, DocumentationID, LangTypeType, `Level`, LevelReq, Gemsockets, Invfile, NightmareUpgrade, HellUpgrade, Quest) VALUES 
                (:code, :documentationID, :langTypeType, :level, :levelReq, :gemsockets, :invfile, :nightmareUpgrade, :hellUpgrade, :quest)
            ");
            foreach($commonWeaponsTable as $row)
                $this->insertCommon($stmt, $documentationID, $row, $imagesPath, $images);
            foreach($commonArmorTable as $row)
                $this->insertCommon($stmt, $documentationID, $row, $imagesPath, $images);
            foreach($miscTable as $row)
                $this->insertCommon($stmt, $documentationID, $row, $imagesPath, $images);
            // Common //

        // CommonWeapons //
            $stmt = $con->prepare("INSERT INTO `CommonWeapons` 
                (CommonCode, DocumentationID, OneHandMinDmg, OneHandMaxDmg, TwoHandMinDmg, TwoHandMaxDmg, MissileMinDmg, MissileMaxDmg, MinStack, MaxStack, Rangeadder, StrBonus, DexBonus, ReqStr, ReqDex, Durability, Nodurability, Wclass, 2handedwclass, Rank, Normal, Exceptional, Elite) VALUES 
                (:commonCode, :documentationID, :oneHandMinDmg, :oneHandMaxDmg, :twoHandMinDmg, :twoHandMaxDmg, :missileMinDmg, :missileMaxDmg, :minStack, :maxStack, :rangeadder, :strBonus, :dexBonus, :reqStr, :reqDex, :durability, :nodurability, :wclass, :2handedwclass, :rank, :normal, :exceptional, :elite)
            ");

            foreach($commonWeaponsTable as $row)
            {
                $stmt->execute([
                    ':commonCode' => $row['code'],
                    ':documentationID' => $documentationID,
                    ':oneHandMinDmg' => $row['mindam'],
                    ':oneHandMaxDmg' => $row['maxdam'],
                    ':twoHandMinDmg' => $row['2handmindam'],
                    ':twoHandMaxDmg' => $row['2handmaxdam'],
                    ':missileMinDmg' => $row['minmisdam'],
                    ':missileMaxDmg' => $row['maxmisdam'],
                    ':minStack' => $row['minstack'],
                    ':maxStack' => $row['maxstack'],
                    ':rangeadder' => $row['rangeadder'],
                    ':strBonus' => $row['StrBonus'],
                    ':dexBonus' => $row['DexBonus'],
                    ':reqStr' => $row['reqstr'],
                    ':reqDex' => $row['reqdex'],
                    ':durability' => $row['durability'],
                    ':nodurability' => $row['nodurability'],
                    ':wclass' => $row['wclass'],
                    ':2handedwclass' => $row['2handedwclass'],
                    ':rank' => $row['rank'],
                    ':normal' => $row['normcode'],
                    ':exceptional' => $row['ubercode'],
                    ':elite' => $row['ultracode']
                ]);
            }
            // CommonWeapons //
            
        // AttackSpeed //
            $stmt = $con->prepare("INSERT INTO `AttackSpeed` 
                (CommonWeaponsCode , DocumentationID, `Character`, Mode, WClass, AttackSpeed) VALUES 
                (:commonWeaponsCode , :documentationID, :character, :mode, :wClass, :attackSpeed)
            ");

            foreach($attackSpeedTable as $row)
            {
                $stmt->execute([
                    ':commonWeaponsCode' => $row['code'],
                    ':documentationID' => $documentationID,
                    ':character' => $row['char'],
                    ':mode' => $row['mode'],
                    ':wClass' => $row['wclass'],
                    ':attackSpeed' => $row['attackSpeed']
                ]);
            }

            // AttackSpeed //
        // CommonArmor //
            $stmt = $con->prepare("INSERT INTO `CommonArmor` 
                (CommonCode, DocumentationID, MinDmg, MaxDmg, MinAc, MaxAc, `Block`, Speed, StrBonus, DexBonus, ReqStr, Durability, Nodurability, Rank, Normal, Exceptional, Elite) VALUES 
                (:commonCode, :documentationID, :minDmg, :maxDmg, :minAc, :maxAc, :block, :speed, :strBonus, :dexBonus, :reqStr, :durability, :nodurability, :rank, :normal, :exceptional, :elite)
            ");

            foreach($commonArmorTable as $row)
            {
                $stmt->execute([
                    ':commonCode' => $row['code'],
                    ':documentationID' => $documentationID,
                    ':minDmg' => $row['mindam'],
                    ':maxDmg' => $row['maxdam'],
                    ':minAc' => $row['minac'],
                    ':maxAc' => $row['maxac'],
                    ':block' => $row['block'],
                    ':speed' => $row['speed'],
                    ':strBonus' => $row['StrBonus'],
                    ':dexBonus' => $row['DexBonus'],
                    ':reqStr' => $row['reqstr'],
                    ':durability' => $row['durability'],
                    ':nodurability' => $row['nodurability'],
                    ':rank' => $row['rank'],
                    ':normal' => $row['normcode'],
                    ':exceptional' => $row['ubercode'],
                    ':elite' => $row['ultracode']
                ]);
            }
            // CommonArmor //

        // CommonMisc //
            $stmt = $con->prepare("INSERT INTO `CommonMisc` 
                (CommonCode, DocumentationID, Speed, MinStack, MaxStack) VALUES 
                (:commonCode, :documentationID, :speed, :minStack, :maxStack)
            ");

            foreach($miscTable as $row)
            {
                $stmt->execute([
                    ':commonCode' => $row['code'],
                    ':documentationID' => $documentationID,
                    ':speed' => $row['speed'],
                    ':minStack' => $row['minstack'],
                    ':maxStack' => $row['maxstack']
                ]);
            }
            // CommonMisc //

        // UniqueItems //
            $stmt = $con->prepare("INSERT INTO `UniqueItems` 
                (DocumentationID, CommonCode, `Index`, Level, LevelReq, Invfile) VALUES 
                (:documentationID, :commonCode, :index, :level, :levelReq, :invfile)
            ");

            $langProperties = $this->getSession()->get('d2d_langProperties');
            $stmt_langProperties = $con->prepare("INSERT INTO `LangProperties` (Pl, En) VALUES (:pl, :en)");
            $stmt_uniqueItemsLangProperties = $con->prepare("INSERT INTO `UniqueItemsLangProperties` (UniqueItemsID, LangPropertiesID) VALUES (:uniqueItemsID, :langPropertiesID)");

            foreach($uniqueItemsTable as $row)
            {
                $imagePath = null;
                foreach($images as $image)
                {
                    if($row['invfile'] === pathinfo($image, PATHINFO_FILENAME))
                    {
                        $imagePath = "$imagesPath/$image";
                        break;
                    }
                }
                if(!$imagePath) throw new \Exception("Cannot find uploaded image for 'invfile' column value: ".$row['invfile']);

                $stmt->execute([
                    ':documentationID' => $documentationID,
                    ':commonCode' => $row['code'],
                    ':index' => $row['index'],
                    ':level' => $row['lvl'],
                    ':levelReq' => $row['lvl req'],
                    ':invfile' => $imagePath
                ]);

                $uniqueItemsID = $con->lastInsertId();

                foreach($langProperties as $langPropertiesRow)
                {
                    if($langPropertiesRow['uniqueItemId'] === $row['id'])
                    {
                        foreach($langPropertiesRow['propTranslations'] as $propTranslation)
                        {
                            $stmt_langProperties->execute([
                                ':pl' => $propTranslation['pl'] ?? null,
                                ':en' => $propTranslation['en'] ?? null
                            ]);
        
                            $langPropertiesID = $con->lastInsertId();
        
                            $stmt_uniqueItemsLangProperties->execute([
                                ':uniqueItemsID' => $uniqueItemsID,
                                ':langPropertiesID' => $langPropertiesID
                            ]);
                        }
                    }
                }
            }
            // UniqueItems //
            
            $con->commit();
        }
        catch(\Exception $e)
        {
            $con->rollBack();
            return false;
        }

        return true;
    }

    private function insertCommon($stmt, $documentationID, array $row, string $imagesPath, $images)
    {
        $imagePath = null;
        foreach($images as $image)
        {
            if($row['invfile'] === pathinfo($image, PATHINFO_FILENAME))
            {
                $imagePath = "$imagesPath/$image";
                break;
            }
        }
        if(!$imagePath) throw new \Exception("Cannot find uploaded image for 'invfile' column value: ".$row['invfile']);

        $stmt->execute([
            ':code' => $row['code'],
            ':documentationID' => $documentationID,
            ':langTypeType' => $row['type'],
            ':level' => $row['level'],
            ':levelReq' => $row['levelreq'],
            ':gemsockets' => $row['gemsockets'],
            ':invfile' => $imagePath,
            ':nightmareUpgrade' => $row['NightmareUpgrade'],
            ':hellUpgrade' => $row['HellUpgrade'],
            ':quest' => $row['quest']
        ]);
    }
}