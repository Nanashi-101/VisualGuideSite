<html>

	<head>
		<title>Moduli Administratorit</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="stylesheet" href="assets/css/main.css" />
		
		
		 
	</head

<body>
<?php
//including the database connection file
include_once("config.php");

if(isset($_POST['addStudent'])) {	
	$SName = $_POST['SName'];
	$Surname = $_POST['Surname'];
	$Birthdate = $_POST['Birthdate'];
	$Address = $_POST['Address'];
	$Description = $_POST['Description'];
	$ID_Faculty = $_POST['ID_Faculty'];
	
	
	$imgData =addslashes (file_get_contents($_FILES['userfile']['tmp_name']));
	$name = $_FILES['userfile']['name'];
	
	 $maxsize = 10000000; //set to approx 10 MB
	

	
	
	// checking empty fields
	if(empty($SName) || empty($Surname)|| empty($Birthdate) || empty($Address)) {
				
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
		
		//link to the previous pMbiemri
		echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
	} else { 
		// if all the fields are filled (not empty) 
			
		//insert data to database	
		$result = mysqli_query($conn, "INSERT INTO Students(SName,Surname,Birthdate,Address, Description, ID_Faculty, image, name) VALUES('$SName','$Surname','$Birthdate','$Address', '$Description', '$ID_Faculty', '$imgData', '$name')");
		// Succes message para redirect
echo "<p style='color:green; font-weight:bold;'>
✅ Data are registering. Please wait 5 seconds...
</p>";

// JavaScript redirect pas 5 sekondave
echo "<script>
setTimeout(function(){
    window.location.href = 'adminn.php';
}, 5000);
</script>";

	}
}
?>

</body>
</html>
