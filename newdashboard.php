<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Overboxd: An Overcart Recommerce Product For Businesses</title>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css">
<link href="<?php echo base_url(); ?>assets/css/template/jquery.comiseo.daterangepicker.css" rel="stylesheet" type="text/css">

<link href="<?php echo base_url(); ?>assets/css/template/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/template/pixel-admin.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/template/themes.min.css" rel="stylesheet" type="text/css">



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/template/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/template/jquery.comiseo.daterangepicker.js"></script>

<script src="<?php echo base_url(); ?>assets/js/template/amExtract/amcharts/amcharts.js" type="text/javascript"></script><!-- bar chart -->
<script src="<?php echo base_url(); ?>assets/js/template/amExtract/amcharts/serial.js" type="text/javascript"></script><!-- bar chart -->
<script type="text/javascript" src="http://www.amcharts.com/lib/3/amcharts.js"></script><!-- stacked -->
<script type="text/javascript" src="http://www.amcharts.com/lib/3/serial.js"></script><!-- stacked -->
<script type="text/javascript" src="http://www.amcharts.com/lib/3/themes/none.js"></script><!-- stacked -->

<script src="http://code.highcharts.com/highcharts.js"></script><!-- highCharts: stacked graph -->
<script src="http://code.highcharts.com/modules/exporting.js"></script><!-- highCharts: stacked graph -->
<!-- <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div> --><!-- highCharts: stacked graph -->


<script>
        $(function() { $("#e1").daterangepicker(); });
</script>
</head>
<body class="theme-default main-menu-animated">
<div id="main-wrapper"> 

  
  <!-- 2. $MAIN_NAVIGATION ===========================================================================

  Main navigation
-->
  <div id="main-navbar" class="navbar navbar-inverse" role="navigation"> 
    <!-- Main menu toggle --> 
    <!--<button type="button" id="main-menu-toggle"><i class="navbar-icon fa fa-bars icon"></i><span class="hide-menu-text">HIDE MENU</span></button>-->
    <div class="navbar-inner"> 
      <!-- Main navbar header -->
      <div class="navbar-header"> 
        
        <!-- Logo --> 
        <a href="../../index.html" class="navbar-brand"> <img src="<?php echo base_url(); ?>assets/images/template/logo.png"/> </a> 
        
        <!-- Main navbar toggle -->
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>
      </div>
      <!-- / .navbar-header -->
      
      <div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
        <div> 
          
          <!-- / .navbar-nav -->
          
          <div class="right clearfix">
            <div class="header_search">
              <form class="navbar-form pull-left">
                <input type="text" class="form-control" placeholder="Enter Keyword">
                <button class="search_btn" type="submit" value="submit"></button>
              </form>
            </div>
            <ul class="nav navbar-nav pull-right right-navbar-nav">
              

              <li class="nav-icon-btn nav-icon-btn-danger dropdown">
              <a href="#notifications" class="dropdown-toggle"> <img src="<?php echo base_url(); ?>assets/images/template/notifications.png"/> <span class="small-screen-text">Notifications</span> </a>
               <!--<a href="#notifications" class="dropdown-toggle" data-toggle="dropdown"> <img src="<?php echo base_url(); ?>assets/images/template/notifications.png"/> <span class="small-screen-text">Notifications</span> </a>--> 
                
                <!-- NOTIFICATIONS --> 
                
                <!-- Javascript --> 
                <script>
                  init.push(function () {
                    $('#main-navbar-notifications').slimScroll({ height: 250 });
                  });
                </script> 
                <!-- / Javascript -->
                
                <div class="dropdown-menu widget-notifications no-padding" style="width: 300px">
                  <div class="notifications-list" id="main-navbar-notifications">
                    <div class="notification">
                      <div class="notification-title text-danger">SYSTEM</div>
                      <div class="notification-description"><strong>Error 500</strong>: Syntax error in index.php at line <strong>461</strong>.</div>
                      <div class="notification-ago">12h ago</div>
                      <div class="notification-icon fa fa-hdd-o bg-danger"></div>
                    </div>
                    <!-- / .notification -->
                    
                    <div class="notification">
                      <div class="notification-title text-info">STORE</div>
                      <div class="notification-description">You have <strong>9</strong> new orders.</div>
                      <div class="notification-ago">12h ago</div>
                      <div class="notification-icon fa fa-truck bg-info"></div>
                    </div>
                    <!-- / .notification -->
                    
                    <div class="notification">
                      <div class="notification-title text-default">CRON DAEMON</div>
                      <div class="notification-description">Job <strong>"Clean DB"</strong> has been completed.</div>
                      <div class="notification-ago">12h ago</div>
                      <div class="notification-icon fa fa-clock-o bg-default"></div>
                    </div>
                    <!-- / .notification -->
                    
                    <div class="notification">
                      <div class="notification-title text-success">SYSTEM</div>
                      <div class="notification-description">Server <strong>up</strong>.</div>
                      <div class="notification-ago">12h ago</div>
                      <div class="notification-icon fa fa-hdd-o bg-success"></div>
                    </div>
                    <!-- / .notification -->
                    
                    <div class="notification">
                      <div class="notification-title text-warning">SYSTEM</div>
                      <div class="notification-description"><strong>Warning</strong>: Processor load <strong>92%</strong>.</div>
                      <div class="notification-ago">12h ago</div>
                      <div class="notification-icon fa fa-hdd-o bg-warning"></div>
                    </div>
                    <!-- / .notification --> 
                    
                  </div>
                  <!-- / .notifications-list --> 
                  <a href="#" class="notifications-link">MORE NOTIFICATIONS</a> </div>
                <!-- / .dropdown-menu --> 
              </li>
              <li class="nav-icon-btn nav-icon-btn-success dropdown"> 
              <a href="#messages" class="dropdown-toggle"> <img src="<?php echo base_url(); ?>assets/images/template/setting.png"/> <span class="small-screen-text">Setting</span> </a>
              <!--<a href="#messages" class="dropdown-toggle" data-toggle="dropdown"> <img src="<?php echo base_url(); ?>assets/images/template/setting.png"/> <span class="small-screen-text">Setting</span> </a>--> 
                
                <!-- MESSAGES --> 
                
                <!-- Javascript --> 
                <script>
                  init.push(function () {
                    $('#main-navbar-messages').slimScroll({ height: 250 });
                  });
                </script> 
                <!-- / Javascript -->
                
                <div class="dropdown-menu widget-messages-alt no-padding" style="width: 300px;">
                  <div class="messages-list" id="main-navbar-messages">
                    <div class="message"> <img src="../../assets/demo/avatars/2.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a>
                      <div class="message-description"> from <a href="#">Robert Jang</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/3.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</a>
                      <div class="message-description"> from <a href="#">Michelle Bortz</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/4.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet.</a>
                      <div class="message-description"> from <a href="#">Timothy Owens</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/5.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</a>
                      <div class="message-description"> from <a href="#">Denise Steiner</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/2.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet.</a>
                      <div class="message-description"> from <a href="#">Robert Jang</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/2.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a>
                      <div class="message-description"> from <a href="#">Robert Jang</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/3.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</a>
                      <div class="message-description"> from <a href="#">Michelle Bortz</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/4.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet.</a>
                      <div class="message-description"> from <a href="#">Timothy Owens</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/5.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</a>
                      <div class="message-description"> from <a href="#">Denise Steiner</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message -->
                    
                    <div class="message"> <img src="../../assets/demo/avatars/2.jpg" alt="" class="message-avatar"> <a href="#" class="message-subject">Lorem ipsum dolor sit amet.</a>
                      <div class="message-description"> from <a href="#">Robert Jang</a> &nbsp;&nbsp;·&nbsp;&nbsp;
                        2h ago </div>
                    </div>
                    <!-- / .message --> 
                    
                  </div>
                  <!-- / .messages-list --> 
                  <a href="#" class="messages-link">MORE MESSAGES</a> </div>
                <!-- / .dropdown-menu --> 
              </li>
              <!-- /3. $END_NAVBAR_ICON_BUTTONS -->
              
              <li class="dropdown"> <a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown"> <img src="<?php echo base_url(); ?>assets/images/template/3.jpg"/> <span>John Doe</span> </a>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="label label-warning pull-right">New</span>Profile</a></li>
                  <li><a href="#"><span class="badge badge-primary pull-right">New</span>Account</a></li>
                  <li><a href="#"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;Settings</a></li>
                  <li class="divider"></li>
                  <li><a href="../../pages-signin.html"><i class="dropdown-icon fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a></li>
                </ul>
              </li>
            </ul>
            <!-- / .navbar-nav --> 
          </div>
          <!-- / .right --> 
        </div>
      </div>
      <!-- / #main-navbar-collapse --> 
    </div>
    <!-- / .navbar-inner --> 
  </div>
  <!-- / #main-navbar --> 
  <div id="main-menu" role="navigation">
    <div id="main-menu-inner">
      <ul class="navigation">
        <li class="active"> <a href="#"><i class="menu-icon fa fa-dashboard"></i><span class="mm-text">Dashboard</span><span class="label label-new">1127</span></a> </li>
        <li> <a href="#"><i class="menu-icon fa fa-clock-o"></i><span class="mm-text">Logistics</span></a> </li>
        <li> <a href="../../stat-panels.html"><i class="menu-icon fa fa-bolt"></i><span class="mm-text">Support</span></a> </li>
        <li> <a href="../../widgets.html"><i class="menu-icon fa fa-envelope-o"></i><span class="mm-text">Quality</span></a> </li>
        <li> <a href="#"><i class="menu-icon fa fa fa-calendar"></i><span class="mm-text">Notifications</span><span class="label label-new">16</span></a></li>
        <li> <a href="#"><i class="menu-icon fa fa-user"></i><span class="mm-text">Contacts</span></a></li>
        <li> <a href="../../tables.html"><i class="menu-icon fa fa-gear"></i><span class="mm-text">Setting</span></a> </li>
        <li> <a href="../../charts.html"><i class="menu-icon fa-sign-out"></i><span class="mm-text">Logout</span></a> </li>
      </ul>
      <!-- / .navigation --> 
      
    </div>
    <!-- / #main-menu-inner --> 
  </div>
  <!-- / #main-menu --> 
  <!-- /4. $MAIN_MENU -->
  
  <div id="content-wrapper"> 
    <!--<ul class="breadcrumb breadcrumb-page">
      <div class="breadcrumb-label text-light-gray">You are here: </div>
      <li><a href="#">Home</a></li>
      <li class="active"><a href="#">Dashboard</a></li>
    </ul>-->
    <ul class="sorting_toolbar">
      <li>View:</li>
      <li class="sorting_icons active"><i class="fa fa-th-list"></i></li>
      <li class="sorting_icons"><i class="fa fa-th"></i></li>
    </ul>
    <div class="page-header">
      <div class="row"> 
        <!-- Page header, center on small screens -->
        <h1 class="col-xs-12 col-sm-8 text-center text-left-sm">Analytics<br>
          <div class="subheading">BUSINESS OVERVIEW AT A GLANCE</div>
        </h1>
        <div class="col-xs-12 col-sm-4">
          <div class="row" style="text-align:right"> 
           <input id="e1" name="e1" style="padding-right:8px" >
          </div>
        </div>
      </div>
    </div>
    <!-- / .page-header -->
  <?php
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

    //now devide the inventory in Unboxed, Refurbished, ...
  $array_sellAsStatus = get_All_SellAsStatus($tableForChart_keys);

  function get_All_SellAsStatus($tableForChart_keys)
  {
    $returnArray = array();
    foreach ($tableForChart_keys as $currentKey)
    {
      $exploded_currentKey = explode(" ", $currentKey);
      $sellAs_currentKey = ( ( $exploded_currentKey[1] != "Phone") ? $exploded_currentKey[1] : $exploded_currentKey[2]); //find the 1st word that gives a clue about the sell as. "Send" in "Feature Phone Send to Service Center" tells us that its send to SC
      if (! in_array($sellAs_currentKey, $returnArray))
        $returnArray[] =  $sellAs_currentKey;
    }

    // change "Send" hardware type to "Sent to Service center"
    if ($index_send = array_search("Send", $returnArray))
        $returnArray[$index_send] = "Send to Service center";

    // change "" hardware type to "(empty)"
    if (in_array("", $returnArray))
        $returnArray[array_search("", $returnArray)] = "(empty)";
    return $returnArray;
  }

  echo "<hr/><pre/>";
  // var_dump($tableForChart);
  // foreach ($tableForChart as $key => $value) {echo "($key, $value)<br/>"; }
  foreach ($tableForChart as $key => $value) {var_dump($key);echo "->";var_dump($value);}
  // foreach ($array_sellAsStatus as $key => $value) {echo "($key, $value)<br/>"; }
  // print_r($tableForChart_keys);
  // print_r($array_hardwareTypes);
  echo "</pre>";die;
  // echo $tableForChart["Accessories BER"];
  // foreach ($tableForChart_keys as $this_key) 
  // {
  //   print_r($tableForChart[$this_key]);
  //   echo "<br/>";
  // }
  // die;
  ?>

    <div class="row">
      <div class="col-md-8">
        <div class="stat-panel">
          <div class="stat-row">
            <div class="padding-sm-hr-custom">
              <div class="row"> <span class="order_total_count">147</span> <span class="order_confram">TOTAL ORDERS CONFIRMED<br/>
                56% OF BOOKED ORDERS</span> </div>
              <!-- <img src="<?php //echo base_url(); ?>assets/images/template/chart.jpg"/>  -->
              <div id="chartdiv" style="width: 100%; height: 800px;"><br/></div>
              </div>
          </div>
        </div>
      </div>
      <!-- /6. $EASY_PIE_CHARTS -->
      
      <div class="col-md-4">
        <div class="row">
          <div class="col-sm-4 col-md-12">
            <div class="stat-panel"> 
              <!-- Danger background, vertically centered text -->
              <div class="stat-cell valign-middle align_center"> <span class="text-bg">PERCENTAGE OF MONTHLY TARGET CONFIRMED ONLY </span><br>
                <!-- Small text --> 
                <span class="text-xlg"><strong>28</strong><span class="text-lg text-slim">%</span></span><br>
                <!-- Big text -->
                <div class="monthly_report"> <img src="<?php echo base_url(); ?>assets/images/template/monthly_target.jpg"/>
                  <div class="min_max_report"> <span class="min">Minimum</span><span class="max">Maximum</span> </div>
                </div>
                
                <!-- /.stat-cell --> 
              </div>
              <!-- /.stat-panel --> 
            </div>
          </div>
          <div class="col-sm-4 col-md-12">
            <div class="stat-panel"> 
              <!-- Danger background, vertically centered text -->
              <div class="stat-cell valign-middle align_center"> <span class="text-bg">TOTAL CONFIRMED REVENUE</span><br>
                <div class="totoal_revenue">Rs.245,967</div>
                
                <!-- /.stat-cell --> 
              </div>
              <!-- /.stat-panel --> 
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-3"> 
        <!-- Centered text -->
        <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg"> LOGISTICS </div>
            <div class="status_per"> <span class="status_text">87%</span> </div>
            <span class="xlheading">SAME DAY SHIPS </span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.27%</li>
            </ul>
          </div>
          <!-- /.stat-row --> 
        </div>
        <!-- /.stat-panel --> 
      </div>
      <div class="col-xs-3"> 
        <!-- Centered text -->
        <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">CUSTOMER SUPPORT</div>
            <div class="status_per status_per_green"> <span class="status_text">63%</span> </div>
            <span class="xlheading">CONFIRMATION</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/red-arrow.png"/></li>
              <li>-0.34%</li>
            </ul>
          </div>
          <!-- /.stat-row --> 
        </div>
        <!-- /.stat-panel --> 
      </div>
      <div class="col-xs-3"> 
        <!-- Centered text -->
        <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">QUALITY SUPPORT</div>
            <div class="status_per status_result"> <span class="status_text">150</span> </div>
            <span class="xlheading">CHECKS</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.44%</li>
            </ul>
          </div>
          <!-- /.stat-row --> 
        </div>
        <!-- /.stat-panel --> 
      </div>
      <div class="col-xs-3"> 
        <!-- Centered text -->
        <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">MIS</div>
            <div class="status_per status_result"> <span class="status_text">670</span> </div>
            <span class="xlheading">Uploaded</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.44%</li>
            </ul>
          </div>
          <!-- /.stat-row --> 
        </div>
        <!-- /.stat-panel --> 
      </div>
    </div>
  </div>
  <!-- / #content-wrapper -->
  <div id="main-menu-bg"></div>
</div>
<!-- highCharts:stacked -->
<script type="text/javascript">
  $(function () {
    $('#chartdiv').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Dashboard Inventory- LISTED'
        },
        xAxis: {
            categories: <?php echo "['". implode("', '", $array_hardwareTypes) ."']" ?>// ['Accessories', 'Camera', 'Computer', 'Feaured Phone', 'Smartphone', 'Tablet', 'Television']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Units'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            // margin: 9;
            width:580,
            align: 'center',
            // x: 0,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: '#FCFFC5', //(Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },
        series: [{
            name: 'BER',
            data: [5, 3, 4, 7, 2,4,2]
        }, {
            name: 'Preowned',
            data: [2, 2, 3, 2, 1,2,3]
        }, {
            name: 'Refurbished',
            data: [3, 4, 4, 2, 5,3,2]
        }, {
            name: 'Sealed',
            data: [3, 4, 4, 2, 5,3,2]
        }, {
            name: 'Unboxed',
            data: [3, 4, 4, 2, 5,3,1]
        }, {
            name: 'Sealed',
            data: [2, 2, 3, 2, 1,2,3]
        }, {
            name: 'Send to Service Center',
            data: [2, 2, 3, 2, 1,2,3]
        }, {
            name: 'Service in-house',
            data: [2, 2, 3, 2, 1,2,3]
        }]
    });
});
</script>
<!-- total orders confirmed with amCharts barGraph: -->
<script type="text/javascript">
<?php $i=1 ?>
var chartData = [{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},{
  "country": <?php $currentKey=$tableForChart_keys[$i]; echo "\"$currentKey\"";?>,
  "visits": <?php  $i+=1; echo($tableForChart[$currentKey]); ?>
},];

AmCharts.ready(function() {
var chart = new AmCharts.AmSerialChart();
chart.dataProvider = chartData;
chart.categoryField = "country";

var graph = new AmCharts.AmGraph();
graph.valueField = "visits";
graph.type = "column";
chart.addGraph(graph);

// chart.write('chartdiv');
});
</script>
</body>
</html>