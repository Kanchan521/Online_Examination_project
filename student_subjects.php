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
<br>
<br>
     <!-- Page Heading -->
     <h1 class="h3 mb-4 text-gray-800">Subject Management</h1>

<!-- DataTales Example -->
<span id="message"></span>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">Subject List</h6>
            </div>
           
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="subject_table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Notes</th>
                        <th>question_bank</th>
                        
                        
                        
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
<script>
$(document).ready(function(){

	var dataTable = $('#subject_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"subject_action_fetch.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				
				"orderable":false,
			},
		],
	});
});
</script>