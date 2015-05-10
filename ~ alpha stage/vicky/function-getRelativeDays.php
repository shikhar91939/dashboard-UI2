<?php 
$arr = array(  0=> "2015-05-10 00:00:00",  1=> "2015-05-10 00:00:00",  2=> "2015-05-10 00:00:00",  3=> "2015-05-10 00:00:00",  4=> "2015-05-10 00:00:00",  5=> "2015-05-10 00:00:00");
$arr2 = array(  0=> "2015-05-10 00:00:00",  1=> "2015-05-09 00:00:00",  2=> "2015-05-08 00:00:00",  3=> "2015-05-7 00:00:00",  4=> "2015-04-10 00:00:00",  5=> "2015-03-10 00:00:00");

var_dump(getRelativeDays($arr2));
// getRelativeDays($arr);

function getRelativeDays($inputArray)
{
  date_default_timezone_set('Asia/Kolkata');
  $today_ymd = date("Y-m-d", strtotime("now"));
  $yesterday_ymd = date("Y-m-d", strtotime("yesterday"));
  $dayBefYest_ymd = date("Y-m-d", strtotime("- 2 days"));


  $returnArray= array();

  foreach ($inputArray as $value)
  {
    $date = explode(" ", $value)[0];
    echo "comparing". "$date" ."\n";
    if ($date === $today_ymd)
    {
      echo " in today";
      $returnArray[] = "Today";
    }
    elseif ($date === $yesterday_ymd) 
    {
      $returnArray[] = "Yesterday" ;
    }
    // elseif ($date === $dayBefYest_ymd)
    // {
    //   $returnArray[] = "Day Bef Yest";
    // }
    else
    {
      $exploded = explode("-", $date);
      $day = $exploded[2];
      $month = $exploded[1];

      switch ($month) 
      {
        case '01': $month = "Jan";break;
        case '02': $month ="Feb" ;   break;
        case '03': $month ="March" ;   break;
        case '04': $month ="Apr" ;   break;
        case '05': $month ="May" ;   break;
        case '06': $month ="June" ;   break;
        case '07': $month ="July" ;   break;
        case '08': $month ="Aug" ;   break;
        case '09': $month ="Sept" ;   break;
        case '10': $month ="Oct" ;   break;
        case '11': $month ="Nov" ;   break;
        case '12': $month ="Dec" ;   break;
      }
      $returnArray[] = $day. " ". $month;
    }

  }
  return $returnArray;
}