<?php

//index.php

include('admin/soes.php');

$object = new soes();

if($object->is_student_login())
{
	header("location:".$object->base_url."student_dashboard.php");
}

include('header.php');

?>


		      	<h3 class="text-center">Welcome</h3>
		      	<p class="text-center">If you are not login into system, click <a href="login.php">here</a></p>
		      	<div class="container">
			      	<div class="card mt-4 mb-4">
			      		<div class="card-header">Latest News</div>
			      		<div class="card-body">
			      		<?php
			      		$object->query = "
			      		SELECT * FROM exam_soes 
			      		WHERE exam_result_datetime != '0000-00-00 00:00:00' 
			      		ORDER BY exam_result_datetime ASC
			      		";

			      		$object->execute_que();

			      		if($object->row_count() > 0)
			      		{
			      			$result = $object->statement_result();
			      			foreach($result as $row)
			      			{
			      				if(time() < strtotime($row["exam_result_datetime"]))
			      				{
			      					echo '<p><b>'.$row["exam_title"].' </b>exam of <b>'.$object->Get_class_name($row["exam_class_id"]).'</b>results  will publish on '.$row["exam_result_datetime"].'</p>';
			      				}
			      				
			      			}
			      		}
			      		?>
						  	<?php
			      		$object->query = "
			      		SELECT * FROM exam_soes 
			      		WHERE exam_status='created'
			      		ORDER BY exam_id ASC
			      		";

			      		$object->execute_que();

			      		if($object->row_count() > 0)
			      		{
			      			$result = $object->statement_result();
			      			foreach($result as $row)
			      			{
			      				
			      					echo '<p><b>'.$row["exam_title"].' </b>exam of <b>'.$object->Get_class_name($row["exam_class_id"]).' - exam timetable has been published..Do check</p>';
			      				
			      			}
			      		}
			      		else
			      		{
			      			echo '<p>No News Found</p>';
			      		}



			      		?>

						  
			      		</div>
			      	</div>
			      </div>
		    

<?php

include('footer.php');

?>