<?php

//user_action.php

include('admin/soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	
	if($_POST["action"] == 'profile')
	{
		sleep(2);

		$error = '';

		$success = '';


	
			
			$object->data = array(
				
				':user_password'	=>	$_POST['user_password'],
				
			);

			$object->query = "
			UPDATE student_soes 
			SET student_password = :user_password 
			WHERE student_id = ".$_SESSION['student_id']
			;

			$object->execute_query();

			$success = '<div class="alert alert-success">Password  Updated</div>';
		}

		$output = array(
			'error'				=>	$error,
			'success'			=>	$success,
			
			'user_password'		=>	$_POST["user_password"],
		
		);

		echo json_encode($output);
	}






?>