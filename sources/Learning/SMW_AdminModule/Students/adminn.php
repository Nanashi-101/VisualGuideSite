<?php
/* Faqja (home.php) e cila paraqitet pasi perdoruesi te llogohet me sukses */
include("check.php");	
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        /* Prevent layout from spilling horizontally */
* { box-sizing: border-box; }
html, body { margin: 0; overflow-x: hidden; }

/* Media scaling */
img, svg, video { max-width: 100%; height: auto; }

/* Optional: quickly spot the offender during dev */
/* html { outline: 0; } */
/* * { outline: 0.5px dashed transparent; } */

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #CE5327;
            --background-light: #f4f4f4;
            --background-dark: #2c3e50;
            --text-light: #ffffff;
            --text-dark: #2c3e50;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .dark-mode {
            background-color: var(--background-dark);
            color: var(--text-light);
        }

        header {
            background-color: var(--secondary-color);
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            position: relative;
        }

        header .dark-toggle,
        header .logout-button {
            position: absolute;
            top: 1rem;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
        }

        header .dark-toggle { right: 3.5rem; }
        header .logout-button { right: 1rem; }

        nav {
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 1rem;
            flex-wrap: wrap;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        section {
            padding: 2rem;
        }

        footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 1rem;
        }

        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #CE5327;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: var(--primary-color);
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        img {
            border-radius: 4px;
        }

        .action-links a {
            text-decoration: none;
            color: var(--secondary-color);
        }

        .action-links a:hover {
            color: #16a085;
        }

        .section-divider {
    margin: 40px auto 20px;
    padding-top: 20px;
    border-top: 3px solid var(--primary-color);
    text-align: center;
}

.section-divider h2 {
    color: var(--primary-color);
    font-size: 24px;
    display: inline-block;
    background-color: var(--background-light);
    padding: 0 10px;
    position: relative;
    top: -14px;
}
    </style>
</head>
<body>
    <header>
        <h1>Students Management Webapplication</h1>
        <button class="dark-toggle" onclick="toggleDarkMode()" title="Ndrysho modalitetin (Dark Mode)">
            <i class="fas fa-moon"></i>
        </button>
        <button class="logout-button" onclick="logout()" title="Çkyçu nga aplikacioni">
            <i class="fas fa-right-from-bracket"></i>
        </button>
    </header>

    <nav>
        <a href="../admin.php">Home</a>
       
        
    </nav>

    <section>
        <div class="form-container">
            <h3>Shto të dhënat e studentit</h3>
            <form enctype="multipart/form-data" action="addStudent.php" method="post" name="form1">
                <div class="form-group">
                    <label for="ID_Faculty">Faculty</label>
                    <select name="ID_Faculty" required>
                        <option value="">Select Faculty</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM Faculties");
                        while ($row = $res->fetch_array()) {
                            echo "<option value='{$row['ID_Faculty']}'>{$row['Faculty']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>SName</label>
                    <input type="text" name="SName" required>
                </div>
                <div class="form-group">
                    <label>Surname</label>
                    <input type="text" name="Surname" required>
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input type="date" name="Birthdate" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="Address">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="Description">
                </div>
                <div class="form-group">
                    <label>Upload Photo</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                    <input name="userfile" type="file" />
                </div>
                <input type="submit" name="addStudent" value="Add">
            </form>
        </div>
        <div class="section-divider">
    <h2><i class="fas fa-users"></i> Students List</h2>
</div>
        <div class="form-container">
        <h3>Search for Student Data for Managing</h3>
<form action="" method="post" onsubmit="addWildcard()">
    <div class="form-group">
        <label for="term">Write name or surname of student</label>
        <input type="text" name="term" id="term" placeholder="SName ose Surname" />
    </div>
    <input type="submit" value="Search">
</form>

<script>
    function addWildcard() {
        var termInput = document.getElementById("term");
        
        // Shto '%' nëse input është bosh
        if (termInput.value.trim() === "") {
            termInput.value = "%";
        }
    }
</script>

            <?php
            if (!empty($_REQUEST['term'])) {
                $term = mysqli_real_escape_string($conn, $_REQUEST['term']);
                $sql = mysqli_query($conn, "
                    SELECT s.ID_Student, s.SName, s.Surname, s.Birthdate, s.Address, s.Description,
                           f.Faculty, s.image, s.name
                    FROM Students s
                    INNER JOIN Faculties f ON s.ID_Faculty = f.ID_Faculty
                    WHERE s.SName LIKE '%$term%' OR s.Surname LIKE '%$term%' OR s.Address LIKE '%$term%'
                ");

                
                echo "<table>
                        <tr>
                            <th>SName</th>
                            <th>Surname</th>
                            <th>Birthdate</th>
                            <th>Address</th>
                            <th>Description</th>
                            <th>Faculty</th>
                            <th>Foto</th>
                            <th>SName file-it</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>";
                while ($row = mysqli_fetch_array($sql)) {
                    echo "<tr>
                            <td>{$row['SName']}</td>
                            <td>{$row['Surname']}</td>
                            <td>{$row['Birthdate']}</td>
                            <td>{$row['Address']}</td>
                            <td>{$row['Description']}</td>
                            <td>{$row['Faculty']}</td>
                            <td><img src='data:image/jpeg;base64,".base64_encode($row['image'])."' width='100%' height='8%'/></td>
                            <td>{$row['name']}</td>
                            <td class='action-links'><a href='update_student.php?ID_Student={$row['ID_Student']}'>Edit</a></td>
                            <td class='action-links'><a href='delete_student.php?ID_Student={$row['ID_Student']}' onclick=\"return confirm('Are you sure you want to delete data?')\">Delete</a></td>
                        </tr>";
                }
                echo "</table>";
            }
            ?>
        </div>
    </section>

    <footer>
        Copyright &copy; LNH <br>
        Referencat: <a href="https://www.srh-university.de/en/" style="color: var(--secondary-color);">SRH</a>
    </footer>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function logout() {
           // alert("You are logout...");
            location.href = "../logout.php";
        }
    </script>
</body>
</html>
