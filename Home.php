<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body style="background-image: url('media/background.png'); background-repeat: repeat;">
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="https://github.com/JorgeST">
        <img src="media/GitHub-Mark-32px.png" width="32" height="32" class="d-inline-block align-top" alt="">
        GitHub project by JorgeST
    </a>
</nav>

<style>
    li {
        color: red;
    }
</style>
<?php
require_once 'Currency.php';

$digital_currency_list = array();
$physical_currency_list = array();


$digital_row = 1;
if (($handle = fopen("documents/digital_currency_list.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        if ($digital_row != 1) {
            $currency = new Currency($data[0], $data[1]);
            array_push($digital_currency_list, $currency);
        }
        $digital_row++;
    }
    fclose($handle);
}
$physical_row = 1;

if (($handle = fopen("documents/physical_currency_list.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        if ($physical_row != 1) {
            $currency = new Currency($data[0], $data[1]);
            array_push($physical_currency_list, $currency);
        }
        $physical_row++;
    }
    fclose($handle);
}; ?>
<div class="container"
     style="padding: 30px 10px; background-color: rgba(250,250,250,0.7); margin-top: 100px; font-size: larger; font-weight: bold;">
    <h1>How much did you missed out by not investing?</h1>

    <p>This aplication lets you look back in time and find out how much you could have earned if you had invested in a
        Crypto Currency.</p>
    <p>It uses <a href="https://www.alphavantage.co/">ALPHA VANTAGE</a> API to calculate the earnings.</p>

    <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#myModal">HOW IT WORKS</button>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-body">
                    <h2>Formula used:</h2>
                    <ul>
                        <p>First we find out what month you would have invested, the Cryto Currency you have chosen and
                            the physical currency you manage.</p>
                        <li><p>Eg. We want to look back 5 months and compare Bitcoins using New Zealand Dollar (NZD)</p>
                        </li>

                        <p>We find the closing price of the Crypto Currency you selected.</p>
                        <li><p>Eg. NZD $19242 for a bitcoin as of Oct 2020 (5 months back)</p></li>

                        <p>We find the amount of Cryto Currency your investment would have purchased.</p>
                        <li><p>Eg. By investing NZD $1000 and buying a bitcoin of value NZD $19242 as of Nov 2020 (5
                                months back) it would have bought you 0.051969 Bitcoin</p></li>

                        <p>We then start to look at every month closing price from your selected purchase date to the
                            present.</p>
                        <p>Based on your percentage earned of a bitcoin we can find out how much you have.</p>

                        <li><p>Eg. In month 4 Bitcoin increased to NZD $28923. You own 0.051969 of a Bitcoin, which is
                                NZD $1503</p></li>

                        <p>We then take this number and substract it from the inital investment.</p>
                        <li><p>Eg. $1503 - $1000 = NZD $503 earned in the first month.</p></li>

                        <p>This process gets repeated for everymonth a ploted in a line graph for better
                            comprehension</p>


                    </ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>


            </div>

        </div>
    </div>

    <form style="padding: 50px 0px;">
        <div class="row mb-3">
            <div class="col-lg-2 col-md-4">
                <label for="investment" class="form-label">Total invested</label>
                <input type="investment" class="form-control investment" id="investment" placeholder="1000">
            </div>
            <div class="col-lg-5 col-md-4">
                <label for="physicalCurrency" class="form-label">Physical Currency</label>
                <select class="form-select form-select-lg mb-3 form-control physicalCurrency"
                        aria-label="Default select example" name="physicalCurrency">
                    <?php foreach ($physical_currency_list as $physical_currency): ?>
                        <option value="<?php echo($physical_currency->getAbbreviation()); ?>"><?php echo($physical_currency->getName()); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-5 col-md-4">
                <label for="digitalCurrency" class="form-label">Digital Currency</label>
                <select class="form-select form-select-lg mb-3 form-control digitalCurrency"
                        aria-label="Default select example" name="digitalCurrency">
                    <?php foreach ($digital_currency_list as $digital_currency): ?>
                        <option value="<?php echo($digital_currency->getAbbreviation()); ?>"><?php echo($digital_currency->getName()); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="investmentPeriod" class="form-label">Investment period</label>
            <p id="investmentText">0 months back</p>
            <input type="range" class="form-range investmentPeriod" min="0" max="12" step="1" id="months"
                   onchange="updateInvestmentPeriodLabel(this.value);" style="width: 100%;" value="0">
        </div>
        <br>
        <br>
        <button type="submit" class="btn btn-primary calculate"
                style="text-align: center; display: block; width: 200px; margin: 0px auto;">Calculate
        </button>
        <br>
        <br>



    </form>
    <div id="message"></div>
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>

    <script>
            function createGraph(graphPoints) {

                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    theme: "Earnings",
                    title: {
                        text: "Investment"
                    },
                    axisX: {
                        valueFormatString: "MMM YYYY",
                        crosshair: {
                            enabled: true,
                            snapToDataPoint: true
                        }
                    },
                    axisY: {
                        title: "Earnings",
                        includeZero: true,
                        crosshair: {
                            enabled: true
                        }
                    },
                    toolTip: {
                        shared: true
                    },
                    legend: {
                        cursor: "pointer",
                        verticalAlign: "bottom",
                        horizontalAlign: "left",
                        dockInsidePlotArea: true,
                        itemclick: toogleDataSeries
                    },
                    data: [{
                        type: "line",
                        showInLegend: true,
                        name: "Earnings",
                        markerType: "square",
                        xValueFormatString: "MMM DD, YYYY",
                        color: "#F08080",
                        dataPoints: graphPoints
                    }]
                });
                chart.render();

                function toogleDataSeries(e) {
                    if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                        e.dataSeries.visible = false;
                    } else {
                        e.dataSeries.visible = true;
                    }
                    chart.render();
                }

            }
        </script>

        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


        <script>
            var bitcoin_percentage;
            var investment;

            function generateGraphValues(graph_data, period) {
                var earning_dates = [];
                for (month = period; month >= 1; month--) {
                    var month_data = getMonthData(graph_data, month);
                    var closing_value = getMontlyClosingValue(month_data);
                    var earnings = getInvestmentEarnings(bitcoin_percentage, closing_value);
                    earning_dates.push({
                        x: (new Date(Object.keys(graph_data)[month])),
                        y: parseInt(earnings - investment)
                    })
                }

                createGraph(earning_dates);
                console.log(earning_dates);
            }

            function getInvestmentEarnings(bitcoinPercentage, bitcoinClosingValue) {
                return (bitcoinPercentage * bitcoinClosingValue);
            }

            function getMontlyClosingValue(monthData) {
                return monthData[(Object.keys(monthData)[6])];
            }

            function getMonthData(graph_data, monthNumber) {
                var starting_date = (Object.keys(graph_data)[monthNumber]);
                var month_data = graph_data[starting_date];
                return month_data;
            }

            function getPercentageEarned(data, period) {

            }

            function updateInvestmentPeriodLabel(val) {
                document.getElementById('investmentText').innerHTML = val + " months";
            }

            $(document).ready(function () {
                $('.calculate').on('click', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    investment = $btn.parent().parent().find('.investment').val();
                    var period = $btn.parent().parent().find('.investmentPeriod').val();
                    var physical_currency = $btn.parent().parent().find('.physicalCurrency').val() || '';
                    var digital_currency = $btn.parent().parent().find('.digitalCurrency').val() || '';
                    var base_url = "https://www.alphavantage.co/query";
                    var api_key = "_YOUR_API_KEY";
                    // Send the data using post

                    $.get(base_url, {
                        "function": "DIGITAL_CURRENCY_MONTHLY",
                        "apikey": api_key,
                        "market": physical_currency,
                        "symbol": digital_currency
                    }, function (data, textStatus, jqXHR) {
                        if (data["Error Message"] == 'Invalid API call. Please retry or visit the documentation (https://www.alphavantage.co/documentation/) for DIGITAL_CURRENCY_MONTHLY.') {
                            // Error with the parameters
                            $('#message').html('<div class="alert alert-danger">' + data["Error Message"] + '<button type="button" class="close close-alert" data-dismiss="alert" aria-hidden="true">×</button></div>');

                        } else {
                            console.log("NO ERROR");
                            console.log(data);
                            // Create a graph based on the information that we got from the API
                            var graph_data = (data["Time Series (Digital Currency Monthly)"]);
                            var month_data = getMonthData(graph_data, period);
                            var closing_value = getMontlyClosingValue(month_data);
                            // Find out how much of a bitcoin we would have purchased
                            bitcoin_percentage = (investment / closing_value)
                            // Find out the amount won each month based on the percentage
                            generateGraphValues(graph_data, period);

                        }

                    });

                });
            });
        </script>

</body>
</html>