<?php

//subject_action.php

include('admin/soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('subject_name');

		$output = array();

		$main_query = "
		
        SELECT distinct(subject_soes.subject_name),subject_soes.subject_id FROM `subject_soes` Inner JOIN subject_to_class_soes on subject_soes.subject_id=subject_to_class_soes.subject_id Inner Join student_to_class_soes on student_to_class_soes.class_id=subject_to_class_soes.class_id ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= " where subject_soes.subject_name LIKE '%".$_POST["search"]["value"]."%'";
		
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY subject_soes.subject_id DESC ';
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
			//$sub_array[] = html_entity_decode($row["class_name"]);
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$notes='<a href="notes.php?subject='.$row["subject_name"].'"target="_blank">Notes</a>';
			$sub_array[]=$notes;
			$question_bank='<a href="question.php?subject='.$row["subject_name"].'"target="_blank">Question_bank</a>';
			$sub_array[]=$question_bank;
			
			
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
}
?>