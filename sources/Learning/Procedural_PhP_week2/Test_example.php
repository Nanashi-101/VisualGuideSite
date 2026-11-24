
<?php
// ----- CONFIG -----
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'school';

// ----- CONNECT -----
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

// DB & table bootstrap
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$DB_NAME`");
mysqli_select_db($conn, $DB_NAME);

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  grade DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// helper
function clean($s) { return trim($s ?? ''); }

$errors = [];
$ok = null;

// ----- INSERT (POST) -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = clean($_POST['name'] ?? '');
  $email = clean($_POST['email'] ?? '');
  $grade = clean($_POST['grade'] ?? '');

  if ($name === '')  $errors[] = 'Name is required';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
  if ($grade === '' || !is_numeric($grade)) $errors[] = 'Numeric grade required';
  else {
    $g = floatval($grade);
    if ($g < 0 || $g > 100) $errors[] = 'Grade must be 0–100';
  }

  if (!$errors) {
    $stmt = mysqli_prepare($conn, "INSERT INTO students(name,email,grade) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssd', $name, $email, $g);
    if (mysqli_stmt_execute($stmt)) $ok = 'Student added.';
    else $errors[] = 'Insert failed: ' . mysqli_error($conn);
    mysqli_stmt_close($stmt);
  }
}

// ----- FETCH -----
$res = mysqli_query($conn, "SELECT id,name,email,grade,created_at FROM students ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Procedural PHP – Students</title>
  <style>
    body { font-family: Arial, sans-serif; margin:24px; }
    form, table { max-width: 760px; }
    label { display:block; margin-top:10px; font-weight:bold; }
    input[type=text],input[type=email],input[type=number]{ width:100%; padding:8px; }
    input[type=submit]{ margin-top:12px; padding:8px 14px; }
    .msg{margin:10px 0; padding:8px; border:1px solid #ddd;}
    .ok{background:#ecfff0; border-color:#b6e2c0;}
    .err{background:#fff1f1; border-color:#f0b7b7;}
    table{ width:100%; border-collapse:collapse; margin-top:18px; }
    th,td{ border:1px solid #ddd; padding:8px; text-align:left; }
    th{ background:#f7f7f7; }
  </style>
</head>
<body>
  <h1>Students (Procedural PHP)</h1>

  <?php if ($ok): ?><div class="msg ok"><?=htmlspecialchars($ok)?></div><?php endif; ?>
  <?php if ($errors): ?>
    <div class="msg err">
      <strong>Fix the following:</strong>
      <ul><?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <h2>Add Student</h2>
    <label>Name</label>
    <input type="text" name="name" placeholder="Name and Surname" required>
    <label>Email</label>
    <input type="email" name="email" placeholder="x@y.com" required>
    <label>Grade (0–100)</label>
    <input type="number" name="grade" step="0.01" min="0" max="100" placeholder="95" required>
    <input type="submit" value="Add">
  </form>

  <h2>All Students</h2>
  <table>
    <thead><tr>
      <th>#</th><th>Name</th><th>Email</th><th>Grade</th><th>Classification</th><th>Created</th>
    </tr></thead>
    <tbody>
      <?php
      if ($res && mysqli_num_rows($res) > 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($res)) {
          $g = (float)$row['grade'];
          // if/else grade classification
          if ($g >= 90)      { $label = 'A (Excellent)'; }
          elseif ($g >= 80)  { $label = 'B (Very Good)'; }
          elseif ($g >= 70)  { $label = 'C (Good)'; }
          elseif ($g >= 50)  { $label = 'D (Satisfactory)'; }
          else               { $label = 'F (Fail)'; }

          echo '<tr>';
          echo '<td>'.($i++).'</td>';
          echo '<td>'.htmlspecialchars($row['name']).'</td>';
          echo '<td>'.htmlspecialchars($row['email']).'</td>';
          echo '<td>'.number_format($g, 2).'</td>';
          echo '<td>'.$label.'</td>';
          echo '<td>'.htmlspecialchars($row['created_at']).'</td>';
          echo '</tr>';
        }
      } else {
        echo '<tr><td colspan="6">No data yet.</td></tr>';
      }
      if ($res) mysqli_free_result($res);
      mysqli_close($conn);
      ?>
    </tbody>
  </table>
</body>
</html>
