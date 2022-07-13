<?php

//soes.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class soes
{
	var $base_url = 'http://localhost/Student_Management/';
	var $connect;
	var $query;
	var $statement;
	var $data;
	var $now;
	var $host;
	var $username;
	var $database;
	var $password;


function __construct()
	{
		$this->host = 'localhost';
		$this->username = 'root';
		$this->password = '';
		$this->database = 'soes';
        $this->connect = new PDO("mysql:host=$this->host; dbname=$this->database", "$this->username", "$this->password");
        date_default_timezone_set('Asia/Kolkata');
		$this->now = date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
		session_start();
	}
	function execute_query()
	{
		$this->statement = $this->connect->prepare($this->query);
		
			$this->statement->execute($this->data);
				
	}
	function execute_que()
	{
		$this->statement = $this->connect->prepare($this->query);
		
			$this->statement->execute();
				
	}

	function row_count()
	{
		return $this->statement->rowCount();
	}

	function statement_result()
	{
		return $this->statement->fetchAll();
	}

	function get_result()
	{
		return $this->connect->query($this->query, PDO::FETCH_ASSOC);
	}

	

	function is_login()
	{
		if(isset($_SESSION['user_id']))
		{
			return true;
		}
		return false;
	}

	function is_master_user()
	{
		if(isset($_SESSION['user_type']))
		{
			if($_SESSION["user_type"] == 'Master')
			{
				return true;
			}
			return false;
		}
		return false;
	}

	function is_student_login()
	{
		if(isset($_SESSION['student_id']))
		{
			return true;
		}
		return false;
	}

	function clean_input($string)
	{
	  	$string = trim($string);
	  //	$string = stripslashes($string);
	
		 // $string = strtoupper($string);
	  	return $string;
	}
	function clean($string)
	{
	  	$string = trim($string);
	  	$string = stripslashes($string);
	  	$string = htmlspecialchars($string);
		  $string = strtoupper($string);
	  	return $string;
	}

	function Get_class_name($class_id)
	{
		$this->query = "
		SELECT class_name FROM class_soes 
		WHERE class_id = '$class_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["class_name"];
		}
	}

	function Check_subject_already_added_in_exam($exam_id, $subject_id)
	{
		$this->query = "
		SELECT exam_subject_id FROM subject_wise_exam_detail 
		WHERE exam_id = '$exam_id' 
		AND subject_id = '$subject_id'
		";

		$this->execute_que();

		if($this->row_count() > 0)
		{
			return true;
		}
		return false;
	}

	function Get_exam_name($exam_id)
	{
		$this->query = "
		SELECT exam_title FROM exam_soes 
		WHERE exam_id = '$exam_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["exam_title"];
		}
	}

	function Get_exam_duration($exam_id)
	{
		$this->query = "
		SELECT exam_duration FROM exam_soes 
		WHERE exam_id = '$exam_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["exam_duration"];
		}
	}

	function Get_question_option_data($exam_subject_question_id, $option_number)
	{
		$this->query = "
		SELECT question_option_title FROM question_option_soes 
		WHERE exam_subject_question_id = '$exam_subject_question_id' 
		AND question_option_number = '$option_number'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row['question_option_title'];
		}
	}

	function Can_add_question_in_this_subject($exam_subject_id)
	{
		$this->query = "
		SELECT subject_total_question FROM subject_wise_exam_detail 
		WHERE exam_subject_id = '$exam_subject_id'
		";

		$allow_question = 0;

		$result = $this->get_result();
		foreach($result as $row)
		{
			$allow_question = $row["subject_total_question"];
		}

		$this->query = "
		SELECT * FROM exam_subject_question_soes 
		WHERE exam_subject_id = '$exam_subject_id'
		";

		$this->execute_que();

		$total_question = $this->row_count();

		if($total_question >= $allow_question)
		{
			return false;
		}

		return true;
	}
	function Can_add_question_in_this_sub($exam_subject_id)
	{
		$this->query = "
		SELECT subject_total_question FROM subjective_subject 
		WHERE exam_sub_id = '$exam_subject_id'
		";

		$allow_question = 0;

		$result = $this->get_result();
		foreach($result as $row)
		{
			$allow_question = $row["subject_total_question"];
		}

		$this->query = "
		SELECT * FROM exam_subjective_question_soes 
		WHERE exam_subject_id = '$exam_subject_id'
		";

		$this->execute_que();

		$total_question = $this->row_count();

		if($total_question >= $allow_question)
		{
			return false;
		}

		return true;
	}


	function Get_Class_subject($class_id)
	{
		$this->query = "
		SELECT subject_name FROM subject_soes 
		WHERE class_id = '$class_id' 
		AND subject_status = 'Enable'
		";
		$result = $this->get_result();
		$data = array();
		foreach($result as $row)
		{
			$data[] = $row["subject_name"];
		}
		return $data;
	}

	function Get_user_name($user_id)
	{
		$this->query = "
		SELECT * FROM user_soes 
		WHERE user_id = '".$user_id."'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			if($row['user_type'] != 'Master')
			{
				return $row["user_name"];
			}
			else
			{
				return 'Master';
			}
		}
	}

	function Get_Subject_name($subject_id)
	{
		$this->query = "
		SELECT subject_name FROM subject_soes 
		WHERE subject_id = '$subject_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["subject_name"];
		}
	}
	function Get_Subject_id($subject_name)
	{
		$this->query = "
		SELECT subject_id FROM subject_soes 
		WHERE subject_name = '$subject_name'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["subject_id"];
		}
	}

	function Get_student_question_answer_option($exam_subject_question_id, $student_id)
	{
		$this->query = "
		SELECT student_answer_option FROM exam_subject_question_answer 
		WHERE exam_subject_question_id = '".$exam_subject_question_id."' 
		AND student_id = '".$student_id."'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["student_answer_option"];
		}
	}

	function Get_question_answer_option($question_id)
	{
		$this->query = "
		SELECT exam_subject_question_answer FROM exam_subject_question_soes 
		WHERE exam_subject_question_id = '".$question_id."' 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["exam_subject_question_answer"];
		}
	}

	function Get_question_right_answer_mark($exam_subject_id)
	{
		$this->query = "
		SELECT marks_per_right_answer FROM subject_wise_exam_detail 
		WHERE exam_subject_id = '".$exam_subject_id."' 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["marks_per_right_answer"];
		}
	}
	function Get_question_wrong_answer_mark($exam_subject_id)
	{
		$this->query = "
		SELECT marks_per_wrong_answer FROM subject_wise_exam_detail 
		WHERE exam_subject_id = '".$exam_subject_id."' 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["marks_per_wrong_answer"];
		}
	}

	function Get_exam_id($exam_code)
	{
		$this->query = "
		SELECT exam_id FROM exam_soes 
		WHERE exam_code = '$exam_code'
		";

		$result = $this->get_result();

		foreach($result as $row)
		{
			return $row['exam_id'];
		}
	}

	function Get_exam_subject_id($exam_subject_code)
	{
		$this->query = "
		SELECT exam_subject_id FROM subject_wise_exam_detail 
		WHERE subject_exam_code = '$exam_subject_code'
		";

		$result = $this->get_result();

		foreach($result as $row)
		{
			return $row['exam_subject_id'];
		}
	}

	function send_email($receiver_email, $subject, $body)
	{
		$mail = new PHPMailer();
		
         $mail->isSMTP();

		 $mail->Mailer = "smtp";
		 $mail->Host="smtp.gmail.com";
		 $mail->SMTPDebug  = 1;  
        
        
		$mail->SMTPSecure = "tls";

		$mail->Port = '587';

		$mail->SMTPAuth = true;

		$mail->Username = 'xxxxxxxxxx';

		$mail->Password = 'xxxxxxxxxxxxxxxx';

		$mail->FromName = 'online_education';

		$mail->From = 'xxxxxxxxxxxxxx';

		$mail->setFrom('xxxxxxxx','xxxxxxxxxxxxxxxx');

		$mail->addAddress($receiver_email);

		$mail->isHTML(true);

		$mail->Subject = $subject;

		$mail->Body = $body;
	
		
		$mail->Send();
		
		
	
}


	
	function Get_total_classes()
	{
		$this->query = "
		SELECT COUNT(class_id) as Total 
		FROM class_soes 
		WHERE class_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_subject()
	{
		$this->query = "
		SELECT COUNT(subject_id) as Total 
		FROM subject_soes 
		WHERE subject_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_student()
	{
		$this->query = "
		SELECT COUNT(student_id) as Total 
		FROM student_soes 
		WHERE student_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_exam()
	{
		$this->query = "
		SELECT COUNT(exam_id) as Total 
		FROM exam_soes 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_result()
	{
		$this->query = "
		SELECT COUNT(exam_id) as Total 
		FROM exam_soes 
		WHERE exam_result_datetime != '0000-00-00 00:00:00' 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}


}


?>