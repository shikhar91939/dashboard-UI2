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

    public function getData_staticElements()
   {

      $responseArray = $this->getData_SoapApi();
      echo json_encode($responseArray);
    }

    public function getData_SoapApi()
    {
      /*
      Get all data related to the SOAP API function called here 
      */
      date_default_timezone_set('Asia/Kolkata');
    
      $yesterday = date('Y-m-d 00:00:00', strtotime("yesterday"));
      $now =  date('Y-m-d H:i:s', strtotime("now"));
      $dayBeforeYest = date('Y-m-d H:i:s', strtotime("-2 days midnight")) ;
      $monthStart =  date('Y-m-d H:i:s', strtotime("first day of this month midnight")) ;  //never set to $from . though $monthStart_minus1  is
      $monthStart_minus1 =  date('Y-m-d H:i:s', strtotime("last day of previous month midnight")) ;   //taking a margin of one day so that Orders are not missed due to time diff.

      $to = $now;
      $from = $monthStart_minus1; // from is set to monthStart OR dayBeforeYest depending upon whichever comes first
      if (strtotime("first day of this month midnight") > strtotime("-2 days midnight") ) 
      {
        $from = $dayBeforeYest;        
      }

      // var_dump($now);echo "now ";echo "<br>";
      // var_dump($dayBeforeYest);echo "day bef yest ";echo "<br>";
      // var_dump($monthStart_minus1);echo "month Start";
      // die;
      
      try 
      {
        $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
        $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');

        $params = array('complex_filter'=>
          array(
              array('key'=>'created_at','value'=>array('key' =>'from','value' => $from)), //taking dayBeforeYesterday to avoid missing yesterday's orders due to time zone difference
              array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => $to))
            )
        );
        $ordersList = $client->salesOrderList($session,$params);


      } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }

        //   echo "<pre>";
        // foreach ($ordersList as $order) 
        // {
        //   echo $order->increment_id;
        //   echo " . ";
        //   echo $order->created_at;
        //   $state = $order->state;
        //   $status = $order->status;
        //   // echo " | <strong>".$state.'</strong> -> <em>'.$status.' </em><br>';
        //   echo ", <strong>".$state.'</strong> , <em>'.$status.' </em><br>';
        // }die;
        $monthStart_timeStamp = strtotime("first day of this month midnight");
        $yesterday_timeStamp = strtotime("-2 days midnight");
        $today_timeStamp = strtotime("today");
        $today = date('Y-m-d H:i:s',$today_timeStamp);

        // echo $today.'<hr>';

      $count_monthsOrders =0;
      $monthlyCount_CSconfirmed =0;
      $monthlyConfirmedRevenue = 0;
      $count_yesterdaysOrders= 0;
      $count_todaysOrders =0;
      $count_sameDayShips =0;
      $count_CSconfirmed =0;
      $count_CScancelled =0;
      $todaysConfirmedRevenue =0;
      $array_pendingStates = array('pending_payment','canceled');
      $array_pendingStatus = array('pending',/*'processing',*/ 'canceled','reverse_pickup','rto'); //RTOs not included in month revenue (rev. pickup)
      $orderValue = 0;

      foreach ($ordersList as $order)
      {
        //looping through each order starting this monthStart (Or DayBefYesterday if it comes first) and till now      
        $order_timeStamp = strtotime(" + 330 minutes", strtotime($order->created_at)) ;
        $order_time = date('Y-m-d H:i:s',strtotime(" + 330 minutes", strtotime($order->created_at))) ;
        // echo "<br/>".$order->increment_id. " - created at ". $order->created_at ." - order_time=$order_time"; echo "<br>";//debugging line

        $orderValue = $order->grand_total;
        $state = $order->state;
        $status = $order->status;
        if ($order_timeStamp >= $monthStart_timeStamp) //if its this month's order
        {
          // echo "Month's order, ";//debugging line
          // echo "Sate:  " .", <strong>".$state.'</strong> , Status:<em>'.$status.' </em>';//debugging line
          if ( ! ( in_array($state, $array_pendingStates) || in_array($status, $array_pendingStatus))) // is the order is NOT pending
          {
            $monthlyCount_CSconfirmed ++;
            $monthlyConfirmedRevenue += $orderValue;
          }

          $count_monthsOrders++;
        }

        if ($order_timeStamp >= $yesterday_timeStamp && $order_timeStamp < $today_timeStamp) //all yesterday's 
        {
          $count_yesterdaysOrders++;
        }
        elseif ($order_timeStamp >= $today_timeStamp) // if its today's order
        {
          // echo "Sate:  " .", <strong>".$state.'</strong> , Status:<em>'.$status.' </em>';//debugging line
          if ($state === 'complete') // Its been shipped today. $status === 'delivered ' is not needed  as State still remains 'complete' if status is delivered
          {
            $count_sameDayShips ++;
            // echo "<br>Same day ship:<span style=\"font-size:25px;\"></span>";
          }
          if ( ! ( in_array($state, $array_pendingStates) || in_array($status, $array_pendingStatus)))  //if the order is NOT pending
          {
            // echo "<strong>Confirmed</strong>";
            $count_CSconfirmed ++;
            $todaysConfirmedRevenue += $orderValue;
          }
          if ($status === 'canceled')
          {
            // echo "--:canceled detected";
            $count_CScancelled ++;
          }
          // else//debugging only
          //   echo "<strong>Pending </strong>";
          // if (in_array($state, $array_pendingStates)) echo ' in_array($state, $array_pendingStates)' ;
          // if (in_array($status, $array_pendingStatus)) echo ' in_array($status, $array_pendingStatus)' ;
          
          $count_todaysOrders ++;
        }
        
        // else //for debugging: to see if any of the ignored orders are today's
        // {
        //   // echo "  yesterday's order<br/>  ".  date('Y-m-d H:i:s',$order_timeStamp) ."<".  date('Y-m-d H:i:s',$today_timeStamp)."   <hr>";
        // } 
  
        // echo "<hr>";
      }//end of foreach $ordersList as $order

      // echo "$monthlyCount_CSconfirmed";die;

      $percent_sameDayShips=  $count_sameDayShips /$count_CSconfirmed *100;
      $percent_CSconfirmed= $count_CSconfirmed / $count_todaysOrders *100;
      $percent_CScancelled = $count_CScancelled/ $count_todaysOrders *100;
      $thisMonthsTarget = 12000000;   // Change this value to current month's sales target (in Rs.)
      $percent_monthlySalesTarget = $monthlyConfirmedRevenue / $thisMonthsTarget *100;

      //fomatting data before sending
      $percent_sameDayShips = floor($percent_sameDayShips);
      $percent_CSconfirmed = floor($percent_CSconfirmed);
      $percent_CScancelled = floor($percent_CScancelled);
      $todaysConfirmedRevenue = floor($todaysConfirmedRevenue);
      $todaysConfirmedRevenue = $this->moneyFormatIndia($todaysConfirmedRevenue);
      $monthlyConfirmedRevenue = floor($monthlyConfirmedRevenue);
      $monthlyConfirmedRevenue = $this->moneyFormatIndia($monthlyConfirmedRevenue);
      $percent_monthlySalesTarget = number_format((float)$percent_monthlySalesTarget, 2, '.', ''); // gives a string value with 2 places after decimal
      $thisMonthsTarget = $this->moneyFormatIndia($thisMonthsTarget);

      $returnArray = array('count_sameDayShips'=>$count_sameDayShips, 'count_CSconfirmed'=>$count_CSconfirmed,'todaysConfirmedRevenue' =>$todaysConfirmedRevenue, 'percent_sameDayShips'=> $percent_sameDayShips,'percent_CSconfirmed'=> $percent_CSconfirmed, 'percent_CScancelled'=>$percent_CScancelled, 'count_yesterdaysOrders' => $count_yesterdaysOrders ,'thisMonthsTarget'=> $thisMonthsTarget,'monthlyConfirmedRevenue'=> $monthlyConfirmedRevenue,'percent_monthlySalesTarget'=> $percent_monthlySalesTarget);

      // echo "<pre>";
      // var_dump($returnArray); die;
      return $returnArray;
    }

    function moneyFormatIndia($num)
    {
      $explrestunits = "" ;
      if(strlen($num)>3){
          $lastthree = substr($num, strlen($num)-3, strlen($num));
          $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
          $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
          $expunit = str_split($restunits, 2);
          for($i=0; $i<sizeof($expunit); $i++){
              // creates each of the 2's group and adds a comma to the end
              if($i==0)
              {
                  $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
              }else{
                  $explrestunits .= $expunit[$i].",";
              }
          }
          $thecash = $explrestunits.$lastthree;
      } else {
          $thecash = $num;
      }
      return $thecash; // writes the final format where $currency is the currency symbol.
    }

    public function appendInFile()
    { 
      // try{
      //   echo "hello<br>";
      //   $file = "/var/www/bootstrapp/vicky/returnfromupload/testFile.txt";
      //   $fh = fopen($myFile, 'a') or die("can't open file");
      //   fwrite($fh, "stringasasas");
      //   fclose($fh);
      //   echo "appended in file";
      // }
      // catch (Exception $e) {
      // die ('exception in file writing: ' . $e->getMessage());
      // }

      // try {
      //   $fileRead = "/var/www/bootstrapp/vicky/apiCall.php";
      //   // $frh = fopen($fileRead, 'r');
      //   $contents= file_get_contents($fileRead, true);
      //   var_dump($contents);

      // } catch (Exception $e) {
      //   die ('exception in file reading: ' . $e->getMessage());
      // }
    }
  public function submitDateRange()
    {
    $start_ymd=$this->input->post('start'); // $start_ymd ->  startTime in YearMonthDay
    $end_ymd=$this->input->post('end');     //  || for $end_ymd

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

    $today = date("Y-m-d",strtotime("today"));
    $yesterday  = date("Y-m-d",strtotime("yesterday"));
    // var_dump($start_ymd);echo "<br>";
    // var_dump($today);die;
    if ($start_ymd == $today) 
    {
      //the function getSalesData() takes today by default
      $response_sales = $this->getSalesData();
      $response_combined["sales_graph"] = $response_sales;
    }
    else
    {
      if ($start_ymd === $end_ymd)
      {
        $end_ymd = date("Y-m-d",strtotime("+1 day",strtotime($start_ymd))); // made this correction as chosing "yesterday" gives (yesterday 00:00:00) to (yesterday 00:00:00)
      }

      $from = $start_ymd. " 00:00:00";
      $to = $end_ymd. " 00:00:00";

      // $from = "2015-07-10 00:00:00";// debuging only

      // echo json_encode(array("from"=>$from, "to"=>$to));die;
      // if ($end_ymd == $today && $start_ymd == $yesterday) {
      //   # code... }
      $response_sales = $this->getSalesData($from, $to);
      $response_combined["sales_graph"] = $response_sales;
      // echo json_encode($response_sales);die;
    }
    echo json_encode($response_combined);


    // echo "<pre>";
    // var_dump($response_sales);
    // echo "<hr>";
    // var_dump($response_combined);die;
    // var_dump(json_encode($response_combined));die;
    // echo "</pre>";
    
    // echo json_encode($response_combined);
    }

    public function getLogisticsData($from='2015-05-01 00:00:00' , $to='2015-05-07 00:00:00' )
    {echo "string";die;
      /*
      gets number of sameday ships for any date range
      *//*
      date_default_timezone_set('Asia/Kolkata');
      echo date('Y-m-d H:i:s',strtotime("now"));echo "<br>";
      $ordersList=null;

      $to_timeStamp = strtotime($to);
      $from_timeStamp = strtotime($from);
      $from_minus1_timeStamp = strtotime("-1 days",strtotime($from));
      $from_minus1 = date('Y-m-d 00:00:00', $from_minus1_timeStamp);// run soap command from $srom-1day o avoid missing orders due to time zone diff
      //also making sure time of $from  and $to is 00:00:00 as we need same DAY ships
      try 
      {
        $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
        $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');

        $params = array('complex_filter'=>
          array(
              array('key'=>'created_at','value'=>array('key' =>'from','value' => $from_minus1)), //taking from 1 day before the range due to time zone difference
              array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => $to))
            )
        );
        $ordersList = $client->salesOrderList($session,$params);

      } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
      }


      $count_sameDayShips = 0;
      $count_totalOrders = 0;
      // var_dump($ordersList);die;
      foreach ($ordersList as $order)
      {
        $orderID = $order->increment_id;
        $order_timeStamp = strtotime(" + 330 minutes", strtotime($order->created_at)) ;
        $order_time = date('Y-m-d H:i:s', $order_timeStamp) ;
        // echo "<br/>".$order->increment_id. " - created at ". $order->created_at ." - order_time=$order_time"; echo "<br>";//debugging line

        $state = $order->state;
        $status = $order->status;

        if ($order_timeStamp >= $from_timeStamp && $order_timeStamp <= $to_timeStamp) // if it lies in the date range
        {
          // echo "in our time range";
          echo "check shipping same";die;
          if($this->wasShippedSameDay($orderID)) //if its a same day ship
          {
            // echo "Sate:  " .", <strong>".$state.'</strong> , Status:<em>'.$status.' </em>';//debugging line
            if ($state === 'complete') // Its been shipped today. $status === 'delivered ' is not needed  as State still remains 'complete' if status is delivered
            {
              $count_sameDayShips ++;
              // echo "<br>Same day ship:<span style=\"font-size:25px;\"></span>";
            }
          }
        }
        // echo "<hr>";
        $count_totalOrders ++;
      }

    */}

    public function wasShippedSameDay($orderID = 'BST1100012893') //default order number 'BST1100012893' added for debugging
    {/*
      date_default_timezone_set('Asia/Kolkata');
      echo "aisa";die;

      try
      {
        $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
          $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');
    
    
          $params = array('complex_filter'=>
              array(
                  array('key'=>'created_at','value'=>array('key' =>'from','value' => '2015-05-01 00:00:00')),
                  array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => '2015-05-07 00:00:00'))
              )
          );
          echo "<pre>";
          $result = $client->salesOrderInfo ($session, 'BST1100012893');
          var_dump($result);die;
          $list_historyArrays = $result->status_history;
          foreach ($list_historyArrays as $historyArray)
          {
            if($historyArray->status == 'Preorder - Pending Release')
            {
              var_dump($historyArray);die;
            }
          }die;
          $result = $client->salesOrderList($session,$params);
          // echo "<pre>";
          // var_dump($result);
          // echo "</pre>";
      }
      catch (SoapFault $fault) {
          trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
      }
      return true;
    */}

    public function getSalesData($start_ymd = null , $end_ymd =null , $divisions_xAxis =24)
    {
      // $ret = array('start_ymd'=>$start_ymd , 'end_ymd'=>$end_ymd  , 'divisions_xAxis'=>$divisions_xAxis);
      // return $ret; //check console .log() to see this value below "return from submitDateRange"
      // die;//i know it will not be executed after return, but helps in Ctrl+F"die"


      date_default_timezone_set('Asia/Kolkata');
      if (is_null($end_ymd) || is_null($start_ymd)) 
      {
        $start_ymd = date('Y-m-d 00:00:00', strtotime("today"));
        $end_ymd =  date('Y-m-d 00:00:00', strtotime("tomorrow"));
      }
      // var_dump($start_ymd);echo"<br>";
      // var_dump($end_ymd);die;

      $timeSplits = $this->splitTimeRange($start_ymd, $end_ymd, $divisions_xAxis);
                  // echo "<pre>";
                  // var_dump($timeSplits);
                  // echo "</pre>";die;
      $returnArray['xAxis'] = $timeSplits[2];
      // echo "<pre>";
      // var_dump($returnArray);die;

      try 
      {
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
        // foreach ($ordersList as $order) {
        //   echo ".";
        //   echo $order->created_at;
        //   $state = $order->state;
        //   $status = $order->status;
        //   // echo " | <strong>".$state.'</strong> -> <em>'.$status.' </em><br>';
        //   echo ", <strong>".$state.'</strong> , <em>'.$status.' </em><br>';
        // }
        // echo "</pre>";die;

        $interval_number = 1; //interval labled as 1,2,3. Not 0,1,2,3...
        $order_time = ''; $orderValue = 0;
        $pendingCount = 0; $pendingAmount = 0;
        $cancelledCount = 0; $cancelledAmount = 0;
        $confirmedCount = 0; $confirmedAmount = 0; $confirmedAmt_totalRange=0;
        $totalOrderCount = 0; $totalOrderAmount = 0;
        $totalConfirmed_count = 0; /*$totalConfirmed_amount = 0; //amount not being used*/
        $orderTable=null;


        foreach ($ordersList as $order ) 
        {
          $order_time = date('Y-m-d H:i:s',strtotime(" + 330 minutes", strtotime($order->created_at))) ;
          /*
          Wasted >2.5 hrs in debugging because of not adding "&& $interval_number<$divisions_xAxis" in the following while's condition -Shikhar
          Thus, please pay attention while writing the condition of while statements. Infinite loops are very common
          */
          while(strtotime($order_time) > $timeSplits[0][$interval_number] && $interval_number<$divisions_xAxis)     // keep ++ing $interval_number WHILE(better than if() ) the current order is out of crrent interval,
          {
            // echo "$order_time > timeSplits[1][interval_number=$interval_number]=".$timeSplits[1][$interval_number].'<hr>';
            $orderTable["interval_number".$interval_number]=array('pendingCount'=>$pendingCount,'cancelledCount'=>$cancelledCount,'confirmedCount'=>$confirmedCount, 'totalOrderCount'=>$totalOrderCount,
                                                                      'pendingAmount'=>$pendingAmount,'cancelledAmount'=>$cancelledAmount,'confirmedAmount'=>$confirmedAmount, 'totalOrderAmount'=>$totalOrderAmount);
            $interval_number ++;

          //initialize all variables for next interval
          $pendingCount = 0; $pendingAmount = 0;
          $cancelledCount = 0; $cancelledAmount = 0;
          $confirmedCount = 0; $confirmedAmount = 0;
          $totalOrderCount = 0; $totalOrderAmount = 0;
          // echo "<pre>";
          // var_dump($orderTable);
          // echo "</pre>";
          // if($interval_number>7)die;
          }

          $orderValue = $order->grand_total;
          if($order->state == 'pending_payment' || $order->status == 'pending' ||   $order->status == 'processing') // (state, status) can be ( new, pending ) or ( pending_payment, pending_payment) for pending orders. // processing because waiting for IMEI ? (not sure)
            {
              $pendingCount++;
              $pendingAmount += $orderValue;
            }
          elseif ( $order->status == 'canceled')
            {
              $cancelledCount++;
              $cancelledAmount += $orderValue;
            }
          else
            {
              $confirmedCount++;
              $confirmedAmount += $orderValue;
            }

          $totalOrderCount++;
          $totalOrderAmount +=$orderValue;
          // echo $order->increment_id." order_time=$order_time<br>";
        }

        while (count($orderTable)<$divisions_xAxis) //again using while instead of if()
        {
            // ending interval $interval_number as no more orders
            // echo "ending interval $interval_number as no more orders<br>";
            $orderTable["interval_number".$interval_number]=array('pendingCount'=>$pendingCount,'cancelledCount'=>$cancelledCount,'confirmedCount'=>$confirmedCount, 'totalOrderCount'=>$totalOrderCount,
                                                                      'pendingAmount'=>$pendingAmount,'cancelledAmount'=>$cancelledAmount,'confirmedAmount'=>$confirmedAmount, 'totalOrderAmount'=>$totalOrderAmount);
            $interval_number ++;  // keep ending intervals till $orderTable)<$divisions_xAxis

            //initialize all variables for next interval (if any) to have zero orders
            $pendingCount = 0;
            $cancelledCount = 0;
            $confirmedCount = 0;
            $totalOrderCount = 0;
        }

        $returnArray['sales_distribution'] = $orderTable;

        foreach ($orderTable as $intervalArray) 
        {
          $confirmedAmt_totalRange += $confirmedAmount;
          $totalConfirmed_count += $intervalArray['confirmedCount'];
        }
        $confirmedAmt_totalRange = $this->moneyFormatIndia($confirmedAmt_totalRange);
        $returnArray['confirmedAmt_totalRange']= $confirmedAmt_totalRange;
        $returnArray['totalConfirmed_count']= $totalConfirmed_count;

        
                  // echo "<pre>";
                  // var_dump($returnArray);
                  //  echo "</pre>";die;
        return $returnArray;
        // echo json_encode($returnArray);


        // echo 'count($orderTable)<$divisions_xAxis -> '. count($orderTable)." <$divisions_xAxis  <br>";
        // if(count($orderTable)<$divisions_xAxis)

        // echo "<pre>";
        // var_dump($orderTable);
        // echo  "pendingCount = $pendingCount; "."cancelledCount = $cancelledCount;"."confirmedCount = $confirmedCount;"."totalOrderCount = $totalOrderCount;";      
        // die;
        // echo "</pre>";
        /*
        _DataStructure Map_

        [inteval1]=[pendingCount1][cancelledCount1][confirmedCount1][totalOrderCount1]
        [inteval2]=[pendingCount2][cancelledCount2][confirmedCount2][totalOrderCount2]
        .
        .
        .
        [intervaln]=[pendingCountn][cancelledCountn][confirmedCountn][totalOrderCountn]
        */


      } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }      
    }

    function splitTimeRange($start_timeStamp= '2015-05-13', $end_timeStamp='2015-05-13', $intervals='24') //remove all 3 default values after debugging
    {
      $start_timeStamp=strtotime($start_timeStamp);
      $end_timeStamp=strtotime($end_timeStamp);
      
      $timeDiff_seconds=$end_timeStamp-$start_timeStamp;
      // $timeDiff_hours = floor($timeDiff_seconds /60 /60);
      $interval_seconds = $timeDiff_seconds / $intervals;

      $timestamp_Array=null;// timestamp array of intervals
      $ymd_Array=null;// YearMonthDay format array of intervals
      $readable_Array=null;// easy to read format; to be used on the xAxis on the graph
      for($i=0; $i<=$intervals ; $i++):
        $next_timeStamp=  $start_timeStamp + ($i * $interval_seconds);
        $timestamp_Array[] = $next_timeStamp;
        $ymd_Array[] =  date('Y-m-d H:i:s', $next_timeStamp);
        $readable_Array[] = date('ga, d M', $next_timeStamp);
      endfor;

      // echo "<pre>";
      // var_dump($ymd_Array);         // <-A
      // var_dump($timestamp_Array);
      // foreach ($ymd_Array as $ymd) 
      //   var_dump(strtotime($ymd));   // <-B
      // echo "</pre>";                  // as A matches B, we can say strtotime() converts 'Y-m-d H:i:s' back to timestamp with loss of less than 1 second each time

      $start_range = $readable_Array[0];
      $end_range = $readable_Array[count($readable_Array) -2]; //2nd last element as the las one is onf next day. for e.g midnight13may tomidnight14thmay, so we take 23hrs13thmay as $end_range

      $start_explode = explode(" ",$start_range);
      $end_explode = explode(" ",$end_range);
      $start_date = $start_explode[1].$start_explode[2];
      $end_date = $end_explode[1].$end_explode[2];
      // var_dump($start_explode);echo "<br>";
      // var_dump($start_explode[1]);
      // die;
      if ( $start_date === $end_date) //if its for one day only
       {
        $count_readable_Array= count($readable_Array);
        for ($i=0; $i < $count_readable_Array ; $i++) 
        {
            $exploded_hour = explode(",", $readable_Array[$i]);
            $readable_Array[$i] = $exploded_hour[0];
        }
      }


      $returnArray = array($timestamp_Array, $ymd_Array, $readable_Array);
      /*
      _DataStrucue Map_
      $returnArray


      array=>
      {
        array=>{ [timeStamp1]                 ,[timeStamp2]                 ,  ... [timeStamp5]                  }
        array=>{ ['Y-m-d H:i:s' of timeStamp1],['Y-m-d H:i:s' of timeStamp2],  ... ['Y-m-d H:i:s' of timeStamp5] }    
        array=>{ ['m d' of timeStamp1],        ['m d' of timeStamp2],          ... ['m d' of timeStamp5]         }    
      }
      */


      return $returnArray;
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
        // echo "<pre>";
        // var_dump($table_sql);die;
        // echo "</pre>";
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