<?php
/* Faqja (home.php) e cila paraqitet pasi perdoruesi te llogohet me sukses */
	//include("check.php");	
?>

<?php
// including the database connection file
include_once("config.php");

if(isset($_POST['update_student']))
{	
	$ID_Student = $_POST['ID_Student'];
	$SName=$_POST['SName'];
	$Surname=$_POST['Surname'];
	$Birthdate=$_POST['Birthdate'];	
	$Address=$_POST['Address'];
	$Description=$_POST['Description'];		
	$ID_Faculty=$_POST['ID_Faculty'];	
	
	$imgData =addslashes (file_get_contents($_FILES['userfile']['tmp_name']));
	$name = $_FILES['userfile']['name'];
	$maxsize = 10000000; //set to approx 10 MB
	
	// checking empty fields
	if(empty($SName) || empty($Surname) || empty($Birthdate) || empty($Address)) {	
			
		if(empty($SName)) {
			echo "<font color='red'>SName field is empty.</font><br/>";
		}
		
		if(empty($Surname)) {
			echo "<font color='red'>Surname field is empty.</font><br/>";
		}
		
		if(empty($Birthdate)) {
			echo "<font color='red'>Birthdate field is empty.</font><br/>";
		}
		if(empty($Address)) {
			echo "<font color='red'>Address field is empty.</font><br/>";
		}		
	} else {	
		//updating the table
		$result = mysqli_query($conn,"UPDATE Students SET SName='$SName',Surname='$Surname',Birthdate='$Birthdate',Address='$Address', Description='$Description', ID_Faculty='$ID_Faculty', image='$imgData', name='$name' WHERE ID_Student=$ID_Student");
		
		//redirectig to the display message. In our case, it is ballina.php
		header("Location: adminn.php");
	}
}
?>
<?php
//getting ID_Studenti from url
$ID_Student = $_GET['ID_Student'];

//selecting data associated with this particular ID_Studenti
$result = mysqli_query($conn,"SELECT * FROM Students WHERE ID_Student=$ID_Student");

while($res = mysqli_fetch_array($result))
{
	$SName = $res['SName'];
	$Surname = $res['Surname'];
	$Birthdate = $res['Birthdate'];
	$Address = $res['Address'];
	$Description=$res['Description'];	
	$ID_Faculty = $res['ID_Faculty'];
}
?>
<!DOCTYPE html>
<html lang="sq">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        header .dark-toggle {
            right: 3.5rem;
        }

        header .logout-button {
            right: 1rem;
        }

        header .dark-toggle:hover,
        header .logout-button:hover {
            color: #ddd;
        }

        nav {
            background-color: var(--primary-color);
            color: white;
            width: 100%;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav a:hover {
            color: var(--secondary-color);
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

        .form-container,
        .search-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #16a085;
        }
    </style>
</head>

<body>
    <header>Students Management Webapplication (SMW)</h1>
        <button class="dark-toggle" onclick="toggleDarkMode()" title="CHange modality (Dark Mode)">
            <i class="fas fa-moon"></i>
        </button>
        <button class="logout-button" onclick="logout()" title="Logout">
            <i class="fas fa-right-from-bracket"></i>
        </button>
    </header>


    <nav>
        <a href="../Admin/admin.php">Home</a>
       
    </nav>

    <section>
	<div class="form-container">
            <h3>Add Dtudent data</h3>
            <form enctype="multipart/form-data" action="update_student.php" method="post" >
            
	<div class="form-group">
                    <label for="ID_Faculty">Faculty</label>
                    <select name="ID_Faculty" required>
    <option value="">Select Faculty</option>
    <?php
    $res = mysqli_query($conn, "SELECT * FROM Faculties");
    while ($row = $res->fetch_array()) {
        $selected = ($row['ID_Faculty'] == $ID_Faculty) ? 'selected' : '';
        echo "<option value='{$row['ID_Faculty']}' $selected>{$row['Faculty']}</option>";
    }
    ?>
</select>

                </div>
                <div class="form-group">
                    <label>SName</label>
                    <input type="text" name="SName" value='<?php echo $SName;?>' required>
                </div>
                <div class="form-group">
                    <label>Surname</label>
                    <input type="text" name="Surname" value='<?php echo $Surname;?>' required>
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input type="date" name="Birthdate" value='<?php echo $Birthdate;?>' required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="Address" value='<?php echo $Address;?>' >
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="Description" value='<?php echo $Description;?>'  >
                </div>

<?php

                // 1) Lexo ID-në (GET/POST) dhe merr rreshtin nga DB
$studentId = isset($_GET['ID_Student']) ? (int)$_GET['ID_Student'] : (int)($_POST['ID_Student'] ?? 0);

$row = null;
if ($studentId > 0) {
    // Përdor prepared statement për siguri
    $stmt = $conn->prepare("SELECT ID_Student, name, image FROM Students WHERE ID_Student = ? LIMIT 1");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); // nëse s’gjen, $row mbetet null
    $stmt->close();
}

// 2) Përdor variabla të sigurta pa shkaktuar warning
$currentFile = $row['name']  ?? null; // SName i skedarit në /uploads
$currentBlob = $row['image'] ?? null; // BLOB nga DB (opsional)

// 3) Rrugët e fotove
$uploadDirFs = __DIR__ . "/uploads/";  // path në disk
$uploadUrl   = "uploads/";             // path për <img src=>

// 4) Vendos çfarë të shfaqet
echo '<div class="form-group">';
echo '  <label>Foto aktuale</label><br>';

if ($currentFile && is_file($uploadDirFs . $currentFile)) {
    // Foto nga folderi uploads/
    $src = $uploadUrl . htmlspecialchars($currentFile, ENT_QUOTES, 'UTF-8');
    echo "  <img src='{$src}' width='120' height='140' style='border:1px solid #ccc; margin-bottom:8px;' alt='Acual Photo'>";
} elseif (!empty($currentBlob)) {
    // Foto nga BLOB si data URI (fallback)
    $base64 = base64_encode($currentBlob);
    echo "  <img src='data:image/jpeg;base64,{$base64}' width='120' height='140' style='border:1px solid #ccc; margin-bottom:8px;' alt='Actual Photo'>";
} else {
    // Asnjë foto e ruajtur
    echo "  <p style='color:#b00;'>⚠️ Does not have saved photo.</p>";
}

echo '</div>';
?>

                <div class="form-group">
                    <label>Upload Photo</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                    <input name="userfile" type="file" />
                </div>
                
				<div class="form-group">
      
				<input type="hidden" name="ID_Student" value='<?php echo $_GET['ID_Student'];?>' /></td>
				<input type="submit" name="update_student" value="Update"></td>

                
				</div>
		
	</form>
	
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
            alert("You are logout from webapplication...");
            // location.href = "logout.php"; // ose një URL reale për logout
        }
    </script>
</body>

</html>
