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
      // log_message('error', 'entered getData_staticElements() now calling data_notCCmetrics()'); //mylog
      $responseArray['data_notCCmetrics'] = $this->data_notCCmetrics();
      // log_message('error', 'returned from data_notCCmetrics(), result stored in responseArray'); //mylog
      // log_message('error', 'responseArray:'); //mylog
      // log_message('error', var_export($responseArray,true)); //mylog

      $start_ymd = date('Y-m-d',strtotime('first day of this month'));
      $end_ymd = date('Y-m-d',strtotime('today'));
        // log_message('error', 'from static,for monthly revenue, calling getCCmetrics($start_ymd, $end_ymd)'); //cmylog1
        // log_message('error', "getCCmetrics($start_ymd, $end_ymd)"); //cmylog1
      $responseArray['CCmetrics'] = $this->getCCmetrics($start_ymd, $end_ymd);

      echo json_encode($responseArray);
    }

    public function data_notCCmetrics($thisMonthsTarget = 14000000)
    {
      /*
      Get all data related to the SOAP API function called here 

      edit:
      this function was made to get (all the static data (not dependant on the dateSalector)) the whole 
      mmonth's orders->cal. confirmed orders this month and %revenue ingauge
      But then we had to match it to cc metrics so month's data is not taken from here.
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

      $percent_sameDayShips= $count_CSconfirmed==0 ? 0 : $count_sameDayShips /$count_CSconfirmed *100; // ? : ; prevents the php notice 'can't devide by zero'
      $percent_CSconfirmed= $count_todaysOrders==0 ? 0 :  $count_CSconfirmed / $count_todaysOrders *100;
      $percent_CScancelled = $count_todaysOrders==0 ? 0 :  $count_CScancelled/ $count_todaysOrders *100;
      // $thisMonthsTarget = 12000000;   // moved to arguement of this function
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
      $response_combined = array();

      date_default_timezone_set('Asia/Kolkata');

      // log_message('error', 'entered submitRange'); //mylog

      $start_ymd=$this->input->post('start'); // $start_ymd ->  startTime in YearMonthDay
      $end_ymd=$this->input->post('end');     //  || for $end_ymd

    // log_message('error','recieved this from post as $start_ymd and $end_ymd:'); //mylog
    // log_message('error', var_export($start_ymd,true) ." and ". var_export($end_ymd,true)); //mylog

    $start = str_replace('/','-',$start_ymd);
    $end = str_replace('/','-',$end_ymd);


    // log_message('error', '(remove this)after replacing / with - $start_ymd and $end_ymd:'); //mylog
    // log_message('error', "$start_ymd and $end_ymd"); //mylog

    // $start = "2015-05-01";
    // $end = "2015-05-05";

    $date_interval_obj = date_diff(date_create($start), date_create($end));// date_diff() works in yyyy-mm--dd . e.g. 2013-03-15
    // var_dump($date_interval_obj->format("%a")); // format("%R%a") will give string(2) "+4"   --where--   format("%a") gives string(1) "4"
    $date_interval = $date_interval_obj->format("%a");

    $start = $this->dateFormatter($start);
    $end = $this->dateFormatter($end);
    // log_message('error', "called dateFormatter() for $start and $end" ); //mylog
    // log_message('error', "start_ymd=$start_ymd and end_ymd=$end_ymd"); //mylog

    $start_comparison = date('Y-m-d',strtotime($start . "-$date_interval days"));


    $response_x = array('start'=> $start_ymd, 'end' => $end_ymd, 'start_comparison' => $start_comparison, 'interval'=> $date_interval);
    // log_message('error', 'delete $response_x, unused variable' ); //mylog    
    // echo json_encode($response_x);
    // $response_combined = array("mis_box"=> $response_x , "two"=>"two_val");
    // echo "<pre>";
    // var_dump($response_combined);die;
    // var_dump(json_encode($response_combined));die;
    // echo "</pre>";


    $dateRange_readable = $this->getDisplayDate($start_ymd, $end_ymd);

    $response_combined['dateRange'] = $dateRange_readable;
    // log_message('error', "added dateRange_readable $dateRange_readable"); //mylog


    $response_mis = $this->getMISdata($start_comparison, $start_ymd, $end_ymd);
    // $response_combined = array("mis_box"=> $response_mis , "two"=>"two_val");
    $response_combined["mis_box"] = $response_mis;
    // log_message('error', "added misData to response_combined"); //mylog
    // log_message('error', var_export($response_combined,true)); //mylog

    $response_qc = $this->getQCdata($start_comparison, $start_ymd, $end_ymd);
    $response_combined["qc_box"] = $response_qc;
    // log_message('error', "added qc data to response_combined"); //mylog
    // log_message('error', var_export($response_combined,true)); //mylog


    // log_message('error', "formatting dates for calling getSalesData() for graph data"); //mylog

    $today = date("Y-m-d",strtotime("today"));
    $yesterday  = date("Y-m-d",strtotime("yesterday"));
    // log_message('error', "for comparison, today=$today and yesterday=$yesterday"); //mylog


    // log_message('error', '"From submitDateRange, Calling function getCCmetrics($start_ymd, $end_ymd):"'); //mylog1
    // log_message('error', "getCCmetrics($start_ymd, $end_ymd):"); //mylog1
    $response_CCmetrics = $this->getCCmetrics($start_ymd, $end_ymd); //getting exct same data as in CC metrics
      // log_message('error', '"returned from getCCmetrics() with $response_CCmetrics:"'); //cmylog
      // log_message('error', var_export($response_CCmetrics,true)); //cmylog
    $response_combined['response_CCmetrics'] = $response_CCmetrics;


/*
getSalesData() called below and json echo'ed. getSalesData() is the function I initially made 
this data (in $response_sales) is being sent in json form but not being used. Remove this once CCmetrics function is ready
*/
    if ($start_ymd == $today) 
    {
      // log_message('error', 'inside if clause, $start_ymd == $today =true'); //mylog
      // log_message('error', "start=$start_ymd and today=$today "); //mylog
      
      // log_message('error', 'calling getSalesData() with no arguements'); //mylog
      $response_sales = $this->getSalesData(); //the function getSalesData() takes today by default
      // log_message('error', 'back from getSalesData()'); //mylog
      $response_combined["sales_graph"] = $response_sales;
      // log_message('error', 'response_combined:'); //mylog
      // log_message('error', var_export($response_combined,true)); //mylog
    }
    else
    {
      if ($start_ymd === $end_ymd)
      {
        // log_message('error', 'did not enter "if ($start_ymd == $today)" '); //mylog
        // log_message('error', 'inside if clause, "if($start_ymd === $end_ymd)"'); //mylog
        // log_message('error', "start=$start_ymd and today=$today "); //mylog
        // log_message('error','$start_ymd === $end_ymd ('.$start_ymd.'). Thus entered if');
        // log_message('debug',var_export($start_ymd,true));
        $end_ymd = date("Y-m-d",strtotime("+1 day",strtotime($start_ymd))); // made this correction as chosing "yesterday" gives (yesterday 00:00:00) to (yesterday 00:00:00)
        // log_message('error', '1 day added to $end_ymd'); //mylog
        // log_message('error', "end_ymd = $end_ymd"); //mylog
      }else 
      {
        // log_message('error', 'did not enter "if ($start_ymd == $today)" '); //mylog
        // log_message('error', 'did not enter "if($start_ymd === $end_ymd)" '); //mylog
        // log_message('error', "start=$start_ymd and today=$today "); //mylog
      }

      $from = $start_ymd. " 00:00:00";
      $to = $end_ymd. " 00:00:00";

      // log_message('error', ' " 00:00:00" appended to both these:'); //mylog
      // log_message('error', "from = $from and to = $to"); //mylog



      // $from = "2015-07-10 00:00:00";// debuging only

      // if ($end_ymd == $today && $start_ymd == $yesterday) {
      //   # code... }
      // log_message('error', 'calling getSalesData($from, $to) ='. "getSalesData($from, $to)"); //mylog
      $response_sales = $this->getSalesData($from, $to);
      // log_message('error', 'response_sales: '); //mylog
      // log_message('error', var_export($response_sales,true)); //mylog
      $response_combined["sales_graph"] = $response_sales;
      // echo json_encode($response_sales);die;
    }

  // log_message('error', 'echoing json response:' ); //mylog
  // log_message('error', var_export(json_encode($response_combined),true) ); //mylog


    echo json_encode($response_combined);
    }

    public function getCCmetrics($start_ymd, $end_ymd)
    {
      // date_default_timezone_set("UTC"); //this is the time zone ccMetrics runs on

      // log_message('error', 'entered "getCCmetrics($start_ymd, $end_ymd):"'); //cmylog
      // log_message('error', "getCCmetrics($start_ymd, $end_ymd):"); //cmylog

      $from = $start_ymd .' 00:00:00';
      $to = $end_ymd ." 23:59:59" ;
      // log_message('error', "was using this for SoapClient earier: from= $from, to=$to):"); //mylog1

      $from = date('Y-m-d H:i:s', strtotime('-330 minutes',strtotime($from)));
      $to = date('Y-m-d H:i:s', strtotime('-330 minutes',strtotime($to)));
       
      // log_message('error', "default timezone: ".date_default_timezone_get()); //mylog1
      // log_message('error', "calling SoapClient. from= $from, to=$to):"); //mylog1
      try
      {
        $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
        $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');

        $params = array('complex_filter'=>
          array(
              array('key'=>'created_at','value'=>array('key' =>'from','value' => $from)), 
              array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => $to))
            )
        );
        $ordersList = $client->salesOrderList($session,$params);
      } catch (SoapFault $fault) {trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR); }

      // log_message('error', "OrderList:"); //cmylog
      // log_message('error', var_export($ordersList,true)); //

      // foreach ($ordersList as $order)//debugging
      // {
      //   $increment_id = $order->increment_id;
      //   $created_at= strtotime($order->created_at);
      //   // $ist = strtotime(" + 330 minutes", $created_at) ;
      //   $order_time_utc = date('Y-m-d H:i:s', $created_at) ;
      //   $ist = date('Y-m-d H:i:s',strtotime(" + 330 minutes", $created_at)) ; //ist time is not being used
      //   // not being used
      //   // $orderValue = $order->grand_total;
      //   // $state = $order->state;
      //   // $status = $order->status;
      // log_message('error', "".$increment_id.": ". ", at ". $order_time_utc. ', ist '.$ist ); //cmylog
      // }

        /*
        Exact copy of CC metrics below
        */
        $canceled_orders = 0;
        $duplicate_order = 0;        
        $fake_orders = 0;
        $pending_orders = 0;
        $canceled_revenue = 0;
        $total_revenue = 0;
        $pending_revenue=0;
        $total_orders = count($ordersList);

        foreach ($ordersList as $order) 
        { 
          $increment_id = $order->increment_id;
          $created_at= strtotime($order->created_at);
          $order_time_utc = date('Y-m-d H:i:s', $created_at) ;
          $ist = date('Y-m-d H:i:s',strtotime(" + 330 minutes", $created_at)) ; //ist time is not being used
          // log_message('error', "".$increment_id.": ". ", at ". $order_time_utc. ', ist '.$ist."->" ); //cmylog

          if($order->status=='canceled')
          {
            // log_message('error', "cancelled,"); //cmylog
            $canceled_orders++;
            $canceled_revenue += $order->grand_total;
          }
          // if($order['sel_cancelled_val']=='Fake order')
          // {
          //   $fake_orders++;
          // }
          // if($order['sel_cancelled_val']=='Duplicate order')
          // {
          //   $duplicate_order++; 
          // }
          if($order->status=='pending' || $order->status == 'pending_payment')
          {
            // log_message('error', "pending(/payment),"); //cmylog
            $pending_orders++;
            $pending_revenue += $order->grand_total;
          }
          $total_revenue +=  $order->grand_total;

          // log_message('error', "\n"); //cmylog
       }

       $cancel_rev_percentage = number_format(round(($canceled_revenue/intval($total_revenue))*100));
       $pending_rev_percentage = number_format(round(($pending_revenue/intval($total_revenue))*100));
       $confirmed_revenue = $total_revenue - ($canceled_revenue + $pending_revenue);
       $confirmed_rev_percentage = number_format(round(($confirmed_revenue/intval($total_revenue))*100));
       $confirmed_revenue = number_format(round(($total_revenue - ($canceled_revenue + $pending_revenue))));
       $total_revenue = number_format(round($total_revenue));
       $canceled_revenue = number_format(round($canceled_revenue));
       $pending_revenue = number_format(round($pending_revenue));
       $confirmed_orders = $total_orders - $canceled_orders - $pending_orders;
       $confirmed_percentage = number_format(round(($confirmed_orders/$total_orders)*100));
       $canceled_percentage = number_format(round(($canceled_orders/$total_orders)*100));
       $duplicate_percentage = number_format(round(($duplicate_order/$total_orders)*100));
       $fake_percentage = number_format(round(($fake_orders/$total_orders)*100));
       $pending_percentage = number_format(round(($pending_orders/$total_orders)*100));

       //formatting before display:
       $confirmed_revenue = str_replace(',','',$confirmed_revenue);
       $confirmed_revenue = $this->moneyFormatIndia($confirmed_revenue);
       // log_message('error', "confirmed_revenue, = $confirmed_revenue"); //cmylog1


      // log_message('error', "final valvues: "); //cmylog

      // log_message('error' ,'$report_date= '. $report_date);//cmylog
      // log_message('error' ,'$total_orders= '. $total_orders);//cmylog
      // log_message('error' ,'$confirmed_orders= '. $confirmed_orders);//cmylog
      // log_message('error' ,'$confirmed_percentage= '. $confirmed_percentage);//cmylog
      // log_message('error' ,'$canceled_orders= '. $canceled_orders);//cmylog
      // log_message('error' ,'$canceled_percentage= '. $canceled_percentage);//cmylog
      // log_message('error' ,'$duplicate_order= '. $duplicate_order);//cmylog
      // log_message('error' ,'$duplicate_percentage= '. $duplicate_percentage);//cmylog
      // log_message('error' ,'$fake_orders= '. $fake_orders);//cmylog
      // log_message('error' ,'$fake_percentage= '. $fake_percentage);//cmylog
      // log_message('error' ,'$pending_orders= '. $pending_orders);//cmylog
      // log_message('error' ,'$pending_percentage= '. $pending_percentage);//cmylog
      // log_message('error' ,'$total_revenue= '. $total_revenue);//cmylog
      // log_message('error' ,'$canceled_revenue= '. $canceled_revenue);//cmylog
      // log_message('error' ,'$cancel_rev_percentage= '. $cancel_rev_percentage);//cmylog
      // log_message('error' ,'$confirmed_revenue= '. $confirmed_revenue);//cmylog
      // log_message('error' ,'$confirmed_rev_percentage= '. $confirmed_rev_percentage);//cmylog
      // log_message('error' ,'$pending_revenue= '. $pending_revenue);//cmylog
      // log_message('error' ,'$pending_rev_percentage= '. $pending_rev_percentage);//cmylog
      // log_message('error', "total_revenue= ". $total_revenue); //cmylog

      $returnArray = array('confirmed_orders'=>$confirmed_orders, 'confirmed_rev_percentage'=>$confirmed_rev_percentage, 'confirmed_revenue'=>$confirmed_revenue,
        'confirmed_percentage'=>$confirmed_percentage, 'canceled_percentage'=>$canceled_percentage, 'pending_percentage'=>$pending_percentage);
      return $returnArray;
    }
    public function getDisplayDate($start_ymd, $end_ymd)
    {
    
      // log_message('error', 'entered getDisplayDate($start_ymd, $end_ymd):' ); //mylog
      // log_message('error', "'entered getDisplayDate($start_ymd, $end_ymd):'" ); //mylog

      if ($start_ymd === $end_ymd) 
      {
        $returnDate = date('M d, Y',strtotime($start_ymd));
        return $returnDate;
      }

      return "Date";
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

    public function onChange_targetRevenue()
    {
      $newTarget/*="20,00,000";//*/=$this->input->post('newTarget'); //debuging

      $newTarget_exploded = $this->multiexplode(array(',',' '),$newTarget);
      $newTarget = implode("", $newTarget_exploded);

      if (!is_numeric($newTarget)) 
      {
        echo json_encode(array('inputIsNumber'=>false));
        return;
      }
      $newTarget = $newTarget + 0; //convert it to a number

      $resultArray = $this->data_notCCmetrics($newTarget);
      $percent_newTarget = $resultArray['percent_monthlySalesTarget'];

      echo json_encode(array("percent_newTarget"=>$percent_newTarget,'inputIsNumber'=>true));
    }

    public function multiexplode ($delimiters,$string) 
    {
      $ready = str_replace($delimiters, $delimiters[0], $string);
      $launch = explode($delimiters[0], $ready);
      return  $launch;
    }

    public function getSalesData($start_ymd =null , $end_ymd =null , $divisions_xAxis =24)
    {
      date_default_timezone_set('Asia/Kolkata');

      // log_message('error', " entered getSalesData() with ($start_ymd , $end_ymd , $divisions_xAxis)" ); //mylog

      if (is_null($start_ymd) && is_null($end_ymd))  
      {
        // log_message('error', "start_ymd and end_ymd are null. entered if clause" ); //mylog
        $start_ymd = date('Y-m-d H:i:s',strtotime("today"));
        $end_ymd = date('Y-m-d H:i:s',strtotime(" midnight tomorrow"));
        // log_message('error', 'exiting if with $start_ymd and $end_ymd as '."$start_ymd and $end_ymd" ); //mylog
      }
      else
      {        
        // log_message('error', "start_ymd and end_ymd NOT null. did not enter if clause" ); //mylog
      }
      
      $start_ymd_timeStamp = strtotime($start_ymd);
      $end_ymd_timeStamp = strtotime($end_ymd);
      // log_message('error', "start_ymd:" ); //mylog
      // log_message('error', var_export($start_ymd_timeStamp,true) ); //mylog

      // log_message('error', 'calling splitTimeRange( $start_ymd_timeStamp, $end_ymd_timeStamp, $divisions_xAxis) =' ); //mylog
      // log_message('error', "splitTimeRange( $start_ymd_timeStamp, $end_ymd_timeStamp, $divisions_xAxis)" ); //mylog

      $timeSplits = $this->splitTimeRange( $start_ymd_timeStamp, $end_ymd_timeStamp, $divisions_xAxis);

      // log_message('error', 'returned from splitTimeRange(). the response $timeSplits=' ); //mylog
      // log_message('error', var_export($timeSplits,true) ); //mylog

      $returnArray['xAxis'] = $timeSplits[2];

      // log_message('error', '$returnArray[\'xAxis\'] assigned value:' ); //mylog
      // log_message('error', var_export($timeSplits[2],true) ); //mylog
      // log_message('error', '$returnArray now:' ); //mylog
      // log_message('error', var_export($returnArray,true) ); //mylog


      // log_message('error', "xAxis reurned from splitTimeRange function:" );
      // log_message('error', var_dump($timeSplits));

      // echo "<pre>";
      // var_dump($returnArray);die;

      try 
      {
        $client = new SoapClient('http://www.overcart.com/index.php/api/v2_soap?wsdl');
        $session = $client->login('dashbaord', 'jn0ar9t6j2cysb9lywbwk0bimft9l1ce');

      // log_message('error', "calling SOAP\'s salesOrderList with start_ymd=$start_ymd  and end_ymd=$end_ymd" ); //mylog
        $params = array('complex_filter'=>
          array(
              array('key'=>'created_at','value'=>array('key' =>'from','value' => $start_ymd)),
              array('key'=>'created_at', 'value'=>array('key' => 'to', 'value' => $end_ymd))
            )
        );
        $ordersList = $client->salesOrderList($session,$params);
      // log_message('error', 'SOAp\'s result, ordersList:' ); //my.log
      // log_message('error', var_export($ordersList,true) ); //my.log
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

        // log_message('error', 'all variables initialized, running foreach loop' ); //mylog
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
            // echo "<hr>";
            // echo "interval_number: ".$interval_number ."<br>";

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

      // log_message('error', 'foreach ends' ); //mylog
      // log_message('error', 'now, orderTable:' ); //mylog
      // log_message('error', var_export($orderTable,true) ); //mylog
      // log_message('error', 'running while to fill remaining intervals' ); //mylog

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
            $pendingAmount = 0;
            $cancelledAmount = 0;
            $confirmedAmount = 0;
            $totalOrderAmount = 0;
        }
      // log_message('error', 'while ends' ); //mylog
      // log_message('error', 'now, orderTable:' ); //mylog
      // log_message('error', var_export($orderTable,true) ); //mylog

        $returnArray['sales_distribution'] = $orderTable;

        foreach ($orderTable as $intervalArray) 
        {
          // var_dump($intervalArray); 
          // echo "confirmed number:"; var_dump($intervalArray['confirmedAmount']);echo "<br>";
          // echo "confirmed amount:"; var_dump($intervalArray['confirmedCount']);echo "<br>";
          $confirmedAmt_totalRange += $intervalArray['confirmedAmount'];
          $totalConfirmed_count += $intervalArray['confirmedCount'];
        }
      // log_message('error', 'looping through orderTable for $confirmedAmt_totalRange and $totalConfirmed_amount' ); //mylog
      // log_message('error', '$confirmedAmt_totalRange: ' ); //mylog
      // log_message('error', var_export($confirmedAmt_totalRange,true) ); //mylog
      // log_message('error', '$totalConfirmed_amount: ' ); //mylog
      // log_message('error', var_export($totalConfirmed_amount,true) ); //mylog

        $confirmedAmt_totalRange = $this->moneyFormatIndia($confirmedAmt_totalRange);
        $returnArray['confirmedAmt_totalRange']= $confirmedAmt_totalRange;
        $returnArray['totalConfirmed_count']= $totalConfirmed_count;
    
      // log_message('error', 'returning from function, $returnArray: ' ); //mylog
      // log_message('error', var_export($returnArray,true) ); //mylog

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
        // log_message('error', 'ENCOUNTERED SOAP FAULT' ); //mylog
        }      
    }

    public function seconds_to_hms($seconds) 
    {
      $t = round($seconds);
      return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }

    function splitTimeRange($start_timeStamp = null, $end_timeStamp = null, $intervals='24') //remove all 3 default values after debugging
    {
      // log_message('error', 'entered splitTimeRange($start_timeStamp, $end_timeStamp, $intervals) with these arguments: '); //mylog
      // log_message('error', "($start_timeStamp, $end_timeStamp, $intervals)"); //mylog

      if (is_null($start_timeStamp) || is_null($end_timeStamp))      
      {
        // log_message('error', '$start_timeStamp'."=$start_timeStamp, end_timeStamp=$end_timeStamp"); //mylog
        $today_debug = date('Y-m-d',strtotime('now'));
        $yest_debug  = date('Y-m-d', strtotime('yesterday'));
        $start_timeStamp=strtotime($today_debug);
        $end_timeStamp=strtotime($yest_debug);

        // log_message('error', '"($start_timeStamp, $end_timeStamp, $intervals)" are now:'); //mylog
        // log_message('error', "($start_timeStamp, $end_timeStamp, $intervals)"); //mylog
      }
      
      $timeDiff_seconds=$end_timeStamp-$start_timeStamp;
      $interval_seconds = $timeDiff_seconds / $intervals;
      // log_message('error', "calculated timeDiff_seconds and interval_seconds"); //mylog
      // log_message('error', '$timeDiff_seconds='.$this->seconds_to_hms($timeDiff_seconds)); //mylog
      // log_message('error', ' $interval_seconds='.$this->seconds_to_hms($interval_seconds)); //mylog


      $timestamp_Array=null;// timestamp array of intervals
      $ymd_Array=null;// YearMonthDay format array of intervals
      $readable_Array=null;// easy to read format; to be used on the xAxis on the graph
      for($i=0; $i<=$intervals ; $i++):
        $next_timeStamp=  $start_timeStamp + ($i * $interval_seconds);
        $timestamp_Array[] = $next_timeStamp;
        $ymd_Array[] =  date('Y-m-d H:i:s', $next_timeStamp);
        $readable_Array[] = date('ga d M y', $next_timeStamp);
      endfor;

      // log_message('error','the 3 arrays created by the for loop:'); //mylog
      // log_message('error','timestamp_Array:');//mylog
      // log_message('error',var_export($timestamp_Array,true));//mylog
      // log_message('error','ymd_Array:');//mylog
      // log_message('error',var_export($ymd_Array,true));//mylog
      // log_message('error',"readable_Array:");//mylog
      // log_message('error',var_export($readable_Array,true));//mylog

      // echo "<pre>";
      // var_dump($ymd_Array);         // <-A
      // var_dump($timestamp_Array);
      // foreach ($ymd_Array as $ymd) 
      //   var_dump(strtotime($ymd));   // <-B
      // echo "</pre>";                  // as A matches B, we can say strtotime() converts 'Y-m-d H:i:s' back to timestamp with loss of less than 1 second 

      // log_message('error','making readble values for xAxis');//mylog
      $start_range = $readable_Array[0];
      $end_range = $readable_Array[count($readable_Array) -2]; //2nd last element as the las one is onf next day. for e.g midnight13may tomidnight14thmay, so we take 23hrs13thmay as $end_range

      $start_explode = explode(" ",$start_range);
      $end_explode = explode(" ",$end_range);
      $start_date = $start_explode[1].$start_explode[2];
      $end_date = $end_explode[1].$end_explode[2];
      // var_dump($start_explode);echo "<br>";
      // var_dump($start_explode[1]);
      // die;
      // log_message('error',' comparing  $end_timeStamp to $start_timeStamp:');//mylog
      // log_message('error',"' comparing $end_timeStamp to $start_timeStamp:'");//mylog
      if (  $end_timeStamp-$start_timeStamp <= (60*60*24)) //if its for one day or less 
       {
        // log_message('error','entered if clause start_date === $end_date is true');//mylog
        $count_readable_Array= count($readable_Array);
        for ($i=0; $i < $count_readable_Array ; $i++) 
        {
            $exploded_hour = explode(" ", $readable_Array[$i]);
            $readable_Array[$i] = $exploded_hour[0];
        }
        // log_message('error',' exiting if clause, $readable_Array:');//mylog
        // log_message('error', var_export($readable_Array,true));//mylog
      }
      else //only temporarily using this. (use the next else afterwords)
         {
        // log_message('error','entered if clause start_date === $end_date is true');//mylog
        $count_readable_Array= count($readable_Array);
        for ($i=0; $i < $count_readable_Array ; $i++) 
        {
            $exploded_hour = explode(" ", $readable_Array[$i]);
            $readable_Array[$i] = $exploded_hour[1]. " ".$exploded_hour[2];
            // $readable_Array[$i] = $i%2!==0 ? null : $exploded_hour[1]. " ".$exploded_hour[2]; // dontDelete
        }
        // log_message('error',' exiting if clause, $readable_Array:');//mylog
        // log_message('error', var_export($readable_Array,true));//mylog
      }
      // else 
      // {
        // log_message('error','entered else clause start_date === $end_date is false');//mylog
      //   $count_readable_Array= count($readable_Array);
      //   for ($i=0; $i < ($count_readable_Array); $i++) //not running the loop for the last element as $readable_Array[$i+1] will give a php notice
      //   { 
      //     //  date('ga d M Y', $next_timeStamp);
      //     $exploded_interval = explode(" ", $readable_Array[$i]);
      //     // log_message('error', 'var_export($exploded_interval,true)');//my.log
      //     // log_message('error', var_export($exploded_interval,true));//my.log

      //     $hour= $exploded_interval[0];
      //     $day= $exploded_interval[1];
      //     $month= $exploded_interval[2];
      //     $year= $exploded_interval[3];

      //     if ($i!=($count_readable_Array-1)) 
      //     {
      //       $exploded_interval_next = explode(" ", $readable_Array[$i+1]);
      //     }else
      //     {// compare last element to the second last element as there is no 'next element'
      //       $exploded_interval_next = explode(" ", $readable_Array[$i-1]);
      //     }
      //     $hour_next= $exploded_interval_next[0];
      //     $day_next= $exploded_interval_next[1];
      //     $month_next= $exploded_interval_next[2];
      //     // var_dump($month_next);die;
      //     // var_dump($exploded_interval_next); echo "<br>";// die;
      //     $year_next= $exploded_interval_next[3];


      //     if ($year !== $year_next)
      //     {
      //       $readable_Array[$i] = $day."".$month." ".$year;
      //     }
      //     elseif ($month !== $month_next) 
      //     {
      //       $readable_Array[$i] = $day." ".$month;
      //     }
      //     elseif ($day !== $day_next) 
      //     {
      //       $readable_Array[$i] = $hour." ".$day;
      //     }
      //     else
      //     {
      //       $readable_Array[$i] = $hour;
      //     }
      //   }//for ends
      // }


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
      // log_message('error','returning from splitTimeRange() with :');//mylog
      // log_message('error',var_export($returnArray,true));//mylog
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
  // var_export($tableForChart_keys,true);
  // var_export($array_hardwareTypes,true);
  // echo "</pre>";die;
  // echo $tableForChart["Accessories BER"];
  // foreach ($tableForChart_keys as $this_key) 
  // {
  //   var_export($tableForChart[$this_ke,truey]);
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



    
    public function logistics()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/logistics';
      $this->load->view('includes/template', $data);  
    }
    public function quality()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/quality';
      $this->load->view('includes/template', $data);  
    }
    public function inventory()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/inventory';
      $this->load->view('includes/template', $data);  
    }
    public function report()
    {
      $data['prevent_css'] = true;
      $data['main_content'] = 'admin/newdashboard/report'; 
      if(isset($_GET['client']) && isset($_GET['brand'])){
            $client = $_GET['client'];
            $brand = $_GET['brand'];}
      else{
          $client = null;
          $brand = null;}
      
      //$this->load->model('speed_metrics_model');
      $data['records'] = $this->speed_metrics_model->getSpeedMetrics($client, $brand);
      //var_dump($data);die;
      if(isset($_GET['client']) || isset($_GET['brand'])) 
        echo json_encode($data);
      else 
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