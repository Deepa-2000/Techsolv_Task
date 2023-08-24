<?php

include('db.php');

//   echo getenv("REMOTE_ADDR"); exit;  

$nameErr = $phoneErr = $emailErr = $subjectErr = $messageErr = NULL;  
$name = $phone = $email = $subject = $message = NULL;  
$flag=true;

function test_input($data){
  $data=trim($data);
  $data=stripslashes($data);
  $data=htmlspecialchars($data);
  return $data;
}


//form validation  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  

    if (empty($_POST['name'])) {
        $flag=false;
        $nameErr="Full Name is required.";
    }else {
        if (!preg_match("/^[a-zA-Z ]*$/",$_POST['name'])) {
            $nameErr = "Only alphabets and white space are allowed!";
            $flag=false;
        }
        $name = test_input($_POST["name"]);

    }

    if (empty($_POST['phone'])) {

        $flag=false;
        $phoneErr="Phone Number is required.";
    }else {
        if (!preg_match ("/^[0-9]*$/", $_POST['phone'])) {
            
            $flag=false;
            $phoneErr="Only numeric value is allowed."; 

        }elseif (strlen ($_POST['phone']) != 10) {
            
            $flag=false;
            $phoneErr="Phone number must contain 10 digits.";

        }
        $phone=test_input($_POST['phone']);
    }

    if (empty($_POST['email'])) {
        
        $flag=false;
        $emailErr="Email is required.";
    }else {
        if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/",$_POST['email'])) {

            $flag=false;
            $emailErr="Please input a valid email address!";
        }
        $email=test_input($_POST['email']);
    }

    if (empty($_POST["subject"])) {

        $subjectErr = "Subject is required"; 
        $flag=false;
    
    }else {  
        if (!preg_match("/^[a-zA-Z ]*$/",$_POST['subject'])) { 

            $flag=false; 
            $subjectErr = "Only alphabets and white space are allowed"; 
        }
        $subject = test_input($_POST["subject"]);    
    }

    if (empty($_POST['message'])) {
        
        $messageErr = "Message is required."; 
        $flag=false;
    }else {
        if(!preg_match("/^(.|\s)*[a-zA-Z]+(.|\s)*$/",$_POST['message'])){

            $flag=false;
            $messageErr = "Only alphabets, special characters, spaces, new line and presumably numbers is allowed."; 

        }
        $message=test_input($_POST['message']);
    }

    //submit form if validated successfully
    $duplicate=mysqli_query($conn,"select * from `users` where name='$name' or email='$email'");
    if (mysqli_num_rows($duplicate)>0 && $flag)
    {
        header("Location: index.php?Message=User's Full Name or Email Already Exists.");
        exit;
    }
    else{
        $ip_address=getenv("REMOTE_ADDR");
        $sql="INSERT INTO `users` (`name`,`phone`,`email`,`subject`,`message`,`ip_address`) VALUES ('$name','$phone','$email','$subject','$message','$ip_address')";

        // print_r($sql);
        // exit;
        if ($conn->query($sql)=== TRUE) {
          $Message="Request Sent Successfully";
          header("Location:index.php?Message={$Message}");
          exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Crud_Operation</title>
    <style>  .error {color: #FF0001;}  </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Contact Form </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>
    <?php
    if(isset($_GET['Message'])){
    ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><?php echo $_GET['Message']; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
    }
    ?>
    <div class="container mt-3">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        
            <div class="col-md-6 mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="<?= $name; ?>" onfocus="this.value=''">
              <span class="error"><?php echo $nameErr;  ?> </span>  
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="<?= $phone; ?>" onfocus="this.value=''" maxlength="10">
              <span class="error"><?php echo $phoneErr;  ?> </span>  

            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="text" name="email" class="form-control" value="<?= $email; ?>" onfocus="this.value=''">
              <span class="error"><?php  echo $emailErr;?> </span>  


            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Subject</label>
              <input type="text" name="subject" class="form-control" value="<?= $subject; ?>" onfocus="this.value=''">
              <span class="error"><?php echo $subjectErr;  ?> </span>  

            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Message</label>
              <textarea class="form-control" name="message" cols="55" rows="4" value="<?= $message; ?>" onfocus="this.value=''"></textarea> 
              <span class="error"><?php echo $messageErr; ?> </span>  
            </div>
            <input type="submit" class="btn btn-primary" name="submit" value="Submit">
            <input class="btn btn-secondary" aria-label="Close alert" type="reset" value="Cancel">
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
