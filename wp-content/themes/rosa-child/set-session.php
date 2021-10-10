<?php
session_start();
if(isset($_POST['chilantroSession'])){
	$_SESSION['chilantro'] = $_POST['chilantroSession'];
	echo $_SESSION['chilantro'];
}
?>