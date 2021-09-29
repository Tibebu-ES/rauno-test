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
     * get post data as input
     * updates name and unit 
     */
    public function update()
    {
        $response = "";
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $unit = $this->request->getPost('unit');

        $row = $this->getSingleRow($id);
        if ($row != null) {
            $row['name'] = $name;
            $row['unit'] = $unit;
            $status = $this->updateSingleRow($id, $row);
            if ($status) {
                $response = array('error' => false, "message" => 'row updated!', 'row' => $row);
            } else {
                $response = array('error' => true, "message" => 'error on updating!');
            }
        } else {
            $response = array('error' => true, "message" => 'row not found, error updating!');
        }
        echo json_encode($response);
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

    /**
     * get a single row with the given id
     * @param $id - id of the row 
     * @return  - row data
     */
    protected function getSingleRow($id)
    {
        $rowToReturn = null;
        $dataRows = $this->readAllData();
        foreach ($dataRows as $index => $row) {
            if ($id == $row['id']) {
                $rowToReturn = $row;
            }
        }

        return $rowToReturn;
    }
}
