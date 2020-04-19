<?php
	if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
			die('Direct access not allowed');
			exit();
	};
	require '../config.php';
	require 'functions.php';  
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Coolflix Database</title>
		<link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="/assets/css/style.css" rel="stylesheet"/>
	</head>
		<body role="document">
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="true">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a href="/" class="navbar-brand">CoolFlix Database</a>
			</div>

			<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1" aria-expanded="true">
			  <ul class="nav navbar-nav">
				<li><a href="/newuser.php">New User</a></li>
				<li><a href="/newplan.php">New Plan</a></li>
				<li><a href="/newexpense.php">New Expense</a></li>
				<li><a id="toggleview" href="#">Toggle Inactive</a></li>
				<li><a href="/endpoints.php">API Info</a></li>
				<li><a href="/adminer.php">Adminer</a></li>
			  </ul>
			</div>
		  </div>
		</nav>
	<div class="container-fluid theme-showcase" role="main"> 