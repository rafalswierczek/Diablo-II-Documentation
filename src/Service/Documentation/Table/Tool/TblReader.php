<?php declare(strict_types=1);

namespace App\Service\Documentation\Table\Tool;

final class TblReader
{
	/**
	 * Returns string.tbl, expansionstring.tbl and patchstring.tbl content as array
	 * 
	 * @param string $stringPath
	 * @param string $expansionstringPath
	 * @param string $patchstringPath
	 * 
	 * @return array|null
	 */
	public function getTblContent(string $stringPath, string $expansionstringPath, string $patchstringPath): ?array
	{
		if(!is_uploaded_file($stringPath) || !is_uploaded_file($expansionstringPath) || !is_uploaded_file($patchstringPath))
			return null;

		$tempFileHandles = [
			['path' => $stringPath, 'pointer' => 'WarrivAct1IntroGossip1', 'fileName' => 'string.tbl'],
			['path' => $expansionstringPath, 'pointer' => 'A4Q2ExpansionSuccessTyrael', 'fileName' => 'expansionstring.tbl'],
			['path' => $patchstringPath, 'pointer' => 'Cutthroat1', 'fileName' => 'patchstring.tbl']
		];

		$rows = [];

		foreach($tempFileHandles as $tempFileHandle)
		{
			$tblData = file_get_contents($tempFileHandle['path']); 

			$fileLength = strlen($tblData);
			$newRow = false;
			$tmpRowData = "";
			$rowKey = '';
			
			if(($startPos = strpos($tblData, $tempFileHandle['pointer'])) !== false)
			{
				$rowsCount = count($rows);
				
				for($i = $startPos; $i < $fileLength; $i++) // foreach byte
				{
					$byte = $tblData[$i];

					if(unpack("c", $byte)[1] === 0)
					{
						if($newRow)
						{
							if(!empty(trim($rowKey)) && !empty(trim($tmpRowData)))
								$rows[$rowKey] = $tmpRowData;
						}
						else
						{
							if(strlen($tmpRowData) > 256)
							{
								$this->errorHandler->setError(['code' => 'file.tbl.invalidKey', 'params' => ['fileName' => $tempFileHandle['fileName'], 'key' => $tmpRowData]]);
								return null;
							}

							$rowKey = $tmpRowData;
						}
						
						$tmpRowData = '';
						$newRow = $newRow === true ? false : true;
					}
					else
						$tmpRowData .= $byte;
				}

				if(count($rows) === $rowsCount) // no rows were added for current .tbl file
					$this->errorHandler->setError(['code' => 'file.tbl.invalidData', 'params' => ['fileName' => $tempFileHandle['fileName']]]);
			}
			else
			{
				$this->errorHandler->setError(['code' => 'file.tbl.invalidPointer', 'params' => ['fileName' => $tempFileHandle['fileName'], 'pointer' => $tempFileHandle['pointer']]]);
				return null;
			}
		}

		return !empty($rows) ? $rows : null;
	}
}