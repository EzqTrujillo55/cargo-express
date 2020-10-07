<div id="chartdiv"></div>

<?php 
/*dump($new_data);
die();*/
$datas=json_encode($data);
?>
<script type="text/javascript">

var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "legend": {    	
    	"align":"left"
        //"useGraphSettings": true
    },
    "dataProvider": <?php echo $datas?> ,
    //"synchronizeGrid":false,   
    "graphs": [{
        "valueAxis": "v1",
        "lineColor": "#2c9f2c",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "hideBulletsCount": 30,
        "title": "<?php echo Driver::t("Signup")?>",
        "valueField": "total",
		"fillAlphas": 0.4,   
		"balloonText": "[[category]]: <b>[[total]]</b>",
    }   
    ],
    "chartScrollbar": {},
    "chartCursor": {
        "cursorPosition": "mouse"
    },
    "categoryField": "date",
    "categoryAxis": {
        "parseDates": true,
        "axisColor": "#DADADA",
        "minorGridEnabled": true
    }
});

chart.addListener("dataUpdated", zoomChart);
zoomChart();

function zoomChart(){
    chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
}
</script>