<?php
include('soes.php');

$object = new soes();

$object->query = "
SELECT * FROM exam_soes 
WHERE exam_status = 'Pending' OR exam_status = 'Created' 
ORDER BY exam_title ASC
";

$result = $object->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjective</title>
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="../vendor/parsley/parsley.css"/>

<link rel="stylesheet" type="text/css" href="../vendor/bootstrap-select/bootstrap-select.min.css"/>

<link rel="stylesheet" type="text/css" href="../vendor/datepicker/bootstrap-datepicker.css"/>

<link rel="stylesheet" type="text/css" href="../vendor/datetimepicker/bootstrap-datetimepicker.css"/>

    <script src="ckeditor/ckeditor.js"></script>
    <script src="ckfinder/ckfinder.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="text-center" style="margin-top:5px; padding-top:0;">Editor</h2>
        <hr>
        <?php
    if(isset($_REQUEST['save'])){
       
        
         $object->data=array(
             ':exam_id'=>$_REQUEST['exam_id'],
             ':sub_id'=>$_REQUEST['exam_subject_id'],
             ':descp'=>$_REQUEST['editor1'],
             ':exam_subject_question_answer'	=>	$_REQUEST["exam_subject_question_answer"]
            
         );
      
     $object->query="INSERT INTO `exam_subject_question_soes`( `exam_id`, `exam_subject_id`, `exam_subject_question_title`,`exam_subject_question_answer`) VALUES(:exam_id,:sub_id,:descp,:exam_subject_question_answer)";
     $object->execute_query();
     $exam_subject_question_id = $object->connect->lastInsertId();

		for($count = 1; $count <= 4; $count++)
		{
			$object->data = array(
				':exam_subject_question_id'		=>	$exam_subject_question_id,
				':question_option_number'		=>	$count,
				':question_option_title'		=>		$object->clean_input($_REQUEST['option' . $count])
			);

			$object->query = "
			INSERT INTO question_option_soes 
			(exam_subject_question_id, question_option_number, question_option_title) 
			VALUES (:exam_subject_question_id, :question_option_number, :question_option_title)
			";
				
			$object->execute_query();
		}
      
        if($object->row_count()>0){
            header('location:exam_subject_question.php');

        }else{
            echo '<div class="alert alert-danger" role="alert">
            <div id="result">failed .</div>
            </div>';
        }
    }
    
    ?>
        <form method="post" id="exam_form">
        <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Exam Name</label>
                        <div class="col-sm-9">
                            <select name="exam_id" id="exam_id" class="form-control" required>
                                <option value="">Select Exam</option>
                                <?php
                                foreach($result as $row)
                                {
                                    echo '
                                    <option value="'.$row["exam_id"].'">'.$row["exam_title"].'</option>
                                    ';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Subject</label>
                        <div class="col-sm-9">
                            <select name="exam_subject_id" id="exam_subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
               <textarea name="editor1" class="form-control"></textarea>
            </div>
                    <div class="form-group">
               <textarea name="option1" class="form-control"></textarea>
            </div>
                    <div class="form-group">
               <textarea name="option2" class="form-control"></textarea>
            </div>
                    <div class="form-group">
               <textarea name="option3" class="form-control"></textarea>
            </div>
                    <div class="form-group">
               <textarea name="option4" class="form-control"></textarea>
            </div>
            <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Answer</label>
                        <div class="col-sm-9">
                            <select name="exam_subject_question_answer" id="exam_subject_question_answer" class="form-control" required>
                                <option value="">Select</option>
                                <option value="1">1 Option</option>
                                <option value="2">2 Option</option>
                                <option value="3">3 Option</option>
                                <option value="4">4 Option</option>
                            </select>
                        </div>
                    </div>
        		</div>
            <div class="form-group">
               <input type="submit" class="btn btn-success btn-block" id="save" value="create article" name="save">
            </div>
        
        </div>
    </form>
        <!-- <div class="editor2">
            This is inline example
        </div> -->
    </div>
 <div style="position:fixed;bottom:10px;right:10px;color:green;">
<strong>Examination</strong>
</div>   
<script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript" src="../vendor/parsley/dist/parsley.min.js"></script>

    <script type="text/javascript" src="../vendor/bootstrap-select/bootstrap-select.min.js"></script>

    <script type="text/javascript" src="../vendor/datepicker/bootstrap-datepicker.js"></script>

    <script type="text/javascript" src="../vendor/datetimepicker/bootstrap-datetimepicker.js"></script>

</body>
<script>
$(document).ready(function(){
$('#exam_id').change(function(){
        var exam_id = $('#exam_id').val();
        if(exam_id != '')
        {
            $.ajax({
                url:"exam_subject_question_action.php",
                method:"POST",
                data:{action:'fetch_subject', exam_id:exam_id},
                success:function(data)
                {
                    $('#exam_subject_id').html(data);
                }
            });
        }
    });

});


CKEDITOR.replace( 'editor1', {
    filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', 
    removePlugins: 'easyimage,simage,image2,about,forms,mathjax,base64image'
} );
CKEDITOR.replace( 'option1', {
    filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', 
    removePlugins: 'easyimage,simage,image2,about,forms,mathjax,base64image'
  
} );
CKEDITOR.replace( 'option2', {
    filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', 
    removePlugins: 'easyimage,simage,image2,about,forms,mathjax,base64image'
   
} );
CKEDITOR.replace( 'option3', {
    filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', 
    removePlugins: 'easyimage,simage,image2,about,forms,mathjax,base64image'
   
} );
CKEDITOR.replace( 'option4', {
    filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
	filebrowserUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', 
    removePlugins: 'easyimage,simage,image2,about,forms,mathjax,base64image'
   
} );
    // CKEDITOR.replace('editor1');
    // CKEDITOR.replace('option1');
    // CKEDITOR.replace('option2');
    // CKEDITOR.replace('option3');
    // CKEDITOR.replace('option4');
    
     CKEDITOR.on('dialogDefinition',function(e){
        dialogName=e.data.name;
         dialogDef=e.data.definition;
         if(dialogName == 'image'){
        //    dialogDef.removeContents('Link');
           dialogDef.removeContents('advanced');
           var tabContent=dialogDef.getContents('info');
           tabContent.remove('txtHSpace');
           tabContent.remove('txtVSpace');
        
         }
     });

</script>
</html>