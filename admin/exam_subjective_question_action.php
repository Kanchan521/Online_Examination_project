<?php

//exam_subject_question_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('exam_soes.exam_title', 'subject_soes.subject_name', 'exam_subjective_question_soes.exam_subject_question_title','exam_subjective_question_soes.marks_per_right_answer');

		$output = array();

		$main_query = "
		SELECT * FROM exam_subjective_question_soes 
		INNER JOIN subjective_subject
		ON subjective_subject.exam_sub_id = exam_subjective_question_soes.exam_subject_id 
		INNER JOIN exam_soes 
		ON exam_soes.exam_id = subjective_subject.exam_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subjective_subject.subject_id 
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE exam_soes.exam_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_soes.subject_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_subjective_question_soes.exam_subject_question_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_subjective_question_soes.marks_per_right_answer LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY exam_subjective_question_soes.exam_sub_question_id DESC ';
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
			$sub_array[] = html_entity_decode($row["exam_title"]);
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$sub_array[] = $row["exam_subject_question_title"];
            $sub_array[] = $row["marks_per_right_answer"] . ' Mark';
		
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["exam_sub_question_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["exam_sub_question_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST['action'] == 'fetch_subject')
	{
		$object->query = "
		SELECT subjective_subject.exam_sub_id, subject_soes.subject_name 
		FROM subjective_subject 
		INNER JOIN exam_soes 
		ON exam_soes.exam_id = subjective_subject.exam_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subjective_subject.subject_id 
		WHERE exam_soes.exam_id = '".$_POST["exam_id"]."' 
		ORDER BY subject_soes.subject_id ASC";

		$result = $object->get_result();
		$html = '<option value="">Select Subject</option>';
		foreach($result as $row)
		{
			if($object->Can_add_question_in_this_sub($row['exam_sub_id']))
			{
				$html .= '<option value="'.$row['exam_sub_id'].'">'.$row['subject_name'].'</option>';
			}
		}
		echo $html;
	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';
		
		$object->data = array(
			':exam_id'						=>	$_POST["exam_id"],
			':exam_subject_id'				=>	$_POST["exam_subject_id"],
			':exam_subject_question_title'	=>	$_POST["exam_subject_question_title"],
            ':marks_per_right_answer'	=>	  $_POST["marks_per_right_answer"],
		);

		$object->query = "
		INSERT INTO exam_subjective_question_soes 
		(exam_id, exam_subject_id, exam_subject_question_title, marks_per_right_answer) 
		VALUES (:exam_id, :exam_subject_id, :exam_subject_question_title, :marks_per_right_answer)
		";

		$object->execute_query();

		

		$success = '<div class="alert alert-success">Question Added</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM exam_subjective_question_soes 
		WHERE exam_sub_question_id = '".$_POST["exam_subject_question_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		$exam_subject_id = '';

		foreach($result as $row)
		{
			$data['exam_id'] = $row['exam_id'];
			$data['exam_subject_id'] = $row['exam_subject_id'];
			$data['exam_subject_question_title'] = $row['exam_subject_question_title'];
            $data['marks_per_right_answer'] = $row['marks_per_right_answer'];
			$exam_subject_id = $row['exam_subject_id'];
		}

		

		$object->query = "
		SELECT subject_soes.subject_name FROM subjective_subject 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subjective_subject.subject_id 
		WHERE subjective_subject.exam_sub_id = '".$exam_subject_id."'
		";

		$result = $object->get_result();

		foreach($result as $row)
		{
			$data['subject_name'] = $row["subject_name"];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$object->data = array(
			':exam_subject_question_title'	=>	$_POST["exam_subject_question_title"],
			':marks_per_right_answer'	=>	$_POST["marks_per_right_answer"],
		);

		$object->query = "
		UPDATE exam_subjective_question_soes 
		SET exam_subject_question_title = :exam_subject_question_title, 
		marks_per_right_answer = :marks_per_right_answer    
		WHERE exam_sub_question_id = '".$_POST['hidden_id']."'
		";

		$object->execute_query();

		
		$success = '<div class="alert alert-success">Exam Subject Question Data Updated</div>';
		
		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM exam_subjective_question_soes 
		WHERE exam_sub_question_id = '".$_POST["id"]."'
		";

		$object->execute_que();

		

		echo '<div class="alert alert-success">Exam Subject Question Data Deleted</div>';
	}
}

?>