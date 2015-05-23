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

<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script><!-- highCharts: stacked graph -->
<script src="http://code.highcharts.com/modules/exporting.js"></script><!-- highCharts: stacked graph -->
<script src="http://code.highcharts.com/modules/drilldown.js"></script><!-- for drill down graph-->

<script src="http://code.highcharts.com/highcharts-more.js"></script><!-- Solid guage chart for monthly target -->
<script src="http://code.highcharts.com/modules/solid-gauge.js"></script><!-- Solid guage chart for monthly target -->
<?php  
  // echo "<h3><b>Automated Data</b></h3><br>";//myecho
  // echo "<pre>";//myecho
  $query = $this->db->query("SELECT id,name FROM compniesdata");
  $result_clientTable = $query->result_array();

  $clientTable = get_clientTable($result_clientTable);   //stores client list with their client ID
  // var_dump($clientTable);

  $table_prod_distribution = get_table_prod_distribution($clientTable, $this->db);
  //table_prod_distribution contains array all client_rows. each client_row has al the client data the graph would need

?>
<script >
  $(function () {

    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Drilldown Chart- False Data'
        },
        xAxis: {
            type: 'category'
        },

        tooltip: {
            formatter: function () {
                return ''+this.point.name+'<br><b>TSP: </b>RS.' + this.point.TSP + '<br/> <b>Share</b>:'+this.point.percent+'%';
            }
        },

        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true
                }
            }
        },

        series: [{
            name: 'Clients',
            colorByPoint: true,
            data: [{
                name: 'Saholic',
                y: 57,
                TSP: '12,3452',
                percent: '12',
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Bootstrapp',
                y: 27,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Technix',
                y: 87,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Vaishno',
                y: 47,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'GadgetCops',
                y: 97,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'W S Retail',
                y: 17,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'TimesInternet',
                y: 37,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Value Plus',
                y: 67,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'RIMS Marketing',
                y: 77,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'ReGlobe',
                y:  7,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Edge Infotel',
                y: 97,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'PB International',
                y: 27,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Green Mobiles',
                y: 57,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Cloudtail',
                y: 67,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'MMX Informatics',
                y: 77,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Sahil International',
                y: 37,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Karma',
                y: 87,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'TimesInternet',
                y: 27,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Regenersis',
                y: 37,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }, {
                name: 'Regenersis India Limited',
                y: 77,
                TSP: '12,3452',
                percent: '12',
                drilldown: 'animals'
            }]
        }],
        drilldown: {
            series: [{
                id: 'animals',
                data: [{
                    name:'Inventory Review', 
                    y:42, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Listed', 
                    y:21, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Manager\'s escalation', 
                    y:14, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Pending Repair', 
                    y:32, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Sell Offline', 
                    y:12, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Under QC', 
                    y:42, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Inbound Holding',
                    y: 2, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Out for Repair', 
                    y:22, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Ready to upload', 
                    y:42, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'BER', 
                    y:20, 
                    TSP: '4,689',
                    percent: '15'
                  },{
                    name:'Sold', 
                    y:82, 
                    TSP: '4,689',
                    percent: '15'
                  } ]
            }/*, {
                id: 'fruits',
                data: [
                    ['Inventory Review', 42],
                    ['Listed', 21],
                    ['Manager\'s escalation', 14],
                    ['Pending Repair', 32],
                    ['Sell Offline', 12],
                    ['Under QC', 42],
                    ['Inbound Holding', 2],
                    ['Out for Repair', 22],
                    ['Ready to upload', 42],
                    ['BER', 20],
                    ['Sold', 82],
                    ['Sell Offline', 10]
                ]
            }, {
                id: 'cars',
                data: [
                    ['Inventory Review', 42],
                    ['Listed', 21],
                    ['Manager\'s escalation', 14],
                    ['Pending Repair', 32],
                    ['Sell Offline', 12],
                    ['Under QC', 42],
                    ['Inbound Holding', 2],
                    ['Out for Repair', 22],
                    ['Ready to upload', 42],
                    ['BER', 20],
                    ['Sold', 82],
                    ['Sell Offline', 10]
                ]
            }*/]
        }
    });
});

// -------------------------------------2nd graph------------------------------------------------------------------
  $(function () {

    // Create the chart
    $('#container2').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Basic drilldown'
        },
        xAxis: {
            type: 'category'
        },

        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true
                }
            }
        },

        tooltip: {
            formatter: function () {
                var tooltip = this.point.name+'<br/><b>Quantity: <span style="color:'+ this.point.color + '">'+this.point.y+'</b></span>';
                tooltip += '<br><b>TSP: </b>RS.' + this.point.TSP + '<br/> <b>Share</b>:'+this.point.percent+'%<br/>';
                tooltip += '<br><b>Next Remittance: </b>RS.' + this.point.next_remittance /*+ '<br/> <b>_____</b>:'+this.point.percent+'%<br/>'*/;
                return tooltip;
            }
        },

        series: [{
            name: 'Things',
            colorByPoint: true,
            data: [
            <?php 
            $data_array = null;
            foreach ($table_prod_distribution as $client_array)
            {
              $client_data = "{
                name: '".$client_array['name']."',
                y: ".$client_array['count_pertinent'].",
                next_remittance: ".$client_array['next_remittance'].",
                drilldown: 'animals'
              }";
              $data_array[]=$client_data;
            }
            echo implode(',', $data_array);

            ?>
            ]
        }],
        drilldown: {
            series: [{
                id: 'animals',
                data: [
                    ['Cats', 4],
                    ['Dogs', 2],
                    ['Cows', 1],
                    ['Sheep', 2],
                    ['Pigs', 1]
                ]
            }, {
                id: 'fruits',
                data: [
                    ['Apples', 4],
                    ['Oranges', 2]
                ]
            }, {
                id: 'cars',
                data: [
                    ['Toyota', 4],
                    ['Opel', 2],
                    ['Volkswagen', 2]
                ]
            }]
        }
    });
});


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
            <!-- <div class="header_search">
              <form class="navbar-form pull-left">
                <input type="text" class="form-control" placeholder="Enter Keyword">
                <button class="search_btn" type="submit" value="submit"></button>
              </form>
            </div>
            <ul class="nav navbar-nav pull-right right-navbar-nav">
              

              <li class="nav-icon-btn nav-icon-btn-danger dropdown">
              <a href="#notifications" class="dropdown-toggle"> <img src="<?php echo base_url(); ?>assets/images/template/notifications.png"/> <span class="small-screen-text">Notifications</span> </a> -->
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
              
              <!-- <li class="dropdown"> <a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown"> <img src="<?php echo base_url(); ?>assets/images/template/3.jpg"/> <span>John Doe</span> </a> -->
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
        <li > <a href=<?php  echo '"'.base_url() . 'index.php/newdashboard"';?> ><i class="menu-icon fa fa-dashboard"></i><span class="mm-text">Dashboard</span><!-- <span class="label label-new">1127</span> --></a> </li>
       <li > <a href=<?php echo base_url() . "index.php/newdashboard/logistics";?> ><i class="menu-icon fa fa-clock-o"></i><span class="mm-text">Logistics</span></a> </li>
       <li > <a href=<?php echo base_url() . "index.php/newdashboard/quality";?> ><i class="menu-icon fa fa-clock-o"></i><span class="mm-text">Quality</span></a> </li>
       <li class="active"> <a href=<?php echo base_url() . "index.php/newdashboard/inventory";?> ><i class="menu-icon fa fa-clock-o"></i><span class="mm-text">Inventory</span></a> </li>
<!-- <li> <a href="../../stat-panels.html"><i class="menu-icon fa fa-bolt"></i><span class="mm-text">Support</span></a> </li>
        <li> <a href="../../widgets.html"><i class="menu-icon fa fa-envelope-o"></i><span class="mm-text">Quality</span></a> </li>
        <li> <a href="#"><i class="menu-icon fa fa fa-calendar"></i><span class="mm-text">Notifications</span><span class="label label-new">16</span></a></li>
        <li> <a href="#"><i class="menu-icon fa fa-user"></i><span class="mm-text">Contacts</span></a></li>
        <li> <a href="../../tables.html"><i class="menu-icon fa fa-gear"></i><span class="mm-text">Setting</span></a> </li>
        <li> <a href="../../charts.html"><i class="menu-icon fa-sign-out"></i><span class="mm-text">Logout</span></a> </li> -->
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
    <!-- <ul class="sorting_toolbar">
      <li>View:</li>
      <li class="sorting_icons active"><i class="fa fa-th-list"></i></li>
      <li class="sorting_icons"><i class="fa fa-th"></i></li>
    </ul> -->
    <div class="page-header">
      <div class="row"> 
        <!-- Page header, center on small screens -->
        <h1 class="col-xs-12 col-sm-8 text-center text-left-sm">Analytics<br>
          <div class="subheading">BUSINESS OVERVIEW AT A GLANCE</div>
        </h1>
        <div class="col-xs-12 col-sm-4">
          <div class="row" style="text-align:right"> 
           <input id="selector_dateRange" name="e1" style="padding-right:8px" >
          </div>
        </div>
      </div>
    </div>
    <div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

    <?php
/*
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 ");
  $table = $query->result_array();
  $saholicAll= $table[0]["COUNT(*)"];
  // echo "All Saholic's products:". $saholicAll ."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1");
  $table = $query->result_array();
  $saholicPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Paid:". $saholicPaid."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND lstatus = 'Sold'");
  $table = $query->result_array();
  $saholicSold = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold:". $saholicSold."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1 AND lstatus = 'Sold'");
  $table = $query->result_array();
  $saholic_soldAndPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold and Paid:". $saholic_soldAndPaid."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 0 AND lstatus = 'Sold'");
  $table = $query->result_array();
  $saholic_soldButNotPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold but not Paid:". $saholic_soldButNotPaid."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1 AND lstatus <> 'Sold' ");
  $table = $query->result_array();
  $saholic_paidButNotSold = $table[0]["COUNT(*)"];
  // echo "<b>Saholic's Paid but not Sold:". $saholic_paidButNotSold."</b><br/>";//myecho


  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'Returned to Client'");
  $table = $query->result_array();
  $saholic_returned = $table[0]["COUNT(*)"];
  // echo "Saholic's Returned Products Products:". $saholic_returned ."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'Pending Pickup'");
  $table = $query->result_array();
  $saholic_pendingPickup = $table[0]["COUNT(*)"];
  // echo "Saholic's Pending Pickup Products:". $saholic_pendingPickup ."<br/>";//myecho


  $saholic_nonArchived = $saholicAll -$saholic_soldAndPaid - $saholic_returned -$saholic_pendingPickup;
  // echo "Saholic's products excluding Archived :". $saholic_nonArchived ." (excluding sold&paid, returned, pending pick up)<br/>";//myecho



  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'listed'");
  $table = $query->result_array();
  $saholic_listed = $table[0]["COUNT(*)"];
  // echo "<b>Saholic's listed Products:". $saholic_listed ."</b><br/>";//myecho

  $saholic_listedPercent = $saholic_listed/$saholic_nonArchived *100;
  $saholic_listedPercent = number_format((float)$saholic_listedPercent, 2, '.', '');

  // echo "<b>%age of Saholic's products in listed: ".$saholic_listedPercent." %&nbsp;&nbsp;&nbsp;&nbsp; (excluding archived)</b><br/>";//myecho

  
  // echo "<br><br>";//myecho

  $query = $this->db->query("SELECT DISTINCT lstatus FROM `products` WHERE client_id = 1");
  $table = $query->result_array();

  $totalCount = 0;
  foreach ($table as $array1)
  {
    foreach ($array1 as $status)
     {

      if ($status === 'Manager\'s escalation')
      {
      $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'Manager\'s escalation'");
      }
      else
      {
      $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = '".$status."'");
      }
      $table = $query->result_array();
      $count_thisStatus = $table[0]["COUNT(*)"];
      // echo "Saholic's ".$status." Products:". $count_thisStatus ."</b><br/>"; //myecho
      $totalCount += $count_thisStatus;
    }
  }
  // echo "total Count=".$totalCount;//myecho
  // echo "<hr>";
  // echo "<pre>";
  // var_dump($table);
  // echo "</pre>";
  // echo "<hr>";//myecho

//=================================================================================================================================//
  // echo "<h3><b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;taking only Overcart warehouse</b></h3><br>";//myecho
   $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholicAll= $table[0]["COUNT(*)"];
  // echo "All Saholic's products:". $saholicAll ."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1 AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholicPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Paid:". $saholicPaid."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND lstatus = 'Sold' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholicSold = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold:". $saholicSold."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1 AND lstatus = 'Sold' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_soldAndPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold and Paid:". $saholic_soldAndPaid."<br/>";//myecho

$query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 0 AND lstatus = 'Sold' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_soldButNotPaid = $table[0]["COUNT(*)"];
  // echo "Saholic's Sold but not Paid:". $saholic_soldButNotPaid."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id = 1 AND payment_made_client= 1 AND lstatus <> 'Sold' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_paidButNotSold = $table[0]["COUNT(*)"];
  // echo "<strike>Saholic's Paid but not Sold:</strike>". $saholic_paidButNotSold."<br/>";//myecho

  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'Returned to Client' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_returned = $table[0]["COUNT(*)"];
  // echo "Saholic's Returned Products Products:". $saholic_returned ."<br/>";//myecho
  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'Pending Pickup' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_pendingPickup = $table[0]["COUNT(*)"];
  // echo "Saholic's Pending Pickup Products:". $saholic_pendingPickup ."<br/>";//myecho


  $saholic_nonArchived = $saholicAll -$saholic_soldAndPaid - $saholic_returned -$saholic_pendingPickup;
  // echo "Saholic's products excluding Archived :". $saholic_nonArchived ." (excluding sold&paid, returned, pending pick up)<br/>";//myecho



  
  $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND lstatus = 'listed' AND location =  'over_werehouse'");
  $table = $query->result_array();
  $saholic_listed = $table[0]["COUNT(*)"];
  // echo "<b>Saholic's listed Products:". $saholic_listed ."</b><br/>";//myecho

  $saholic_listedPercent = $saholic_listed/$saholic_nonArchived *100;
  $saholic_listedPercent = number_format((float)$saholic_listedPercent, 2, '.', '');

  // echo "<b>%age of Saholic's products in listed: ".$saholic_listedPercent." %&nbsp;&nbsp;&nbsp;&nbsp; (excluding archived)</b><br/>";//myecho

  
  // echo "<br><br>";//myecho

  $query = $this->db->query("SELECT DISTINCT lstatus FROM `products` WHERE client_id = 1 ");
  $table = $query->result_array();

  $totalCount = 0;
  foreach ($table as $array1)
  {
    foreach ($array1 as $status)
     {

      if ($status === 'Manager\'s escalation')
      {
      $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND location =  'over_werehouse' AND lstatus = 'Manager\'s escalation' ");
      }
      else
      {
      $query = $this->db->query("SELECT COUNT(*) FROM `products` WHERE client_id=1 AND location =  'over_werehouse' AND lstatus = '".$status."' ");
      }
      $table = $query->result_array();
      $count_thisStatus = $table[0]["COUNT(*)"];
      // echo "Saholic's ".$status." Products:". $count_thisStatus ."</b><br/>"; //myecho
      $totalCount += $count_thisStatus;
    }
  }
  // echo "total Count=".$totalCount;//myecho
  // echo "<hr>";
  // echo "<pre>";
  // var_dump($table);
  // echo "</pre>";//myecho
  // echo "<hr>";//myecho
*/  ?>

  <?php
  echo "<pre>";
  var_dump($table_prod_distribution[0]['name']);
  echo '</pre><pre>';//myecho
  var_dump($table_prod_distribution);
  $jsonObj = json_encode($table_prod_distribution);
  // echo "<br>";//myecho
  echo '</pre><pre>';//myecho
  echo "<h2>formatted json:</h2><br>";//myecho
  echo prettyPrint($jsonObj);//myecho

  echo '</pre><pre>';//myecho
  //list of all useful tables/arrays
  // var_dump($clientTable)  ;
  die;

function get_table_prod_distribution($clientTable, $db)
  {
    $table_prod_distribution=null;
    foreach ($clientTable as $client_name => $client_id)
    {
      $client_row = null; //array containing all data of the client in this iteration
      $client_row['name'] = $client_name;
      $client_row['id'] = $client_id;

      //Archived (=Sold && Paid) in all warehoues:
      //(Shown Individually on the web page)
      $client_archived = get_archived($client_id, $db);
      // echo "$client_name 's client_archived:". $client_archived."<br/>";//myecho
      $client_row['archived'] = $client_archived;

      //next remittance: (sold && not paid).
      // all warehouses included
      $client_remittance = get_nextRemittance($client_id, $client_name, $db);
      $client_row['next_remittance'] = $client_remittance;
      // echo "$client_name 's next remittance: $client_remittance<br>";//myecho

      //(denonminator in calculating %age of 'listed'/'BER'/etc...)
      $client_pertinent = get_relevant($client_id, $client_name, $db);
      $client_row['count_pertinent'] = $client_pertinent;
      // echo "$client_name 's client_pertinent:". $client_pertinent."  <--".'$client_all_OCwarehouse - ($client_soldAndPaid + $client_returned + $client_pedingPickUp )'."<br/>";//myecho


      //product distribution for client
      $client_prod_distribution = get_prod_distribution($client_id, $db, $client_pertinent);

      $client_row['prod_distribution'] = $client_prod_distribution;
      $table_prod_distribution[]=$client_row;

      // $<lstatus>count   _/
      // $<lstatus>percent
      // $<lstatus>TSP _/

    }
    return $table_prod_distribution;
  }

  function get_clientTable($result_clientTable)
  {
  /*   _DataStructure Map_ : $clientTable

  array(20)   //20 being the number of clients at the time. (Not fixed. Taken dynamically )
  {
  array($client_name => $client_id)  //both key and value in string
  array($client_name => $client_id)
  .
  .
  array($client_name => $client_id)
  }    

  */
    $returnArray = null;
    foreach ($result_clientTable as $client )
    {
      $client_id  = null;
      $client_name = null;
      foreach ($client as $key => $value) 
      {
        if ($key === 'id')
          $client_id = $value;
        elseif ($key === 'name')
          $client_name = $value;
        else
          die('Error in SQL table "compniesdata": Unknown Key');
      }
      $returnArray[$client_name] = $client_id;
    }
    return $returnArray;
  }


  function get_archived($client_id, $db)
  {
    $query =$db->query("SELECT COUNT(*)
                        FROM `products`
                        WHERE client_id = $client_id AND payment_made_client= 1 AND lstatus = 'Sold'");
    $table = $query->result_array();
    $client_archived = $table[0]["COUNT(*)"];

    return $client_archived;
  }

  function get_relevant($client_id, $client_name, $db)//client name not required. used only in echoing/debugging
  {
    //Sold&&Paid and in Overcart Warehouse :
    //(this will be excluded in calculating %age of 'listed'/'BER'/etc...)
    $query =$db->query("SELECT COUNT(*)
                        FROM `products`
                        WHERE client_id = $client_id AND payment_made_client= 1 AND lstatus = 'Sold' AND location =  'over_werehouse'");
    $table = $query->result_array();
    $client_soldAndPaid = $table[0]["COUNT(*)"];
    // echo "$client_name 's client_soldAndPaid:". $client_soldAndPaid."<br/>";//myecho

    //'Returned to Client' and in Overcart Warehouse :
    //(this will be excluded in calculating %age of 'listed'/'BER'/etc...)
    $query =$db->query("SELECT COUNT(*) 
                        FROM `products` WHERE client_id=1 
                        AND lstatus = 'Returned to Client' AND location =  'over_werehouse'");
    $table = $query->result_array();
    $client_returned = $table[0]["COUNT(*)"];
    // echo "$client_name 's client_returned:". $client_returned."<br/>";//myecho

    //'pending pick up' and in Overcart Warehouse :
    //(this will be excluded in calculating %age of 'listed'/'BER'/etc...)
    $query =$db->query("SELECT COUNT(*) 
                        FROM `products` WHERE client_id=1 
                        AND lstatus = 'Pending Pickup' AND location =  'over_werehouse'");
    $table = $query->result_array();
    $client_pedingPickUp = $table[0]["COUNT(*)"];
    // echo "$client_name 's client_pedingPickUp:". $client_pedingPickUp."<br/>";//myecho

    //All Client's products in Overcart Warehouse :
    // client_pertinent = client_all_OCwarehouse - (client_soldAndPaid + client_returned + client_pedingPickUp )
    $query =$db->query("SELECT COUNT(*)
                        FROM `products`
                        WHERE client_id = $client_id  AND location =  'over_werehouse'");
    $table = $query->result_array();
    $client_all_OCwarehouse = $table[0]["COUNT(*)"];
    // echo "$client_name 's client_all_OCwarehouse:". $client_all_OCwarehouse."<br/>";//myecho

    //(denonminator in calculating %age of 'listed'/'BER'/etc...)
    $client_pertinent = $client_all_OCwarehouse - ($client_soldAndPaid + $client_returned + $client_pedingPickUp );

    return $client_pertinent;
  }

  function get_prod_distribution($client_id,$db, $client_pertinent)
  {
    $client_prod_distribution = null;
    $query =$db->query("SELECT lstatus, SUM(tprice) , COUNT(id) AS count
                        FROM `products` 
                        WHERE client_id=$client_id AND location =  'over_werehouse'
                        GROUP BY lstatus");
    $result_array_client = $query->result_array();
    foreach ($result_array_client as $row)
    {
      $lstatus=null;
      $count = null;
      foreach ($row as $key => $value) 
      {
        if ($key === 'lstatus')
          $lstatus = $value;
        elseif ($key === 'count')
          $count = $value;
        elseif ($key === 'SUM(tprice)')
          $sum_tprice += $value;
        else
          die('Error in SQL table : Unknown Key in lstatuses for client');
      }
      $sum_tprice = number_format((float)$sum_tprice,2,'.',',');
      $count_pecent = ((float)$count) / ((float)$client_pertinent) *100;
      $count_pecent = number_format((float)$count_pecent,2,'.',',') . " %";
      $client_prod_distribution[$lstatus] = array('count'=> $count, 'sum_tprice'=>$sum_tprice, 'count_pecent'=>$count_pecent);
      // echo "lstatus= $lstatus, count=$count <br>";
    }
    return $client_prod_distribution;
  }

  function get_nextRemittance($client_id, $client_name, $db)//client name not required. used only in echoing/debugging
  {
    // Sold but not Paid. Products from all warehouses included
    $query = $db->query(" SELECT COUNT(*) 
                          FROM `products` 
                          WHERE client_id = 1 AND payment_made_client= 0 AND lstatus = 'Sold'");
    $table = $query->result_array();
    $client_soldButNotPaid = $table[0]["COUNT(*)"];
    // echo "$client_name 's client_soldButNotPaid:". $client_soldButNotPaid."<br/>";//myecho

    return $client_soldButNotPaid;
  }

  function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}
  ?>

    <!-- / .page-header -->
    
    <!-- <div class="row">
      <div class="col-md-8">
        <div class="stat-panel">
          <div class="stat-row">
            <div class="padding-sm-hr-custom">
              <div class="row"> <span class="order_total_count">147</span> <span class="order_confram">TOTAL ORDERS CONFIRMED<br/>
                56% OF BOOKED ORDERS</span> </div>
              <img src="<?php echo base_url(); ?>assets/images/template/chart.jpg"/> </div>
          </div>
        </div>
      </div> -->
      <!-- /6. $EASY_PIE_CHARTS -->
      
      <div class="col-md-4">
        <!-- <div class="row"> -->
          <!-- <div class="col-sm-4 col-md-12"> -->
            <!-- <div class="stat-panel">  -->
              <!-- Danger background, vertically centered text -->
              <!-- <div class="stat-cell valign-middle align_center"> <span class="text-bg">PERCENTAGE OF MONTHLY TARGET CONFIRMED ONLY </span><br> -->
                <!-- Small text --> 
                <!-- <span class="text-xlg"><strong>28</strong><span class="text-lg text-slim">%</span></span><br> -->
                <!-- Big text -->
                <!-- <div class="monthly_report"> <img src="<?php echo base_url(); ?>assets/images/template/monthly_target.jpg"/> -->
                  <!-- <div class="min_max_report"> <span class="min">Minimum</span><span class="max">Maximum</span> </div> -->
                <!-- </div> -->
                
                <!-- /.stat-cell --> 
              <!-- </div> -->
              <!-- /.stat-panel --> 
            <!-- </div> -->
          <!-- </div> -->
          <div class="col-sm-4 col-md-12">
            <div class="stat-panel">  
              <!-- Danger background, vertically centered text  -->
              <div class="stat-cell valign-middle align_center"> <span class="text-bg">Archived</span><br>
                <div class="totoal_revenue">Rs.245,967</div>
                
                <!-- /.stat-cell --> 
              </div>
              <!-- /.stat-panel --> 
            </div>
          </div>
           <div class="col-sm-4 col-md-12">
            <div class="stat-panel">  
              <!-- Danger background, vertically centered text  -->
              <div class="stat-cell valign-middle align_center"> <span class="text-bg">Next Remittance</span><br>
                <div class="totoal_revenue">Rs.245,967</div>
                
                <!-- /.stat-cell --> 
              </div>
              <!-- /.stat-panel --> 
            </div>
          </div>
        <!-- </div> -->
      </div>
    </div>
    <div class="row"><!--
      <div class="col-xs-3"> 
        <!-- Centered text -->
        <!-- <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg"> LOGISTICS </div>
            <div class="status_per"> <span class="status_text">87%</span> </div>
            <span class="xlheading">SAME DAY SHIPS </span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.27%</li>
            </ul>
          </div> -->
          <!-- /.stat-row --> 
        <!-- </div> -->
        <!-- /.stat-panel --> 
      <!-- </div>
      <div class="col-xs-3">  -->
        <!-- Centered text -->
        <!-- <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">CUSTOMER SUPPORT</div>
            <div class="status_per status_per_green"> <span class="status_text">63%</span> </div>
            <span class="xlheading">CONFIRMATION</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/red-arrow.png"/></li>
              <li>-0.34%</li>
            </ul> -->
          <!-- </div> -->
          <!-- /.stat-row --> 
        <!-- </div> -->
        <!-- /.stat-panel --> 
      <!-- </div> -->
      <!-- <div class="col-xs-3">  -->
        <!-- Centered text -->
        <!-- <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">QUALITY SUPPORT</div>
            <div class="status_per status_result"> <span class="status_text">150</span> </div>
            <span class="xlheading">CHECKS</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.44%</li>
            </ul>
          </div> -->
          <!-- /.stat-row --> 
        <!-- </div> -->
        <!-- /.stat-panel --> 
      <!-- </div>
      <div class="col-xs-3">  -->
        <!-- Centered text -->
        <!-- <div class="stat-panel text-center">
          <div class="stat-cell valign-middle align_center">
            <div class="text-bg">MIS</div>
            <div class="status_per status_result"> <span class="status_text">670</span> </div>
            <span class="xlheading">Uploaded</span>
            <ul class="status_more">
              <li>28.18</li>
              <li><img src="<?php echo base_url(); ?>assets/images/template/green-arrow.png"/></li>
              <li>0.44%</li>
            </ul> -->
          <!-- </div> -->
          <!-- /.stat-row --> 
        <!-- </div> -->
        <!-- /.stat-panel --> 
      <!-- </div> -->
     </div>
     <!-- insert graph divs here  -->
    <!-- <input id="selector_dateRange" name="selector_dateRange" style="padding-right:8px" > -->
    <!-- <div id="container" style="width: 100%; height: 550px; border:1px solid black;" ></div> -->
    <!-- <div id="chartdiv" style="width: 100%; height: 550px; border:1px solid black;" ></div>  -->
    <!-- <div id="graph_clientWise" style="width: 100%; height: 550px; border:1px solid black;" ></div> -->


  </div>
  <!-- / #content-wrapper -->
  <div id="main-menu-bg"></div>
</div>
</body>
</html> 
