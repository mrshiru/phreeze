<?php
/** @package    verysimple::Phreeze */

/**
 * ExportUtility Class
 *
 * This contains various utility functions for exporting Phreezable objects into other formats
 * such as Excel, CSV, tab-delimited, XML, etc
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2005 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class ExportUtility
{
	/**
	 * Streams to the browser the provided array of objects as a basic Excel document
	 * with headers
	 * 
	 * Note that PEAR Spreadsheet_Excel_Writer must be installed
	 * @link http://pear.php.net/package/Spreadsheet_Excel_Writer
	 * 
	 * @param Array an array of Phreezable objects, obtained for example, using DataSet->ToObjectArray
	 * @param Phreezer $phreezer is needed to get field maps
	 * @param string (optional) The title of the report
	 */
	static function OutputAsExcel(Array $objects, Phreezer $phreezer, $reportTitle = "Data Export", $fileName = "export.xls")
	{
		require_once 'Spreadsheet/Excel/Writer.php';
		
		// create the workbook and worksheet
		$workbook = new Spreadsheet_Excel_Writer();
		$worksheet = $workbook->addWorksheet("Export");
		
		$BOLD_MED =& $workbook->addFormat();
		$BOLD_MED->setSize(16);
		$BOLD_MED->SetBold();
		
		$BOLD_REG =& $workbook->addFormat();
		$BOLD_REG->setSize(11);
		$BOLD_REG->SetBold();
		
		$NORMAL =& $workbook->addFormat();
		$NORMAL->setSize(11);
		
		$CURRENCY =& $workbook->addFormat();
		$CURRENCY->setNumFormat('0.00');
		$CURRENCY->setSize(11);
		$CURRENCY->setAlign('right');

		$worksheet->writeString(0, 0, $reportTitle, $BOLD_MED);

		// default to no columns
		$fields = Array();
		$columns = Array();
		$is_numeric = Array();
		
		// print the headers
		// while we're looping, also parse the fields so we don't have to do 
		// it repeatedly when looping through data
		if (isset($objects[0]))
		{
			$fields = $phreezer->GetFieldMaps( get_class($objects[0]) );
			$columns = array_keys($fields);
			
			$current_column = 0;
			foreach ($columns as $column) 
			{
				// save this so we don't check it every time when looping through data
				$is_numeric[$column] = $fields[$column]->IsNumeric();

    			$worksheet->writeString(2, $current_column, $column, $BOLD_REG);
    			$current_column++;
			}

		}
		
		$current_row = 3;
		
		// loop through all of the data
		foreach ($objects as $object)
		{
			$current_column = 0;
			foreach ($columns as $column) 
			{
				if ($is_numeric[$column])
				{
					$worksheet->write($current_row, $current_column, $object->$column, $NORMAL);
				}
				else
				{
					$worksheet->writeString($current_row, $current_column, $object->$column, $NORMAL);
				}
				
    			$current_column++;
			}
			$current_row++;
		}
		
		// lastly write to the footer to sum the numeric columns
		$current_column = 0;
		foreach ($columns as $column) 
		{
			if ($is_numeric[$column])
			{
				$columnLetter = ExportUtility::GetColumnLetter($current_column);
				$formula = "=SUM(".$columnLetter."3:".$columnLetter.($current_row-1).")";
				
				// notice the @ sign in front because this will fire a deprecated warning due to use of "split"
				@$worksheet->write($current_row, $current_column, $formula, $BOLD_REG);
			}
			
    		$current_column++;
		}
		
		// =SUM(D4:D23)
		
		//$worksheet->writeString($current_row + 1, 0, 'EOF', $BOLD_MED);
		
		$workbook->send($fileName);
		$workbook->close();
	}
	
	/**
	 * Given a zero-based column number, the approriate Excel column letter is 
	 * returned, ie A, B, AB, CJ, etc.  max supported is ZZ, higher than that will
	 * throw an exception.
	 * 
	 * @param int $columnNumber
	 */
	static function GetColumnLetter($columnNumber)
	{
		// work with 1-based number
		$colNum = $columnNumber + 1;
		$code = "";
		
		if ($colNum > 26)
		{
			// greater than 26 means the column will be AA, AB, AC, etc.
			$left = floor($columnNumber / 26);
			$right = 1 + ($columnNumber % 26);

			if ($left > 26) throw new Exception("Columns exceed supported amount");
			
			$code = chr($left + 64) . chr($right + 64);
		}
		else
		{
			$code = chr($colNum + 64);
		}
		
		return $code;
		
		
	}
}

?>