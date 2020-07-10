<?php 
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: ../../");
  }

// Create a new entry
if (isset($_POST['new'])) {
    $item = $_POST['item'];
    $frequency = $_POST['frequency'];
    $unit = $_POST['unit'];
    $start = $_POST['start'];
    $amount = $_POST['amount'];
    $income = $_POST['income'];

    $statement = $dbc->prepare("INSERT INTO bills (name,unit,frequency,start,amount,income) VALUES (?,?,?,?,?,?)");
    $statement->execute([$item,$unit,$frequency,$start,$amount,$income]);

}

// Delete an entry 
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $statement = $dbc->prepare("DELETE from bills WHERE ID = ?");
    $statement->execute([$id]);
    header('location:' . $uri->expenditure());
}

?>
<div id="expContainer">
<?php
    if (isset($_GET['edit'])) {        
        if (isset($_POST['submit'])) {
            $item = $_POST['item'];
            $frequency = $_POST['frequency'];
            $unit = $_POST['unit'];
            $start = $_POST['start'];
            $amount = $_POST['amount'];
        
            $statement = $dbc->prepare("UPDATE bills SET name=?, unit=?, frequency=?, start=?, amount=? WHERE ID=?");
            $statement->execute([$item,$unit,$frequency,$start,$amount,$_GET['edit']]);
        
            header('location: ' . $uri->expenditure());
        }
        
        // Get the initial variables 
        $id = $_GET['edit'];
        
        $statement = $dbc->prepare("SELECT * from bills WHERE ID = ?");
        $statement->execute([$id]);
        $result = $statement->fetch();
        
        ?>
        
        <h2>Edit: <?php echo $result['name']; ?></h2>
        <form method="POST">
            <label for="item">Item</label><br>
            <input type="text" name="item" value="<?php echo $result['name']; ?>"><br>
            <label for="frequency">Frequency</label><br>
            <input type="number" name="frequency" value="<?php echo $result['frequency']; ?>">
            <select name="unit">
                <option value="365"<?php if ($result['unit'] == '365') echo ' selected'; ?>>Day</option>
                <option value="52"<?php if ($result['unit'] == '52') echo ' selected'; ?>>Week</option>
                <option value="12"<?php if ($result['unit'] == '12') echo ' selected'; ?>>Month</option>
                <option value="1"<?php if ($result['unit'] == '1') echo ' selected'; ?>>Year</option>
            </select><br>
            <label for="start">Last payment</label><br>
            <input type="date" name="start" value="<?php echo $result['start']; ?>"><br>
            <label for="amount">Amount</label><br>
            <input type="number" name="amount" step="0.01" value="<?php echo $result['amount']; ?>"><br>
            <input type="submit" name="submit" value="Submit">
        </form>

<?php 

    } else {
    // Gather data 
    // Create datasets
    $totals = array(
        'income' => [
            'daily' => 0,
            'weekly' => 0,
            'monthly' => 0,
            'yearly' => 0
        ],
        'out' => [
            'daily' => 0,
            'weekly' => 0,
            'monthly' => 0,
            'yearly' => 0
        ]
    );

    // Create income dataset
    $bills = [];
    $statement = $dbc->prepare("SELECT ID, name, unit, frequency, amount, start, income FROM bills ORDER BY name");
    $statement->execute();
    $income = $statement->fetchAll();
    foreach ($income as $each) {
        $bills[$each['ID']] = new Money(
            $each['ID'],
            $each['name'],
            $each['unit'],
            $each['frequency'],
            $each['amount'],
            $each['start'],
            $each['income']
        );
        if ($bills[$each['ID']]->getIncome()) {
            $totals['income']['daily']      = $totals['income']['daily']    + $bills[$each['ID']]->getDaily();
            $totals['income']['weekly']     = $totals['income']['weekly']   + $bills[$each['ID']]->getWeekly();
            $totals['income']['monthly']    = $totals['income']['monthly']  + $bills[$each['ID']]->getMonthly();
            $totals['income']['yearly']     = $totals['income']['yearly']   + $bills[$each['ID']]->getAnnual();  
        } else {
            $totals['out']['daily']         = $totals['out']['daily']       + $bills[$each['ID']]->getDaily();
            $totals['out']['weekly']        = $totals['out']['weekly']      + $bills[$each['ID']]->getWeekly();
            $totals['out']['monthly']        = $totals['out']['monthly']    + $bills[$each['ID']]->getMonthly();
            $totals['out']['yearly']        = $totals['out']['yearly']      + $bills[$each['ID']]->getAnnual(); 
        }
    }   
    ?>

    <a id="showIncomeDiv"><h2>Income</h2></a>
    <div id="incomeDiv">
        <a id="showIncome">Add new item</a>
        <div class="hidden" id="addIncome">
            <form method="POST">
                <label for="item">Item</label><br>
                <input type="text" name="item"><br>
                <label for="frequency">Frequency</label><br>
                <input type="number" name="frequency">
                <select name="unit">
                    <option value="365">Day</option>
                    <option value="52">Week</option>
                    <option value="12">Month</option>
                    <option value="1">Year</option>
                </select><br>
                <label for="start">Last payment</label><br>
                <input type="date" name="start"><br>
                <label for="amount">Amount</label><br>
                <input type="number" name="amount" step="0.01"><br>
                <input type="hidden" name="income" value="1"><br>
                <input type="submit" name="new" value="Add new income">
            </form>
        </div>
        <script type="text/javascript">
        $( "#showIncome" ).click(function() {
        $( "#addIncome" ).toggle( "slow", function() {
            // Animation complete.
        });
        });
        </script>
        <table id="income" class="display" style="width:100%">
            <thead>
                <th>Name</th>
                <th>Daily</th>
                <th>Weekly</th>
                <th>Monthly</th>
                <th>Yearly</th>
                <th>Next due</th>
                <th>Amount</th>
                <th>Edit</th>
                <th>Delete</th>
            </thead>
            <tbody>
            <?php
            foreach($bills as $each) {
                if ($each->getIncome()) {
                    echo '<tr>';
                        echo '<td>' . $each->getName() . '</td>';
                        echo '<td align="right">£' . $each->getDaily() . '</td>';
                        echo '<td align="right">£' . $each->getWeekly() . '</td>';
                        echo '<td align="right">£' . $each->getMonthly() . '</td>';
                        echo '<td align="right">£' . $each->getAnnual() . '</td>';
                        echo '<td>' . $each->setNext() . '</td>';
                        echo '<td align="right">£' . $each->getAmount() . '</td>';
                        echo '<td><a href="' . $uri->expenditureEdit($each->getId()) . '"><i class="fa fa-edit"></i></a></td>';
                        echo '<td><a href="' . $uri->expenditureDelete($each->getId()) . '" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-trash"></i></a></td>';
                echo '</tr>';
                }
            }
            ?>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
            <tbody>
        </table>
        <div id="incomePiechart"></div>
    </div>


    <script type="text/javascript">
        $( "#showIncomeDiv" ).click(function() {
        $( "#incomeDiv" ).toggle( "slow", function() {
            // Animation complete.
        });
        });
    </script>


    <a id="showExpenditureDiv"><h2>Expenditure</h2></a>
    <div id="expenditureDiv">
        <a id="showExpenditure">Add new item</a>
        <div class="hidden" id="addExpenditure">
            <form method="POST">
                <label for="item">Item</label><br>
                <input type="text" name="item"><br>
                <label for="frequency">Frequency</label><br>
                <input type="number" name="frequency">
                <select name="unit">
                    <option value="365">Day</option>
                    <option value="52">Week</option>
                    <option value="12">Month</option>
                    <option value="1">Year</option>
                </select><br>
                <label for="start">Last payment</label><br>
                <input type="date" name="start"><br>
                <label for="amount">Amount</label><br>
                <input type="number" name="amount" step="0.01"><br>
                <input type="hidden" name="income" value="0"><br>
                <input type="submit" name="new" value="Add new expenditure">
            </form>
        </div>
        <script type="text/javascript">
        $( "#showExpenditure" ).click(function() {
        $( "#addExpenditure" ).toggle( "slow", function() {
            // Animation complete.
        });
        });
        </script>
        <table id="outgoing" class="display" style="width:100%">
            <thead>
                <th>Name</th>
                <th>Daily</th>
                <th>Weekly</th>
                <th>Monthly</th>
                <th>Yearly</th>
                <th>Next due</th>
                <th>Amount</th>
                <th>Edit</th>
                <th>Delete</th>
            </thead>
            <tbody>
            <?php
            foreach($bills as $each) {
                if (!$each->getIncome()) {
                    echo '<tr>';
                        echo '<td>' . $each->getName() . '</td>';
                        echo '<td align="right">£' . $each->getDaily() . '</td>';
                        echo '<td align="right">£' . $each->getWeekly() . '</td>';
                        echo '<td align="right">£' . $each->getMonthly() . '</td>';
                        echo '<td align="right">£' . $each->getAnnual() . '</td>';
                        echo '<td>' . $each->setNext() . '</td>';
                        echo '<td align="right">£' . $each->getAmount() . '</td>';
                        echo '<td><a href="' . $uri->expenditureDelete($each->getId()) . '"><i class="fa fa-edit"></i></a></td>';
                        echo '<td><a href="' . $uri->expenditureDelete($each->getId()) . '" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-trash"></i></a></td>';
                echo '</tr>';
                }
            }
            ?>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th style="text-align:right"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
        </table>
        <div id="outgoingPiechart"></div>

        </div>


    <script type="text/javascript">
        $( "#showExpenditureDiv" ).click(function() {
        $( "#expenditureDiv" ).toggle( "slow", function() {
            // Animation complete.
        });
        });
    </script>


    <a id="showTotalDiv"><h2>Totals</h2></a>
    <div id="totalDiv">
        <table id="totals" class="display" style="width:100%">
            <thead>
                <th>Name</th>
                <th>Daily</th>
                <th>Weekly</th>
                <th>Monthly</th>
                <th>Yearly</th>
            </thead>
            <tbody>
                <tr>
                    <td>Income</td>
                    <td><?php echo $totals['income']['daily']; ?></td>
                    <td><?php echo $totals['income']['weekly']; ?></td>
                    <td><?php echo $totals['income']['monthly']; ?></td>
                    <td><?php echo $totals['income']['yearly']; ?></td>
                </tr>
                <tr>
                    <td>Outgoing</td>
                    <td><?php echo $totals['out']['daily']; ?></td>
                    <td><?php echo $totals['out']['weekly']; ?></td>
                    <td><?php echo $totals['out']['monthly']; ?></td>
                    <td><?php echo $totals['out']['yearly']; ?></td>
                </tr>
                <tr>
                    <td>Remaining</td>
                    <td><?php echo round($totals['income']['daily'] - $totals['out']['daily'],2); ?></td>
                    <td><?php echo round($totals['income']['weekly'] - $totals['out']['weekly'],2); ?></td>
                    <td><?php echo round($totals['income']['monthly'] - $totals['out']['monthly'],2); ?></td>
                    <td><?php echo round($totals['income']['yearly'] - $totals['out']['yearly'],2); ?></td>

                </tr>
            </tbody>
        </table>
        <div id="totalPiechart"></div>

    </div>


    <script type="text/javascript">
        $( "#showTotalDiv" ).click(function() {
        $( "#totalDiv" ).toggle( "slow", function() {
            // Animation complete.
        });
        });
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#totals').DataTable({
            "bPaginate":false,
            "bFilter":false,
            "bSort":false
        })
    }),
    $(document).ready(function() {
        $('#income').DataTable({
            "bPaginate":false,

            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\£,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                totalDay = api
                    .column( 1 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalWeek = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalMonth = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalYear = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( 4 ).footer() ).html(
                    '£'+ totalYear.toFixed(2)
                );
                $( api.column( 3 ).footer() ).html(
                    '£'+ totalMonth.toFixed(2)
                );
                $( api.column( 2 ).footer() ).html(
                    '£'+ totalWeek.toFixed(2)
                );
                $( api.column( 1 ).footer() ).html(
                    '£'+ totalDay.toFixed(2)
                );
            }


        });
    } );

    $(document).ready(function() {
        $('#outgoing').DataTable({
            "bPaginate":false,

            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\£,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                totalDay = api
                    .column( 1 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalWeek = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalMonth = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                totalYear = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( 4 ).footer() ).html(
                    '£'+ totalYear.toFixed(2)
                );
                $( api.column( 3 ).footer() ).html(
                    '£'+ totalMonth.toFixed(2)
                );
                $( api.column( 2 ).footer() ).html(
                    '£'+ totalWeek.toFixed(2)
                );
                $( api.column( 1 ).footer() ).html(
                    '£'+ totalDay.toFixed(2)
                );
            }


        });
    } );

    // Pie charts
    google.charts.load('current', {'packages':['corechart','table']});

    // Pie charts on load
    google.charts.setOnLoadCallback(drawIncomeChart);
    google.charts.setOnLoadCallback(drawOutChart);
        google.charts.setOnLoadCallback(drawTotalChart);

    // Income chart
    function drawIncomeChart() {
    var data = google.visualization.arrayToDataTable([
    ['Bill', 'Amount'],
    <?php 
    // Get all the bills - weekly
    foreach ($bills as $each) {
        if ($each->getIncome()) {
                echo '["' . $each->getName() . '", ' . $each->getWeekly() . '],' . "\n";
        }
    }
    ?>
    ]);
    
    // Optional; add a title and set the width and height of the chart
    var options = {'title':'Money breakdown', 'width':4000, 'height':600, 'left':0};

    // Display the chart inside the <div> element with id="piechart"
    var chart = new google.visualization.PieChart(document.getElementById('incomePiechart'));
    chart.draw(data, options);
    };

    // Outgoing chart
    function drawOutChartBack() {
    var data = google.visualization.arrayToDataTable([
    ['Bill', 'Amount'],
    <?php 
    // Get all the bills - weekly
    foreach ($bills as $each) {
        if (!$each->getIncome()) {
                echo '["' . $each->getName() . '", ' . $each->getWeekly() . '],' . "\n";
        }
    }
    ?>
    ]);

    // Optional; add a title and set the width and height of the chart
    var options = {'title':'Money breakdown', 'width':800, 'height':600};

    // Display the chart inside the <div> element with id="piechart"
    var chart = new google.visualization.PieChart(document.getElementById('outgoingPiechart'));
    chart.draw(data, options);
    };

    function drawTotalChartBacak() {
        var data = google.visualization.arrayToDataTable([
            ['Bill', 'Amount'],
            <?php 
            // Get all the bills weekly
            foreach ($bills as $each) {
                if (!$each->getIncome()) {
                    echo '["' . $each->getName() . '",' . $each->getWeekly() . '],' . "\n";
                }
            }
            // Get the excess income
            echo '["Excess",' . round($totals['income']['weekly'] - $totals['out']['weekly'],2) . ']';
            ?>
        ]);
        // Add a title and set the width
        var options = {'title':'Money breakdown', 'width':800, 'height':600};
        // Display the chart
        var chart = new google.visualization.PieChart(document.getElementById('totalPiechart'));
        chart.draw(data, options);
    };

    function drawIncomeChart() {
                // Define the chart to be drawn.
                var data = google.visualization.arrayToDataTable([
                <?php
                $names = "['',";
                $amounts = "['',";
                    foreach ($bills as $each) {
                        if ($each->getIncome()) {
                            $names .= "'" . $each->getName() . "',";
                            $amounts .= $each->getAnnual() . ",";
                        }
                    }


                    $names .= "],";
                    $amounts .= "]";

                    echo $names;
                    echo $amounts;
                ?>

                ]);

                var options = {title: 'Income', isStacked:true, legend: {position: 'top'}, xAxis: {textPosition:'none'}};  

                // Instantiate and draw the chart.
                var chart = new google.visualization.BarChart(document.getElementById('incomePiechart'));
                chart.draw(data, options);
    };

    function drawOutChart() {
                // Define the chart to be drawn.
                var data = google.visualization.arrayToDataTable([
                <?php
                $names = "['',";
                $amounts = "['',";
                    foreach ($bills as $each) {
                        if (!$each->getIncome()) {
                            $names .= "'" . $each->getName() . "',";
                            $amounts .= $each->getAnnual() . ",";
                        }
                    }


                    $names .= "],";
                    $amounts .= "]";

                    echo $names;
                    echo $amounts;
                ?>

                ]);

                var options = {title: 'Expenditure', isStacked:true, legend: {position: 'top'}, xAxis: {textPosition:'none'}};  

                // Instantiate and draw the chart.
                var chart = new google.visualization.BarChart(document.getElementById('outgoingPiechart'));
                chart.draw(data, options);
    };

    function drawTotalChart() {
                // Define the chart to be drawn.
                var data = google.visualization.arrayToDataTable([
                <?php

                $names = "['','Expenditure','Excess'],";
                $amounts = "[''," . $totals['out']['yearly'] . "," . round($totals['income']['yearly'] - $totals['out']['yearly'],2) . "]";

                    echo $names;
                    echo $amounts;
                ?>

                ]);

                var options = {title: 'Excess', isStacked:true, legend: {position: 'top'}, xAxis: {textPosition:'none'}};  

                // Instantiate and draw the chart.
                var chart = new google.visualization.BarChart(document.getElementById('totalPiechart'));
                chart.draw(data, options);
    };



    </script>
<?php } ?>

</div>