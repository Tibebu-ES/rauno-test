<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Files\File;
use Exception;

class RaunoTest extends BaseController
{
    public $dataFileds = ['id', 'name', 'unit', 'value', 'date_update', 'date_create', 'icon', 'color'];
    public function index()
    {
        //
        $data['rows'] = $this->readAllData();
        return view('rauno_test_view', $data);
    }


    /**
     * reads csv data
     * @return array - returns array of each row data, each row data are associative array where the keys are
     * field names 'id', 'name', 'unit', 'value', 'date_update', 'date_create', 'icon', 'color'
     * @throws Exception 
     */
    protected function readAllData()
    {
        try {
            $result = [];
            $databaseFile =  fopen(WRITEPATH . 'database/database.csv', 'r');
            if ($databaseFile !== FALSE) {
                //$fields = fgetcsv($databaseFile, 1000, ",");
                while (($data = fgetcsv($databaseFile, 1000, ",")) !== FALSE) {
                    if ($data == $this->dataFileds) {
                        continue;
                    }
                    $row = [];
                    foreach ($data as $index => $value) {
                        $row[$this->dataFileds[$index]] = $value;
                    }
                    $result[] = $row;
                }
            }
            fclose($databaseFile);
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * update entire data
     */
    protected function updateAllData($dataRows)
    {
        //prepend the fields
        array_unshift($dataRows, $this->dataFileds);
        $databaseFile =  fopen(WRITEPATH . 'database/database.csv', 'w');
        if ($databaseFile !== FALSE) {
            foreach ($dataRows as $row) {
                if (fputcsv($databaseFile, $row) === False) {
                    throw new Exception("Error on writting to csv file");
                }
            }
        }
        return true;
    }

    /**
     * updates a single row 
     * @param $id - id of the row to be updatted
     * @param $rowData - associative array of row, fields names are keys
     * @return bool
     */
    protected function updateSingleRow($id, $rowData)
    {
        $status = false;
        $dataRows = $this->readAllData();
        foreach ($dataRows as $index => $row) {
            if ($id == $row['id']) {
                $dataRows[$index] = $rowData;
                if ($this->updateAllData($dataRows)) {
                    $status = true;
                    break;
                }
            }
        }

        return $status;
    }
}
