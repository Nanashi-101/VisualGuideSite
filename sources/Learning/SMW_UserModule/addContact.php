<html>
<head>
	<title>Students Managemwnt Webapplication (SMW)</title>
			<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="stylesheet" href="assets/css/main.css" />
		
</head>
<body>
<?php
//including the database connection file
include_once("config.php");

if(isset($_POST['addContact'])) {	
	$Subject = $_POST['Subject'];
	$Message = $_POST['Message'];
	$Email = $_POST['Email'];
		
	// checking empty fields
	if(empty($Subject) || empty($Message) || empty($Email)) {			
		if(empty($Subject)) {echo "<font color='red'>Subject field is empty.</font><br/>";}
		if(empty($Message)) {echo "<font color='red'>Message field is empty.</font><br/>";}
		if(empty($Email)) {echo "<font color='red'>Email field is empty.</font><br/>";}
		//link to the previous Message
		echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
	} else { 
		// if all the fields are filled (not empty) 
		//insert data to database	
		$result = mysqli_query($conn, "INSERT INTO Contacts(Subject,Message,Email) VALUES('$Subject','$Message','$Email')");
		//display success messMesazhi
	//	echo "<font color='green'>Data added successfully.";
		//echo "<br/><a href='contact.php'>View Result</a>";
		echo "<script>
         setTimeout(function(){
            window.location.href = 'index.php';
         }, 5000);
      </script>";
		 echo"<p><b>   The data is being recorded in the system. Please wait 5 seconds.
 <b></p>";
	
	}
}
?>
</body>
</html>
