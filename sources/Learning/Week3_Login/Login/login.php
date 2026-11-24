<?php
/* Through this faile it is checked if the login is done sucessfully, if the username ans password that you have field in the form is on MySQL db or not.
 */
	session_start();
	include("config.php"); //Establishing connection with our database
	
	$error = ""; //Variable for storing our errors.
	if(isset($_POST["Submit"]))
	{
		if(empty($_POST["username"]) || empty($_POST["password"]))
		{
			$error = "Both fields are required.";
		}else
		{
			// Define $username and $password
			$username=$_POST['username'];
			$password=$_POST['password'];
			//Check username and password from database
			$sql="SELECT uid FROM users WHERE username='$username' 
			and password='$password'";
			$result=mysqli_query($conn,$sql);
			$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
			//If username and password exist in  database then create a session.
			//Otherwise it should appear the error.
			if(mysqli_num_rows($result) == 1)
			{
				$_SESSION['username'] = $username; // Initializing Session
				header("location: home.php"); // Redirecting To Other Page
			}else
			{
				$error = "Incorrect username or password.";
			}
		}
	}
?>