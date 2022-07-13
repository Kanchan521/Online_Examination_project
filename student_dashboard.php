<?php

//student_dashboard.php

include('admin/soes.php');

$object = new soes();

if(!$object->is_student_login())
{
	header("location:".$object->base_url."");
}

include('header.php');

?>
 

	<!-- Page Heading -->
</br>
</br>
</br>
	<h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

	<!-- Content Row -->
	<div class="row row-cols-5">
		<!-- Earnings (Monthly) Card Example -->
		<div class="col mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
								<a href="student_subjects.php">My subject</a></div>
							
						</div>
						<div class="col-auto">
							<i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	</div>


				
		    

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){

	

});

</script>