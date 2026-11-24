<form method="POST">
  Enter a number: <input type="number" name="num">
  <input type="submit" value="Generate">
</form>

<?php
if (isset($_POST['num'])) {
    $num = $_POST['num'];

    echo "<h3>Multiplication Table for $num</h3>";
    echo "<table border='1' cellpadding='5'>";
    for ($i = 1; $i <= 10; $i++) {
        echo "<tr><td>$num x $i</td><td>" . ($num * $i) . "</td></tr>";
    }
    echo "</table>";
}
?>
