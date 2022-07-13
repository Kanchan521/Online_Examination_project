<?php

//classes_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('subject_name', 'status');

		$output = array();

		$main_query = "
		SELECT * FROM question_bank_soes ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE subject_name LIKE "%'.$_POST["search"]["value"].'%" ';
			
			$search_query .= 'OR status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY qpdf_id DESC ';
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
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$notes = '<a href="../questionbank/'.$row["question_bank"].'" target="_blank" >'.$row["question_bank"].'</a>';
			$sub_array[] = $notes;
            $sub_array[]=$row["added_on"];
			$status = '';
			if($row["status"] == 'active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["qpdf_id"].'" data-status="'.$row["status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["qpdf_id"].'" data-status="'.$row["status"].'">Deactive</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["qpdf_id"].'"><i class="fas fa-edit"></i></button>
            <button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["qpdf_id"].'"><i class="fas fa-times"></i></button>
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


        $subject_name= $object->Get_Subject_name($_POST['subject_id']);
        $file_name = $_FILES['notes']['name'];
		$object->data = array(
			':subject_name' 	=>	$subject_name,
			':notes'	     =>	$file_name,
		
		);

		$object->query = "
		SELECT * FROM question_bank_soes 
		WHERE question_bank = :notes
		AND subject_name = :subject_name
		";

		$object->execute_query();

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">file  Already Exists</div>';
		}
        else{
      
		  if($_FILES["notes"]["name"] != '')
			{
				
               $file_name = $_FILES['notes']['name'];
			    $tmp_name = $_FILES['notes']['tmp_name'];
				$time = time();
				
				move_uploaded_file($tmp_name,"../questionbank/".$file_name);

                $subject_name= $object->Get_Subject_name($_POST['subject_id']);
				$object->data = array(
			     	':subject_name'			=>	$object->clean_input($subject_name),
			    	':notes'             =>   $file_name,
				    
				    ':status'			=>	'active',
				    ':added_on'		=>	$object->now
			);

			$object->query = "
			INSERT INTO question_bank_soes 
			(subject_name,question_bank,status,added_on) 
			VALUES (:subject_name,:notes, :status, :added_on)
			";

			$object->execute_query();

			$success = '<div class="alert alert-success">notes Added</div>';
		
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
		SELECT * FROM question_bank_soes 
		WHERE qpdf_id = '".$_POST['notes_id']."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['subject_id'] = $object->Get_Subject_id($row['subject_name']);
			$data['notes'] = $row['question_bank'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

        $subject_name= $object->Get_Subject_name($_POST['subject_id']);
        $file_name = $_FILES['notes']['name'];
		$object->data = array(
			':subject_name' 	=>	$subject_name,
			':notes'	     =>	$file_name,
		
		);

		$object->query = "
		SELECT * FROM question_bank_soes 
		WHERE question_bank = :notes
		AND subject_name = :subject_name
		";

		$object->execute_query();

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">file  Already Exists</div>';
		}
		else
		{

            if($_FILES["notes"]["name"] != '')
			{
				
               $file_name = $_FILES['notes']['name'];
			    $tmp_name = $_FILES['notes']['tmp_name'];
				$time = time();
				
				move_uploaded_file($tmp_name,"../notes/".$file_name);

                $subject_name= $object->Get_Subject_name($_POST['subject_id']);
				$object->data = array(
			     	':subject_name'			=>	$object->clean_input($subject_name),
			    	':notes'             =>   $file_name,
                
				    
				   
			);

			$object->query = "
			UPDATE question_bank_soes 
			SET subject_name=:subject_name , question_bank=:notes where qpdf_id = '".$_POST['hidden_id']."';
			";

			$object->execute_query();

			$success = '<div class="alert alert-success">notes updated</div>';
		
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
			':status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE question_bank_soes 
		SET status = :status 
		WHERE qpdf_id = '".$_POST["id"]."'
		";

		$object->execute_query();

		echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM question_bank_soes 
		WHERE qpdf_id = '".$_POST["id"]."'
		";

		$object->execute_que();

		echo '<div class="alert alert-success">notes Deleted</div>';
	}
}

?>