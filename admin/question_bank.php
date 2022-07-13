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
$subject='';
$subject_data='';
if(isset($_GET["subject"]))
{
	$subject=$_GET["subject"];
	$object->query = "
SELECT * FROM question_bank_soes
WHERE status = 'active' AND subject_name='".$subject."'
ORDER BY qpdf_id ASC
";

$subject_data = $object->get_result();
}

include('header.php');

?>

                    <!-- Page Heading -->
					  <h1 class="h3 mb-4 text-gray-800">Question Bank</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">

                            		<h6 class="m-0 font-weight-bold text-primary"><?php echo $subject?></h6>
                            	</div>
                            	
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
							<?php
							$count=1;
                            foreach($subject_data as $row)
                            { ?>
							    
								<div class="text-danger"><?php echo $count.".  ";?><a href="../questionbank/<?php echo $row['question_bank'];?>" target="_blank" class="text-success"><?php echo $row['question_bank'];?></a></div>
								<?php
								$count++;
                            }
                            ?>
                            </div>
                        </div>
                    </div>




<?php
                include('footer.php');
                ?>