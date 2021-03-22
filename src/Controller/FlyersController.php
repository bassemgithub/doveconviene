<?php
declare(strict_types = 1);

namespace App\Controller;
use \SplFileObject;
use \LimitIterator;
use \ArrayObject;
use \Exception;
//require "memory.php";

/**
 * Flyers Controller
 *
 * @method \App\Model\Entity\Flyer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FlyersController extends AppController
{
    /**
     * handlResponse Function
     *
     *
     */
    public function handlResponse($success, $status_code, $error = null, $result = null)
    {

        $res['success'] = $success;
        $res['code'] = $status_code;
        if (isset($error))
        {
            $res['error'] = $error;
        }
        else
        {
            $res['result'] = $result;
        }
        $this->setResponse($this
            ->response
            ->withStatus($status_code));
        return $res;

    }

    public function mergeMultipleFiles($directory_files, $offset, $limit)
    {
        $global_files_array = array();

        // read all the csv file
        foreach (glob($directory_files) as $filePath)
        {
            $file = new SplFileObject($filePath);
            $fileIterator = new LimitIterator($file, $offset, $limit);
            $global_files_array = array_merge($global_files_array, iterator_to_array($fileIterator, false));
        }
        return new ArrayObject($global_files_array);
    }

    public function getHeader($directory_files)
    {
        foreach (glob($directory_files) as $filePath)
        {
            $file = new SplFileObject($filePath);
            $headerIterator = new LimitIterator($file, 0, 1);
            foreach ($headerIterator as $line)
            {
                $line = str_replace("\r", "", $line);
                $line = str_replace("\n", "", $line);
                $header = explode(",", $line);
            }
            return $header;
        }

    }

    public function filterData($row, $filter)
    {
        $count = 0;
        foreach ($filter as $key => $value)
        {

            // validate date active or not, the if used to check that the passed row is not the header
            if ($row['start_date'] != 'start_date' && !isset($filter['id']))
            {
                $startDate = date('Y-m-d', strtotime(str_replace('-', '/', $row['start_date'])));
                $endDate = date('Y-m-d', strtotime($row['end_date']));
                $today = date('Y-m-d', strtotime('now'));

                if (($startDate > $today) || ($today > $endDate))
                {
                    $count += 1;
                    break;
                }
            }
            // If filter value is different to the line value so the count will be incremented in
            // order to finish the loop quickly
            if ($filter[$key] != $row[$key])
            {
                $count += 1;
                break;
            }

        }
        return $count;
    }

    public function applyFieldsFilter($row, $fields, $header)
    {
        // 1) filter with fields
        $field = explode(",", $fields);
        for ($i = 0;$i < count($field);$i++)
        {
            if (!in_array($field[$i], $header))
            {
                $error = json_decode(json_encode(array(
                    "message" => "Bad Request",
                    "debug" => "wrong fileds list"
                )));
                return $this->handlResponse(false, 400, $error, null);
            }

        }
        $field = array_combine($field, $field);

        $row = array_intersect_key($row, $field);
        return $row;
    }
    /**
     * readTheFile Function
     *
     *
     */
    public function readTheFile($directory_files, $limit = 100, $page = 0, $fields = null, $filter = null)
    {

        //1) limit, page=pages
        $offset = $page * 100;

        $res = array();
        $header = array();
        $all_rows = array();
        $fileIterator = $this->mergeMultipleFiles($directory_files, $offset, $limit);

        $header = $this->getHeader($directory_files);
        $all_rows = array();
        foreach ($fileIterator as $line)
        {
            $row = explode(",", trim($line));

            $row = array_combine($header, $row);

            if ($filter != null)
            {
                $count = $this->filterData($row, $filter);
                // if the count is different to 0 that mean that the line content dont match the fiter data
                if ($count != 0)
                {
                    continue;
                }

            }
            if (isset($fields))
            {
                $row = $this->applyFieldsFilter($row, $fields, $header);
            }
            $all_rows[] = $row;
        }

        if (count($all_rows) > 0)
        {
            return $this->handlResponse(true, 200, null, $all_rows);
        }
        else
        {
            if (isset($filter['id']))
            {
                $error = json_decode(json_encode(array(
                    "message" => "Not found",
                    "debug" => "Resource " . $filter['id'] . " not Found"
                )));
                return $this->handlResponse(fasle, 404, $error, null);
            }
            else
            {
                $error = json_decode(json_encode(array(
                    "message" => "Not found",
                    "debug" => "Not found"
                )));
                return $this->handlResponse(fasle, 404, $error, null);
            }
        }

    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($id = null)
    {
        $res = array();
        $filter = array();
        $filter = isset($_GET['filter']) ? $_GET['filter'] : $filter;
        $fields = isset($_GET['fields']) ? $_GET['fields'] : null;
        if ($id != null)
        {
            $filter['id'] = $id;
        }
        $res = $this->readTheFile("../src/csv/*.csv", 100, $page = 0, $fields, $filter);
        $this->set(['my_response' => $res, '_serialize' => 'my_response', ]);
        $this->RequestHandler->renderAs($this, 'json');
    }

}

