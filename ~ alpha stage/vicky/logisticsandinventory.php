<!DOCTYPE html>
<html>
<head>
  <title>Dashboard | Overcart Analytics</title>

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
  <script src="http://code.highcharts.com/modules/drilldown.js"></script><!-- for drill down graph-->


 
  <script >
      
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
      $(function () {

    // Create the chart
    $('#container').highcharts({
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

        series: [{
            name: 'Things',
            colorByPoint: true,
            data: [{
                name: 'Animals',
                y: 5,
                drilldown: 'animals'
            }, {
                name: 'Fruits',
                y: 2,
                drilldown: 'fruits'
            }, {
                name: 'Cars',
                y: 4,
                drilldown: 'cars'
            }]
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



     
  </script>
</head>
<body>

    <input id="selector_dateRange" name="selector_dateRange" style="padding-right:8px" >
    <div id="graph_clientWise" style="width: 100%; height: 550px; border:1px solid black;" ></div>
    <!-- <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto border:1px solid black"></div> -->

</body>
</html>