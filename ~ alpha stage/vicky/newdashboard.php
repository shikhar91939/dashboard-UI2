<?php
class Newdashboard extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->database();
       
        if(!$this->session->userdata('is_logged_in')){
            redirect('admin/login');
        }
    }
  
    public function index()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/metricsboard';
      $this->load->view('includes/template', $data);  
    }

  public function submitDateRange()
    {
    $start_ymd=$this->input->post('start');
    $end_ymd=$this->input->post('end');

    // $start = implode('/', array_reverse(explode('-', $start)));
    // $end = implode('/', array_reverse(explode('-', $end)));
    // echo $this->dateFormatter("2015-05-01");

    $start = str_replace('/','-',$start_ymd);
    $end = str_replace('/','-',$end_ymd);

    // $start = "2015-05-01";
    // $end = "2015-05-05";


    $date_interval_obj = date_diff(date_create($start), date_create($end));// date_diff() works in yyyy-mm--dd . e.g. 2013-03-15
    // var_dump($date_interval_obj->format("%a")); // format("%R%a") will give string(2) "+4"   --where--   format("%a") gives string(1) "4"
    $date_interval = $date_interval_obj->format("%a");

    $start = $this->dateFormatter($start);
    $end = $this->dateFormatter($end);

    $start_comparison = date('Y-m-d',strtotime($start . "-$date_interval days"));


    $response_x = array('start'=> $start_ymd, 'end' => $end_ymd, 'start_comparison' => $start_comparison, 'interval'=> $date_interval);
    // echo json_encode($response_x);
    // $response_combined = array("mis_box"=> $response_x , "two"=>"two_val");
    // echo "<pre>";
    // var_dump($response_combined);die;
    // var_dump(json_encode($response_combined));die;
    // echo "</pre>";



    $response_mis = $this->getMISdata($start_comparison, $start_ymd, $end_ymd);
    // $response_combined = array("mis_box"=> $response_mis , "two"=>"two_val");
    $response_combined = array();
    $response_combined["mis_box"] = $response_mis;

    $response_qc = $this->getQCdata($start_comparison, $start_ymd, $end_ymd);
    $response_combined["qc_box"] = $response_qc;


    // echo "<pre>";
    // var_dump($response_combined);die;
    // var_dump(json_encode($response_combined));die;
    // echo "</pre>";
    
    echo json_encode($response_combined);
    }

    public function getSalesData($start_comparison = null, $start_ymd = '2015-05-04 00:00:00', $end_ymd ='2015-05-07 00:00:00' )
    {
        try {
            $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
            $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');


      $params = array('complex_filter'=>
          array(
              array('key'=>'created_at','value'=>array('key' =>'from','value' => $start_ymd)),
              array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => $end_ymd))
            )
        );
        $ordersList = $client->salesOrderList($session,$params);
        // echo "<pre>";
        // var_dump($ordersList);
        // echo "</pre>";

        $pendingCount = 0;
        $cancelledCount = 0;
        $confirmedCount = 0;
        $orderCount = 0;

        foreach ($ordersList as $order ) 
        {
            if($order->state == 'pending' || $order->state == 'pending_payment' ||  $order->status == 'processing')
            {
                $pendingCount++;
            }
            elseif ( $order->status == 'canceled')
            {
              $cancelledCount++;
            }
            else
            {
              $confirmedCount++;
            }
            $orderCount++;
        }







        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }      
    }

    public function dateFormatter($date)
    {
        // $array_ymd = explode('-', $date);
        // $array_ymd = array_reverse($array_ymd);
        // $date = implode('/', $array_ymd);
        // now $date format is dd/mm/yyy


        $date =  date('m-d-Y', strtotime($date));//need date in m-d-Y for "+x days" in strtotime
        $date = implode('/', explode('-', $date));

        return $date;
    }

    public function getInventoryData()
    {
        $sqlo = "select t1.id,t1.productid,t4.name as client,t1.old_status,t1.new_status,t1.time_stamp,datediff(now(),t1.time_stamp) from status_update_log as t1 inner join (SELECT max(id) as mid FROM `status_update_log` group by productid) as t2 on t1.id = t2.mid inner join products as t3 on t1.productid = t3.id left join compniesdata as t4 on t3.client_id = t4.id";
        $query = $this->db->query($sqlo);
        $table_sql = $query->result_array();
        echo "<pre>";
        var_dump($table_sql);die;
        echo "</pre>";
    }

    public function getgraph()
    {

      
  $query = $this->db->query("SELECT concat(t2.name,' ',t1.sell_as) as catstat,count(t1.id) as quant FROM `products` as t1 left join categories as t2 on t1.category_id = t2.id group by catstat");
  $table = $query->result_array();
  // echo "<pre>";
  // var_dump($table);
  // echo "</pre>";

  $tableForChart =  array();
  $sellAsStatus = 'x';
  $qty = 'y';
  foreach ($table as $rowNumber => $array_ofRow)
  {
    foreach ($array_ofRow as $key => $value) 
    {
      // echo "key = ";var_dump($key); echo "value=";  var_dump($value); echo "<br/>" ;
      if ($key == "catstat") 
        $sellAsStatus = $value;
      elseif ($key == "quant")
        $qty = $value;
      else
      {
        echo "unknown key value";
        die;
      }
    }
    $tableForChart[$sellAsStatus] = $qty;
  }
  array_splice($tableForChart, 0, 1); // REMOVING 1ST element. (key,value ) => (, 2958)
  $tableForChart_keys = array_keys($tableForChart);
  $array_hardwareTypes = array();
  getHardwareTypes($tableForChart_keys, $array_hardwareTypes);

  function getHardwareTypes($tableForChart_keys, &$array_hardwareTypes)
  {
    foreach ($tableForChart_keys as $currentKey)
    {
      $exploded_currentKey = explode(" ", $currentKey);
      $firstWord_currentKey = $exploded_currentKey[0];
      if (! in_array($firstWord_currentKey, $array_hardwareTypes)) 
        $array_hardwareTypes[] = $firstWord_currentKey;
      
    }

    // change "Featured" hardware type to "Feaured Phone"
    if ($index_Feature = array_search("Feature", $array_hardwareTypes))
      $array_hardwareTypes[$index_Feature] = "Feaured Phone";
    
  }
  // var_dump(array(0,""));die;
    //now devide the inventory in Unboxed, Refurbished, ...
  $array_sellAsStatus = get_All_SellAsStatus($tableForChart_keys);
  function get_All_SellAsStatus($tableForChart_keys)
  {
    $returnArray = array();
    foreach ($tableForChart_keys as $currentKey)
    {
      $exploded_currentKey = explode(" ", $currentKey);
      $sellAs_currentKey = ( ( $exploded_currentKey[1] != "Phone") ? $exploded_currentKey[1] : $exploded_currentKey[2]); //find the 1st word that gives a clue about the sell as. "Send" in "Feature Phone Send to Service Center" tells us that its send to SC
      if (in_array($sellAs_currentKey, array('','0')))
        {
          continue;// remove "" and int(0) from arraay as they are not sellAs statuses
      }
      if (! in_array($sellAs_currentKey, $returnArray))
        $returnArray[] =  $sellAs_currentKey; 
    }

    // change "Send" hardware type to "Sent to Service center"
    if ($index_send = array_search("Send", $returnArray))
        $returnArray[$index_send] = "Send to Service center";

    // remove "" and int(0) from arraay as they are not sellAs statuses
    if (in_array("", $returnArray))
        $returnArray[array_search("", $returnArray)] = "(empty)";
    return $returnArray;
  }
  
  $array_accessories = getDistribution("Accessories", $array_sellAsStatus, $tableForChart);
  $array_Camera = getDistribution("Camera", $array_sellAsStatus, $tableForChart);
  $array_Computer = getDistribution("Computer", $array_sellAsStatus, $tableForChart);
  $array_Feaured_Phone = getDistribution("Feaured_Phone", $array_sellAsStatus, $tableForChart);
  $array_Smartphone = getDistribution("Smartphone", $array_sellAsStatus, $tableForChart);
  $array_Tablet = getDistribution("Tablet", $array_sellAsStatus, $tableForChart);
  $array_Television = getDistribution("Television", $array_sellAsStatus, $tableForChart);
//['Accessories', 'Camera', 'Computer', 'Feaured Phone', 'Smartphone', 'Tablet', 'Television']

  function getDistribution($hardwareType, $array_sellAsStatus, $tableForChart)
  {
  $returnArray = array_combine(array_values($array_sellAsStatus), makeAnArray(count($array_sellAsStatus), "0")); //needed an array of same no. of elements in the second argument. 
    foreach ($tableForChart as $key => $value) 
    {
      $exploded_currentKey = explode(" ", $key);
      if ($exploded_currentKey[0] == $hardwareType) 
      {
        $sellAs_iteration= ( ( $exploded_currentKey[1] != "Phone") ? $exploded_currentKey[1] : $exploded_currentKey[2]); //find the 1st word that gives a clue about the sell as. "Send" in "Feature Phone Send to Service Center" tells us that its send to SC
        if ( in_array($exploded_currentKey[1], array('','0')) )
        {
          continue;
        }
        $updating_thisIndex = array_search($sellAs_iteration, $returnArray);
        $returnArray[$sellAs_iteration] = $value;
      }
      
    }
    return $returnArray;
  }
  
  //   O C D
  // $chartArray_BER = getChartArray('BER',$array_accessories, $array_Camera, $array_Computer, $array_Feaured_Phone, $array_Smartphone, $array_Tablet, $array_Television);
  // function getChartArray($sellAs, $array_accessories, $array_Camera, $array_Computer, $array_Feaured_Phone, $array_Smartphone, $array_Tablet, $array_Television)
  // {
  //   $returnArray = array();
  //   foreach ($array_accessories as $key => $value) 
  //   {
  //       if ($key == $sell_as) 
  //       {
  //         $returnArray[]
  //       }
  //   }
  // }

  function makeAnArray($length, $element)
  {
    $returnArray = array();// Overcart.com
    for ($i=0; $i < $length; $i++) { 
      $returnArray[] = $element;
    }
    return $returnArray;
  }


  // echo "<hr/><pre/>";
  // var_dump($tableForChart);
  // foreach ($tableForChart as $key => $value) {echo "($key, $value)<br/>"; }
  // foreach ($tableForChart as $key => $value) {var_dump($key);echo "->";var_dump($value);}
  // foreach ($array_accessories as $key => $value) {echo "($key, $value)<br/>"; }
  // foreach ($array_accessories as $key => $value) {var_dump($key);echo "->";var_dump($value);}
  // foreach ($array_sellAsStatus as $key => $value) {echo "($key, $value)<br/>"; }
  // print_r($tableForChart_keys);
  // print_r($array_hardwareTypes);
  // echo "</pre>";die;
  // echo $tableForChart["Accessories BER"];
  // foreach ($tableForChart_keys as $this_key) 
  // {
  //   print_r($tableForChart[$this_key]);
    // echo "<br/>";die;
  // }
  // die;

    }
    public function getMISdata($start_comparison, $start_ymd, $end_ymd)
    {
        $returnArray = array();
        $hours = 600;
        $hours_compare = 2*$hours;

        $from = $start_comparison;
        $to = $end_ymd; 

        // $query_total = $this->db->query("SELECT count(id) FROM `status_update_log` where new_status = 'listed' and time_stamp > date_sub(now(),interval $hours_compare hour)");
        $query_total = $this->db->query("SELECT count(id) FROM status_update_log where new_status = 'listed' and date(time_stamp) >= '$from' and date(time_stamp) <= '$to'");
        $table_sql_compare = $query_total->result_array();
        $mis_upload_count_total = "";
        foreach ($table_sql_compare as $outerArray) 
        {
          foreach ($outerArray as $key => $value) 
          {
            $mis_upload_count_total = $value;
          }
        }

        // var_dump( $mis_upload_count_total);die;

        $from = $start_ymd;
        $to = $end_ymd; 
        // $query = $this->db->query("SELECT count(id) FROM `status_update_log` where new_status = 'listed' and time_stamp > date_sub(now(),interval $hours hour)");
        $query = $this->db->query("SELECT count(id) FROM status_update_log where new_status = 'listed' and date(time_stamp) >= '$from' and date(time_stamp) <= '$to'");
        $table_sql = $query->result_array();

        $mis_upload_count = "";

        foreach ($table_sql as $outerArray) 
        {
          foreach ($outerArray as $key => $value) 
          {
            $mis_upload_count = $value;
          }
        }
        $compare_mis = $mis_upload_count_total - $mis_upload_count ;
        $diff_mis= $mis_upload_count  - $compare_mis ;
        $percent_mis =   ($compare_mis == 0) ? "Undefined" : ((double)($diff_mis / $compare_mis) * 100);

        /*$mis_upload_count = "1";*/
        /*$diff_mis = "2";*/
        $mis_percent_display = /*"3";// = */(gettype($percent_mis ) == "string" ) ? $percent_mis :  number_format((float)$percent_mis, 2, '.', '');

        $returnArray["mis_upload_count"] = "" . $mis_upload_count;
        $returnArray["diff_mis"] = "" . $diff_mis;
        $returnArray["mis_percent_display"] = "" . $mis_percent_display;
        $returnArray["isDiffPositive"] =  ($diff_mis>0);

        // echo "<pre>";
        // var_dump($returnArray);
        // echo "</pre>";
        return $returnArray;
    }    

    public function getQCdata($start_comparison, $start_ymd, $end_ymd)
    {
        // $hours = 24;
        // $hours_compare = $hours*2 ;

        $from = $start_comparison;
        $to = $end_ymd;
        $query_quality = $this->db->query("SELECT count(id) FROM `status_update_log` where old_status = 'Under QC' and date(time_stamp) >= '$from' and date(time_stamp) <= '$to'");
        $sqlArray_quality = $query_quality->result_array();
        $totalQCs = "";
        foreach ($sqlArray_quality as $key => $innerArray) 
        {
          foreach ($innerArray as $key1 => $value)
          {
            $totalQCs = $value;
          }
        }

        $from = $start_ymd;
        $to = $end_ymd;
        $query_quality_total = $this->db->query("SELECT count(id) FROM `status_update_log` where old_status = 'Under QC' and date(time_stamp) >= '$from' and date(time_stamp) <= '$to'");
        $sqlArray_quality_total = $query_quality_total->result_array();
        $totalQCs_total = "";
        foreach ($sqlArray_quality_total as $key => $innerArray) 
        {
          foreach ($innerArray as $key1 => $value)
          {
            $totalQCs_total = $value;
          }
        }

        $totalQCs_compare = $totalQCs_total - $totalQCs;
        $totalQCs_diff = $totalQCs - $totalQCs_compare;
        $qc_percent_rise = ($totalQCs_compare == 0) ? "Undefined" : ((double)($totalQCs_diff / $totalQCs_compare) * 100);

        $returnArray = array();
        $returnArray["totalQCs"] = $totalQCs;
        $returnArray["totalQCs_diff"] = $totalQCs_diff;
        $returnArray["qc_percent_rise"] = $qc_percent_rise;
        $returnArray["isDiffPositive"] =  ($totalQCs_diff>0);


        return $returnArray;
    }

    
    public function logisticsandinventory()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/logisticsandinventory';
      $this->load->view('includes/template', $data);  
    }
  
    public function submitDates_inventory()
    {
      $start_ymd=$this->input->post('start');
      $end_ymd=$this->input->post('end');
      
      // echo json_encode(array('start'=>$start_ymd, 'end'=> $end_ymd));

      $returnArray['clientWise']['categories'] = array('Pending Pickup', 'Inbound Holding','Pending QC', 'Under QC', 'Manager\'s escalation',
        'Out for Repair','Ready to upload', 'Listed', 'Returned to Client', 'Sell Offline', 'Inventory Review', 'Sold');
      
      $returnArray['clientWise']['yAxis_text'] = 'Items';
      $returnArray['clientWise']['legend']['Karma'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9.6);
      $returnArray['clientWise']['legend']['Cloudtail'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4.8+3);
      $returnArray['clientWise']['legend']['PB_International'] = array( 0, 0, 0, 10, 10, 20, 20, 20, 20, 10, 10, 9.6);
      $returnArray['clientWise']['legend']['Technix'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4.8);
      $returnArray['clientWise']['legend']['Value_Plus'] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1.0);



      // $returnArray['clientWise']['legend']['Karma'] = array(7.0+5, 6.9+5, 9.5+5, 14.5+5, 18.2+5, 21.5+5, 25.2+5, 26.5+5, 23.3+5, 18.3+5, 13.9+5, 9.6);
      // $returnArray['clientWise']['legend']['Cloudtail'] = array(3.9+5+5, 4.2+5+5, 5.7+5+5, 8.5+5+5, 11.9+5+5, 15.2+5+5, 17.0+5+5, 16.6+5+5, 14.2+5+5, 10.3+5+5, 6.6+5+5, 4.8+3);
      // $returnArray['clientWise']['legend']['PB International'] = array( 7.0-5+5, 6.9-5+5, 9.5-5+5, 14.5-5+5, 18.2-5+5, 21.5-5+5, 25.2-5+5, 26.5-5+5, 23.3-5+5, 18.3-5+5, 13.9-5+5, 9.6);
      // $returnArray['clientWise']['legend']['Technix'] = array(3.9+5, 4.2+5, 5.7+5, 8.5+5, 11.9+5, 15.2+5, 17.0+5, 16.6+5, 14.2+5, 10.3+5, 6.6+5, 4.8);
      // $returnArray['clientWise']['legend']['Value Plus'] = array(-0.9+5, 0.6+5, 3.5+5, 8.4+5, 13.5+5, 17.0+5, 18.6+5, 17.9+5, 14.3+5, 9.0+5, 3.9+5, 1.0);

      

      // echo "<pre>";
      // var_dump($returnArray);
      echo(json_encode($returnArray));
      // echo "<pre>";
    }


}