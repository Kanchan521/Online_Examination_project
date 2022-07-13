<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Online Student  Management System in PHP</title>

	    <!-- Custom styles for this page -->
	    <link href="vendor/bootstrap/bootstrap.min.css" rel="stylesheet">

	    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

	    <!-- Custom styles for this page -->
    	<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

	    <link rel="stylesheet" type="text/css" href="vendor/parsley/parsley.css"/>
	    <link rel="stylesheet" type="text/css" href="vendor/TimeCircle/TimeCircles.css"/>
	    <style>
	    	.border-top { border-top: 1px solid #e5e5e5; }
			.border-bottom { border-bottom: 1px solid #e5e5e5; }

			.box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
	    </style>
	</head>
	<body>
	
		<?php
		if($object->is_student_login())
		{
		?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-info">
		  	<a class="navbar-brand" href="#">Student Exam System</a>
		  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
		    	<span class="navbar-toggler-icon"></span>
		  	</button>
		  	<div class="collapse navbar-collapse" id="navbarText">
		    	
				<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
			<i class="fa fa-bars"></i>
		</button>

		<!-- Topbar Navbar -->
		<ul class="navbar-nav ml-auto">

			<div class="topbar-divider d-none d-sm-block"></div>

			<?php
			$object->query = "
			SELECT * FROM student_soes 
			WHERE student_id = '".$_SESSION['student_id']."'
			";

			$user_result = $object->get_result();

			$user_name = '';
			$user_profile_image = '';
			foreach($user_result as $row)
			{
				if($row['student_name'] != '')
				{
					$user_name = $row['student_name'];
				}
				else
				{
					$user_name = 'Student';
				}

				if($row['student_image'] != '')
				{
					 
					 $user_profile_image = str_replace("../", "", $row["student_image"]);
				}
				else
				{
					$user_profile_image = '../img/undraw_profile.svg';
				}
			}
			?>

			<!-- Nav Item - User Information -->
			<li class="nav-item dropdown no-arrow">
				<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
					data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="mr-2 d-none d-lg-inline text-light small" id="user_profile_name"><strong>Welcome <?php echo $user_name; ?></strong></span>
					<img class="img-profile rounded-circle"
						src="<?php echo $user_profile_image; ?>" id="user_profile_image" height="40px" width="40px">
				</a>
				<!-- Dropdown - User Information -->
				<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
					aria-labelledby="userDropdown">
					<a class="dropdown-item" href="profile.php">
						<i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
						Profile
					</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="logout.php">
						<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
						Logout
					</a>
				</div>
			</li>

		</ul>

	<!-- </nav> -->
		  	</div>
		</nav>
		<?php
		}
		else
		{
		?>
		<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
		    <h5 class="my-0 mr-md-auto font-weight-normal">Student Workspace</h5>
		    
	    </div>

	    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
	      	<h1 class="display-4">Online Student Management System</h1>
	    </div>
	    <br />
	    <br />
	    <?php
		}
	    ?>

	<!-- End of Topbar -->

	<!-- Begin Page Content -->
	
	    <div class="container-fluid">