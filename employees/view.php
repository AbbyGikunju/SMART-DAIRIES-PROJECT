<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'biodairy';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch milk purchase records, along with the employees and farmers involved
$sql = "SELECT employees.username AS employee_username, farmers.username AS farmer_username, 
               SUM(milk_quantity) AS total_milk, purchase_date 
        FROM milk_purchases
        JOIN users AS employees ON milk_purchases.username = employees.username
        JOIN users AS farmers ON milk_purchases.farmer_username = farmers.username
        GROUP BY employees.username, farmers.username, purchase_date 
        ORDER BY purchase_date DESC";

// Execute query
$result = $conn->query($sql);
$data = array(); // Initialize an array to store data for the chart
$total_milk_collected = 0; // Variable to store the total milk collected

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'employee' => $row['employee_username'],
            'farmer' => $row['farmer_username'],
            'milk_quantity' => (int) $row['total_milk'],
            'purchase_date' => $row['purchase_date']
        );
        // Add to total milk collected
        $total_milk_collected += (int) $row['total_milk'];
    }
} else {
    echo "No records found.";
}

$conn->close();

// Format the data into a JavaScript-friendly format for Google Charts
$chart_data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Quantity Display</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        // Load the Visualization API and the linechart package.
        google.charts.load('current', {
            packages: ['corechart', 'line']
        });

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            
            // Define the columns for the chart
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Milk Quantity');

            // Parse PHP data and format it into a Google Charts-friendly format
            var chartData = <?php echo $chart_data; ?>;

            // Process the data into the format required for the chart
            var chartRows = [];
            chartData.forEach(function(record) {
                chartRows.push([record.purchase_date, record.milk_quantity]);
            });

            // Add the rows to the data table
            data.addRows(chartRows);

            // Create a chart options object
            var options = {
                title: 'Milk Purchase Trend',
                curveType: 'function',
                legend: { position: 'bottom' },
                hAxis: {
                    title: 'Purchase Date',
                    slantedText: true,
                    slantedTextAngle: 45
                },
                vAxis: {
                    title: 'Milk Quantity (liters)'
                },
                height: '100%',
                width: '100%',
            };

            // Instantiate and draw the chart, passing in the options
            var chart = new google.visualization.LineChart(document.getElementById('linechart_material'));
            chart.draw(data, options);
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            width: 90%;
            max-width: 1200px;
        }
        #linechart_material {
            width: 100%;
            height: 500px; /* Ensure chart is responsive */
        }
        .total-milk {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4 text-primary">Milk Purchase Trend (Employee vs Farmer)</h1>
    <!-- Display the total milk collected -->
    <div class="total-milk">
        Total Milk Collected: <?php echo $total_milk_collected; ?> Liters
    </div>
    <!-- Line chart container -->
    <div id="linechart_material"></div>
</div>

</body>
</html>
