<?php

//classes_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('class_name', 'class_status');

		$output = array();

		$main_query = "
		SELECT * FROM class_soes ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE class_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR class_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY class_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute_que();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query;

		$object->execute_que();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = html_entity_decode($row["class_name"]);
			$syllabus = '<a href="../syllabus/'.$row["syllabus"].'" target="_blank" >Syllabus</a>';
			$sub_array[] = $syllabus;
			$status = '';
			if($row["class_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["class_id"].'" data-status="'.$row["class_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["class_id"].'" data-status="'.$row["class_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["class_id"].'"><i class="fas fa-edit"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$object->data = array(
			':class_name'	=>	$_POST["class_name"]
		);

		$object->query = "
		SELECT * FROM class_soes 
		WHERE class_name = :class_name
		";

		$object->execute_query();

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Class Name Already Exists</div>';
		}
		else
		{    if($_FILES["syllabus"]["name"] != '')
			{
				
               $file_name = $_FILES['syllabus']['name'];
			    $tmp_name = $_FILES['syllabus']['tmp_name'];
				$time = time();
				$new_name = $time.$file_name;
				move_uploaded_file($tmp_name,"../syllabus/".$new_name);
				$object->data = array(
			     	':class_name'			=>	$object->clean_input($_POST["class_name"]),
			    	':syllabus'             =>   $new_name,
				    ':class_code'			=>	md5(uniqid()),
				    ':class_status'			=>	'Enable',
				    ':class_created_on'		=>	$object->now
			);

			$object->query = "
			INSERT INTO class_soes 
			(class_name,syllabus, class_code, class_status, class_created_on) 
			VALUES (:class_name,:syllabus, :class_code, :class_status, :class_created_on)
			";

			$object->execute_query();

			$success = '<div class="alert alert-success">Class Added</div>';
		
		}
	}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM class_soes 
		WHERE class_id = '".$_POST["class_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['class_name'] = $row['class_name'];
			$data['syllabus'] = $row['syllabus'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$object->data = array(
			':class_name'	=>	$_POST["class_name"],
			':class_id'		=>	$_POST['hidden_id']
		);

		$object->query = "
		SELECT * FROM class_soes 
		WHERE class_name = :class_name 
		AND class_id != :class_id
		";

		$object->execute_query();

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Class Name Already Exists</div>';
		}
		else
		{

         if($_FILES["syllabus"]["name"] != '')
			{
				
               $file_name = $_FILES['syllabus']['name'];
			    $tmp_name = $_FILES['syllabus']['tmp_name'];
				$time = time();
				$new_name = $time.$file_name;
				move_uploaded_file($tmp_name,"../syllabus/".$new_name);
			$object->data = array(
				':class_name'		=>	$object->clean_input($_POST["class_name"]),
				':syllabus'             =>   $new_name,
			);

			$object->query = "
			UPDATE class_soes 
			SET class_name = :class_name ,syllabus=:syllabus
			WHERE class_id = '".$_POST['hidden_id']."'
			";

			$object->execute_query();

			$success = '<div class="alert alert-success">Class Data Updated</div>';
		}
	}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$object->data = array(
			':class_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE class_soes 
		SET class_status = :class_status 
		WHERE class_id = '".$_POST["id"]."'
		";

		$object->execute_query();

		echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM class_soes 
		WHERE class_id = '".$_POST["id"]."'
		";

		$object->execute_que();

		echo '<div class="alert alert-success">Class Data Deleted</div>';
	}
}

?>