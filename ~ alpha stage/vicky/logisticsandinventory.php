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

function abc($orders = array(), $db)
{

//$orderno = 'BST1100002096';

        $sqlo =  "SELECT t1.id, t1.imei, t1.sold_date, t2.time_stamp as dispatch_date
                  FROM products as t1
                  LEFT JOIN detail_loc_log as t2
                  ON t1.id = t2.productid
                  WHERE t1.orderno IN (". implode(',',$orders). ") && t2.new_location = 'to_buyer'";
        //var_dump($sqlo);

        $query_quality = $db->query($sqlo); 

        $sqlArray_quality = $query_quality->result_array();

        //echo "<pre>";
        //var_dump($sqlArray_quality);die;
}

$orders = array("'BST1100002096'","'BST1100002096'");
$abc = abc($orders, $this->db);

  ?>

<?php

$orderno = 'BST1100002096';

  $query = $this->db->query("SELECT t1.id, t1.imei, t1.sold_date, t2.time_stamp as dispatch_date
                  FROM products as t1
                  LEFT JOIN detail_loc_log as t2
                  ON t1.id = t2.productid
                  WHERE t1.orderno = '$orderno' && t2.new_location = 'to_buyer'");
  $table = $query->result_array();
  //echo "<pre>";
  //var_dump($table);
  //echo "</pre>";
  //die;
//same day ship's query


  $query = $this->db->query("SELECT concat(t2.name,' ',t1.sell_as) as catstat,count(t1.id) as quant FROM `products` as t1 left join categories as t2 on t1.category_id = t2.id group by catstat");
  $table = $query->result_array();
  // echo "<pre>";
  // var_dump($table);
  // echo "</pre>";
  // die;

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
  $array_Feaured_Phone = getDistribution("Feature", $array_sellAsStatus, $tableForChart);
  $array_Smartphone = getDistribution("Smartphone", $array_sellAsStatus, $tableForChart);
  $array_Tablet = getDistribution("Tablet", $array_sellAsStatus, $tableForChart);
  $array_Television = getDistribution("Television", $array_sellAsStatus, $tableForChart);
//['Accessories', 'Camera', 'Computer', 'Feaured Phone', 'Smartphone', 'Tablet', 'Television']

  // echo "<pre>";
  // // var_dump($array_accessories);
  // var_dump($array_Feaured_Phone);
  // echo "</pre>";die;

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
            //     else//for ebugging only
            // {echo "hardware type=$hardwareType, exploded_currentKey[0]=$exploded_currentKey[0]<br/>";}

      
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


  //echo "<hr/><pre/>";
  // var_dump($tableForChart);
  //foreach ($tableForChart as $key => $value) {echo "($key, $value)<br/>"; }
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
  //die;  
  ?>

<script>
  $(function() { $("#selector_dateRange").daterangepicker(); });

      function render_clientWise () {
        
      $('#graph_clientWise').highcharts({
          title: {
              text: 'Inventory Overview',
              x: -20 //center
          }/*,
          subtitle: {
              text: 'Source: WorldClimate.com',
              x: -20
          }*/,
          xAxis: {
              categories: ['Pending Pickup', 'Inbound Holding','Pending QC', 'Under QC', 'Manager\'s escalation','Out for Repair',
               'Ready to upload', 'Listed', 'Returned to Client', 'Sell Offline', 'Inventory Review', 'Sold']
          },
          yAxis: {
              title: {
                  text: 'Items'
              },
              plotLines: [{
                  value: 0,
                  width: 1,
                  color: '#808080'
              }]
          }/*,
          tooltip: {
              valueSuffix: '°C'
          }*/,
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
          },
          series: [{
              name: 'Karma',
              data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
          }, {
              name: 'Cloudtail',
              data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
          }, {
              name: 'PB International',
              data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
          }, {
              name: 'Saholic',
              data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
          }, {
              name: 'Technix',
              data: [ 7.0-5, 6.9-5, 9.5-5, 14.5-5, 18.2-5, 21.5-5, 25.2-5, 26.5-5, 23.3-5, 18.3-5, 13.9-5, 9.6]
          }, {
              name: 'Value Plus',
              data: [3.9+5, 4.2+5, 5.7+5, 8.5+5, 11.9+5, 15.2+5, 17.0+5, 16.6+5, 14.2+5, 10.3+5, 6.6+5, 4.8+3]
          }]
        });
      
      }
    </script>
    <script >
      

    </script>
    <script>
    //column drilldown chart: aging by lstatus
    //   $(function () {

    // // Create the chart
    // $('#container').highcharts({
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: 'Basic drilldown'
    //     },
    //     xAxis: {
    //         type: 'category'
    //     },

    //     legend: {
    //         enabled: false
    //     },

    //     plotOptions: {
    //         series: {
    //             borderWidth: 0,
    //             dataLabels: {
    //                 enabled: true
    //             }
    //         }
    //     },

    //     series: [{
    //         name: 'Things',
    //         colorByPoint: true,
    //         data: [{
    //             name: 'Animals',
    //             y: 5,
    //             drilldown: 'animals'
    //         }, {
    //             name: 'Fruits',
    //             y: 2,
    //             drilldown: 'fruits'
    //         }, {
    //             name: 'Cars',
    //             y: 4,
    //             drilldown: 'cars'
    //         }]
    //     }],
    //     drilldown: {
    //         series: [{
    //             id: 'animals',
    //             data: [
    //                 ['Cats', 4],
    //                 ['Dogs', 2],
    //                 ['Cows', 1],
    //                 ['Sheep', 2],
    //                 ['Pigs', 1]
    //             ]
    //         }, {
    //             id: 'fruits',
    //             data: [
    //                 ['Apples', 4],
    //                 ['Oranges', 2]
    //             ]
    //         }, {
    //             id: 'cars',
    //             data: [
    //                 ['Toyota', 4],
    //                 ['Opel', 2],
    //                 ['Volkswagen', 2]
    //               ]
    //             }]
    //           }
    //       });
    //   });
    $('#chartdiv').css({'background-color':'#112233'});
    </script>
     <script>

       function render_clientWise_2 () {

      var json_clientWise = json_variable.clientWise;
      console.log('in function render_clientWise');
      console.log(json_clientWise.legend.Karma);

      $('#graph_clientWise').highcharts({
          title: {
              text: 'Inventory Overview',
              x: -20 //center
          }/*,
          subtitle: {
              text: 'Source: WorldClimate.com',
              x: -20
          }*/,
          xAxis: {
              categories: json_clientWise.categories 
          },
          yAxis: {
              title: {
                  text: json_clientWise.yAxis_text
              },
              plotLines: [{
                  value: 0,
                  width: 1,
                  color: '#808080'
              }]
          }/*,
          tooltip: {
              valueSuffix: '°C'
          }*/,
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
          },
          series: [{
              name: 'Karma',
              data: json_clientWise.legend.Karma
          }, {
              name: 'Cloudtail',
              data: json_clientWise.legend.Cloudtail
          }, {
              name: 'PB International',
              data: json_clientWise.legend.PB_International
          }, {
              name: 'Saholic',
              data: json_clientWise.legend.Saholic
          }, {
              name: 'Technix',
              data: json_clientWise.legend.Technix
          }, {
              name: 'Value Plus',
              data: json_clientWise.legend.Value_Plus
          }]
        });
      
      }

      var json_variable;
        $(function() { 
          $("#selector_dateRange").daterangepicker({
             onChange: function() { 
              console.log('change') ;
              callAjax();
            }
          }); 
          renderAll();
        });


        function callAjax ( x ) 
        {
          var selector_dateRange = $("#selector_dateRange").val();
          console.log('selected range: '+ selector_dateRange);

          $.ajax({
            type: "POST",
            url: '<?php echo site_url("newdashboard").'/submitDates_inventory';?>', 
            data: JSON.parse(selector_dateRange), 
            dataType: 'json',
            // async: false, //This is deprecated in the latest version of jquery must use now callbacks
            success: function(d)
            {
              json_variable = d;
              console.log("sasas");
              console.log(json_variable);

              render_clientWise_2();
            },
            error: function (jqXHR, textStatus, errorThrown) { alert("ajax error"); } 
          });
        }

        function renderAll () 
        {
          render_clientWise();
        }

        function renderAll_2 () 
        {
          console.log('renderAll_2');
          render_clientWise_2();
          // console.log(d);
          // console.log(d.clientWise);
          // var json_clientWise = d.clientWise;
          // render_clientWise_2(json_clientWise);
        }

        $(function () {

    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
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

        series: [{
            name: 'Things',
            colorByPoint: true,
            data: [{
                name: <?php echo "'".$array_hardwareTypes[0]."'" ?>,
                // <?php echo "['". implode("', '", $array_hardwareTypes) ."']" ?>// ['Accessories', 'Camera', 'Computer', 'Feaured Phone', 'Smartphone', 'Tablet', 'Television']
                y: <?php echo  array_sum($array_accessories);?>,
                 //[<?php echo $array_accessories['BER'].", ".$array_Camera['BER'] . ", ". $array_Computer['BER'].", ". $array_Feaured_Phone['BER'] . ", ". $array_Smartphone['BER'] . ", ". $array_Tablet['BER']. ", ". $array_Television['BER'];?>]  
                drilldown: 'animals'
            }, {
                name: <?php echo "'".$array_hardwareTypes[2]."'" ?>,// [1] gives books
                y: <?php echo  array_sum($array_Camera);?>,//this gives books not camers
                drilldown: 'fruits'
            }, {
                name: <?php echo "'".$array_hardwareTypes[3]."'" ?>,// [2] gives books
                y: <?php echo  array_sum($array_Computer);?>,//this gives books not camers
                drilldown: 'cars'
            }, {
                name: <?php echo "'".$array_hardwareTypes[4]."'" ?>,// [2] gives books
                y: <?php echo  array_sum($array_Feaured_Phone);?>,//this gives books not camers
                drilldown: 'cars'
            }]
        }],
        drilldown: {
            series: [{
                id: 'animals',
                data: [
                    ['BER', 4],
                    ['New', 2],
                    ['Preowned', 1],
                    ['Sealed', 2],
                    ['Refurbished',3]
                    ['Unboxed', 1],
                    ['Send to service center', 1]
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

 /*
  Inventory- Listed graph- stacked column
  */
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
            data: [<?php echo $array_accessories['BER'].", ".$array_Camera['BER'] . ", ". $array_Computer['BER'].", ". $array_Feaured_Phone['BER'] . ", ". $array_Smartphone['BER'] . ", ". $array_Tablet['BER']. ", ". $array_Television['BER'];?>]  
        }, { // ['Accessories', 'Camera', 'Computer', 'Feaured Phone', 'Smartphone', 'Tablet', 'Television']
            name: 'New',
            data: [<?php echo $array_accessories['New'].", ".$array_Camera['New'] . ", ". $array_Computer['New'].", ". $array_Feaured_Phone['New'] . ", ". $array_Smartphone['New'] . ", ". $array_Tablet['New']. ", ". $array_Television['New'];?>]
        }, {
            name: 'Preowned',
            data: [<?php echo $array_accessories['Preowned'].", ".$array_Camera['Preowned'] . ", ". $array_Computer['Preowned'].", ". $array_Feaured_Phone['Preowned'] . ", ". $array_Smartphone['Preowned'] . ", ". $array_Tablet['Preowned']. ", ". $array_Television['Preowned'];?>]
        }, {
            name: 'Refurbished',
            data: [<?php echo $array_accessories['Refurbished'].", ".$array_Camera['Refurbished'] . ", ". $array_Computer['Refurbished'].", ". $array_Feaured_Phone['Refurbished'] . ", ". $array_Smartphone['Refurbished'] . ", ". $array_Tablet['Refurbished']. ", ". $array_Television['Refurbished'];?>]
        }, {
            name: 'Sealed',
            data: [<?php echo $array_accessories['Sealed'].", ".$array_Camera['Sealed'] . ", ". $array_Computer['Sealed'].", ". $array_Feaured_Phone['Sealed'] . ", ". $array_Smartphone['Sealed'] . ", ". $array_Tablet['Sealed']. ", ". $array_Television['Sealed'];?>]
        }, {
            name: 'Unboxed',
            data: [<?php echo $array_accessories['Unboxed'].", ".$array_Camera['Unboxed'] . ", ". $array_Computer['Unboxed'].", ". $array_Feaured_Phone['Unboxed'] . ", ". $array_Smartphone['Unboxed'] . ", ". $array_Tablet['Unboxed']. ", ". $array_Television['Unboxed'];?>]
        }, {
            name: 'Send to Service center',
            data: [<?php echo $array_accessories['Send to Service center'].", ".$array_Camera['Send to Service center'] . ", ". $array_Computer['Send to Service center'].", ". $array_Feaured_Phone['Send to Service center'] . ", ". $array_Smartphone['Send to Service center'] . ", ". $array_Tablet['Send to Service center']. ", ". $array_Television['Send to Service center'];?>]
        }]
    });
});
/*
Inventory stacked column graph ends here
*/

// $(function () {
//     $('#chartdiv').highcharts({
//         chart: {
//             type: 'column'
//         },
//         title: {
//             text: 'Stacked column chart'
//         },
//         xAxis: {
//             categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
//         },
//         yAxis: {
//             min: 0,
//             title: {
//                 text: 'Total fruit consumption'
//             },
//             stackLabels: {
//                 enabled: true,
//                 style: {
//                     fontWeight: 'bold',
//                     color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
//                 }
//             }
//         },
//         legend: {
//             align: 'right',
//             x: -30,
//             verticalAlign: 'top',
//             y: 25,
//             floating: true,
//             backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
//             borderColor: '#CCC',
//             borderWidth: 1,
//             shadow: false
//         },
//         tooltip: {
//             formatter: function () {
//                 return '<b>' + this.x + '</b><br/>' +
//                     this.series.name + ': ' + this.y + '<br/>' +
//                     'Total: ' + this.point.stackTotal;
//             }
//         },
//         plotOptions: {
//             column: {
//                 stacking: 'normal',
//                 dataLabels: {
//                     enabled: true,
//                     color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
//                     style: {
//                         textShadow: '0 0 3px black'
//                     }
//                 }
//             }
//         },
//         series: [{
//             name: 'John',
//             data: [5, 3, 4, 7, 2]
//         }, {
//             name: 'Jane',
//             data: [2, 2, 3, 2, 1]
//         }, {
//             name: 'Joe',
//             data: [3, 4, 4, 2, 5]
//         }]
//     });
// });

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
        <li class="active"> <a href=<?php echo base_url() . "index.php/newdashboard/logisticsandinventory";?> ><i class="menu-icon fa fa-clock-o"></i><span class="mm-text">Logistics and Inventory</span></a> </li>
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
          <!-- <div class="col-sm-4 col-md-12"> -->
            <!--<div class="stat-panel">  
              Danger background, vertically centered text -->
              <!-- <div class="stat-cell valign-middle align_center"> <span class="text-bg">TOTAL CONFIRMED REVENUE</span><br> -->
                <!-- <div class="totoal_revenue">Rs.245,967</div> -->
                
                <!-- /.stat-cell --> 
            <!--   </div> -->
              <!-- /.stat-panel --> 
            <!-- </div> -->
          <!-- </div> -->
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
    <div id="container" style="width: 100%; height: 550px; border:1px solid black;" ></div>
    <div id="chartdiv" style="width: 100%; height: 550px; border:1px solid black;" ></div> 
    <!-- <div id="graph_clientWise" style="width: 100%; height: 550px; border:1px solid black;" ></div> -->


  </div>
  <!-- / #content-wrapper -->
  <div id="main-menu-bg"></div>
</div>
</body>
</html> 
