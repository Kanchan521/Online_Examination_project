<?php
include('soes.php');

$object = new soes();

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if(!$object->is_master_user())
{
    header("location:".$object->base_url."admin/result.php");
}

$object->query = "
SELECT * FROM subject_soes 
WHERE subject_status = 'Enable' 
ORDER BY subject_name ASC
";

$subject_data = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">class Notes</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary"></h6>
                            	</div>
                            	<div class="col" align="right">
                                    <button type="button" name="add_subject" id="add_subject" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="upload_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Subject Name</th>
											<th>notes</th>
                                            <th>added On</th>
                                            <th>Status</th>
                                            <th>Action</th>
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

<div id="uploadModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="upload_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add notes</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
		          		<label>Subject Name</label>
		          		<select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            <?php
                            foreach($subject_data as $row)
                            {
                                echo '<option value="'.$row["subject_id"].'">'.$row["subject_name"].'</option>';
                            }
                            ?>
                        </select>
		          	</div>
                      <div class="form-group">
		          		<label>Notes</label>
		          		<input type="file" name="notes" id="notes" class="form-control"  />
                         </br>
						  <span id="notes_uploaded"></span>
		          	</div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#upload_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"upload_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[3],
				"orderable":false,
			},
		],
	});

    //
    $('#add_subject').click(function(){
        
        $('#upload_form')[0].reset();

        $('#upload_form').parsley().reset();

        $('#modal_title').text('Add notes');

        $('#action').val('Add');

        $('#submit_button').val('Add');

        $('#uploadModal').modal('show');

        $('#form_message').html('');

        $('#notes').attr('required', 'required');

        $('#notes_uploaded').html('');

    });

    $('#notes').change(function(){
        var extension = $('#notes').val().split('.').pop().toLowerCase();
        if(extension != '')
        {
            if(jQuery.inArray(extension, ['docx','doc','pdf','txt','pptx']) == -1)
            {
                alert("Invalid File");
                $('#notes').val('');
                return false;
            }
        }
    });


	$('#upload_form').parsley();

	$('#upload_form').on('submit', function(event){
		event.preventDefault();
		if($('#upload_form').parsley().isValid())
		{		
			$.ajax({
				url:"upload_action.php",
				method:"POST",
				async:'false',
				data:new FormData(this),
				dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#uploadModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){

		var id = $(this).data('id');

		$('#upload_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"upload_action.php",

	      	method:"POST",

	      	data:{notes_id:id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                //$('#class_id').val(data.class_id);

	        	$('#subject_id').val(data.subject_id);
	        	
	        	$('#notes_uploaded').html(data.notes);

	        	$('#modal_title').text('Edit notes');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#uploadModal').modal('show');

	        	$('#hidden_id').val(id);

	      	}

	    })

	});

	$(document).on('click', '.status_button', function(){
		var id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'active';
		if(status == 'active')
		{
			next_status = 'deactive';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{

      		$.ajax({

        		url:"upload_action.php",

        		method:"POST",

        		data:{id:id, action:'change_status', status:status, next_status:next_status},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}
	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"upload_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}

  	});

});
</script>