<?php
$course = $_POST['course'] ?? '';
$components = $_POST['components'] ?? [];
$grades = $_POST['grades'] ?? [];

$total_percent = 0;
$total_weight = 0;

function showError($message) {
    echo "<!doctype html>
    <html>
    <head>
      <title>Error</title>
      <style>
        html, body {
          height: 100%;
          margin: 0;
          padding: 0;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          background: linear-gradient(135deg, #f0f4ff, #e6f2ff);
          display: flex;
          flex-direction: column;
        }
        h1 {
          color: #d9534f;
          margin-bottom: 25px;
          text-align: center;
        }
        .center {
          display: flex;
          justify-content: center;
          align-items: flex-start;
          flex: 1;
          padding: 40px 20px;
        }
        .result-box {
          background: #ffffff;
          padding: 30px 35px;
          border-radius: 15px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.1);
          border-top: 5px solid #d9534f;
          min-width: 300px;
          text-align: center;
        }
        .result-box p {
          font-size: 18px;
          margin: 15px 0;
          font-weight: 600;
          color: #d9534f;
        }
        a {
          display: inline-block;
          margin-top: 20px;
          text-decoration: none;
          padding: 10px 20px;
          border-radius: 8px;
          background: #007bff;
          color: #fff;
          font-weight: 600;
          transition: 0.3s;
        }
        a:hover {
          background: #0056b3;
        }
        footer {
          text-align: center;
          padding: 15px;
          font-size: 14px;
          color: #555;
          background: #f8f9fa;
          border-top: 1px solid #ddd;
        }
      </style>
    </head>
    <body>
      <div class='center'>
        <div class='result-box'>
          <h1>Error</h1>
          <p>$message</p>
          <a href='index.html'>Go Back</a>
        </div>
      </div>
      <footer>&copy; 2025 Created by <strong>Asim</strong></footer>
    </body>
    </html>";
    exit;
}

// Check if components exist
if (empty($components)) {
    showError("❌ No components provided. Please enter at least one component.");
}

// Check if grades exist
if (empty($grades)) {
    showError("❌ No grade ranges provided. Please define grading criteria.");
}

// Loop through each component
foreach ($components as $compIndex => $comp) {
    $weight = $comp['weight'];
    $items = $comp['items'];

    if (!is_numeric($weight) || $weight <= 0) {
        showError("❌ Invalid weight for Component " . ($compIndex+1) . ".");
    }

    if (empty($items)) {
        showError("❌ No items provided for Component " . ($compIndex+1) . ".");
    }

    $obtained_sum = 0;
    $total_sum = 0;

    foreach ($items as $itemIndex => $item) {
        $obtained = $item['obtained'];
        $total = $item['total'];

        if (!is_numeric($obtained) || !is_numeric($total) || $total <= 0) {
            showError("❌ Invalid total marks in Component " . ($compIndex+1) . " Item " . ($itemIndex+1) . ".");
        }

        if ($obtained > $total) {
            showError("❌ Obtained marks cannot exceed total marks for Component " . ($compIndex+1) . " Item " . ($itemIndex+1) . ".");
        }

        $obtained_sum += $obtained;
        $total_sum += $total;
    }

    $component_percent = ($total_sum > 0) ? ($obtained_sum / $total_sum) * 100 : 0;

    $weighted_score = ($component_percent * $weight) / 100;
    $total_percent += $weighted_score;
    $total_weight += $weight;
}

// Validate total weight = 100
if ($total_weight != 100) {
    showError("❌ The total weight must equal 100%. You entered $total_weight%.");
}

$grade = "N/A";
foreach ($grades as $g) {
    if (!isset($g['letter'], $g['min'], $g['max'])) continue;
    $letter = $g['letter'];
    $min = $g['min'];
    $max = $g['max'];

    if (!is_numeric($min) || !is_numeric($max)) continue;

    if ($total_percent >= $min && $total_percent <= $max) {
        $grade = $letter;
        break;
    }
}
?>

<!doctype html>
<html>
<head>
  <title>Result</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f0f4ff, #e6f2ff);
      display: flex;
      flex-direction: column;
    }
    .center {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      flex: 1;
      padding: 40px 20px;
    }
    h1 {
      color: #1a1a1a;
      margin-bottom: 25px;
      text-align: center;
    }
    .result-box {
      background: #ffffff;
      padding: 30px 35px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      border-top: 5px solid #007bff;
      min-width: 300px;
      text-align: center;
    }
    .result-box p {
      font-size: 20px;
      margin: 15px 0;
      font-weight: 600;
    }
    .total-percent { color: #007bff; }
    .grade { font-weight: 700; }
    .grade.green { color: #28a745; }
    .grade.red { color: #d9534f; }
    .grade.yellow { color: #ffc107; }
    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      background: #007bff;
      color: #fff;
      font-weight: 600;
      transition: 0.3s;
    }
    a:hover { background: #0056b3; }
    footer {
      text-align: center;
      padding: 15px;
      font-size: 14px;
      color: #555;
      background: #f8f9fa;
      border-top: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <div class="center">
    <div class="result-box">
      <h1>Result for <?php echo htmlspecialchars($course); ?></h1>
      <p>Total Percentage: <span class="total-percent"><?php echo round($total_percent, 2); ?>%</span></p>
      <p>Grade: 
        <?php 
          $gradeClass = "green";
          if ($grade === "F") $gradeClass = "red";
          elseif ($grade === "N/A") $gradeClass = "yellow";
        ?>
        <span class="grade <?php echo $gradeClass; ?>"><?php echo htmlspecialchars($grade); ?></span>
      </p>
      <a href="index.html">Go Back</a>
    </div>
  </div>
  <footer>&copy; 2025 Created by <strong>Asim</strong></footer>
</body>
</html>
