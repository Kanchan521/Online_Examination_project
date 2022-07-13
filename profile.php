<?php

include('admin/soes.php');

$object = new soes();


$object->query = "
    SELECT * FROM student_soes
    WHERE student_id = '".$_SESSION["student_id"]."'
    ";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-6"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="profile" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>Change Password</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--<div class="row">
                                    <div class="col-md-6">!-->
                                    <div class="form-group">
                                            
                                            <span id="uploaded_image"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="user_name" id="user_name" class="form-control" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <input type="text" name="gender" id="gender" class="form-control" readonly   />
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" name="user_address" id="user_address" class="form-control" readonly  />
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input type="text" name="user_email" id="user_email" class="form-control" readonly/>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input type="text" name="dob" id="dob" class="form-control" required readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="user_password" id="user_password" class="form-control" required data-parsley-maxlength="16" data-parsley-trigger="keyup" />
                                        </div>
                                      
                                    <!--</div>
                                </div>!-->
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    <?php
    foreach($result as $row)
    {
    ?>
    $('#user_name').val("<?php echo $row['student_name']; ?>");
    $('#gender').val("<?php echo $row['student_gender']; ?>");
    $('#user_address').val("<?php echo $row['student_address']; ?>");
    $('#user_email').val("<?php echo $row['student_email_id']; ?>");
    $('#dob').val("<?php echo $row['student_dob']; ?>");
    $('#user_password').val("<?php echo $row['student_password']; ?>");
    <?php
        if($row["student_image"] != '')
        {
           
    ?>
    
    $('#uploaded_image').html('<img src="<?php echo  str_replace("../","",$row["student_image"]); ?>" class="img-thumbnail" width="100" />');
    <?php
        }
      
    }
       
    ?>
   
  

 

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"user_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                   
                    $('#user_password').val(data.user_password);
                   
                    if(data.user_profile != '')
						
                    $('#message').html(data.success);

					setTimeout(function(){

				        $('#message').html('');

				    }, 5000);
				}
			})
		}
	});

});
</script>