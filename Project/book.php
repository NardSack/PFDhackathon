<?php
if(isset($_GET['date']))
{
    $date = $_GET['date'];
}

if(isset($_POST['submit']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $reason = $_POST['reason'];
    $notes = $_POST['notes'];
    $mysqli = new mysqli('localhost','root','','apptcalendar');
    $stmt = $mysqli->prepare("insert into booking (name,email,date,reason,notes) values (?,?,?,?,?)");
    $stmt->bind_param("sssss", $name, $email, $date, $reason, $notes);
    $stmt->execute();
    $msg="<div class='alert alert-success'>Booking Successful</div>";
    // $stmt->close();
    // $mysqli->close();
}

// Include the database configuration file 

 
$statusMsg = ''; 
 
// File upload directory 
$targetDir = "uploads/"; 
 
if(isset($_POST["submit"])){ 
    if(!empty($_FILES["file"]["name"])){ 
        $fileName = basename($_FILES["file"]["name"]); 
        $targetFilePath = $targetDir . $fileName; 
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
     
        // Allow certain file formats 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            // Upload file to server 
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
                // Insert image file name into database 
                $insert = $mysqli->prepare("INSERT INTO images (email, picture) VALUES (?,?)"); 
                $insert->bind_param("ss", $email, $fileName);
                $insert->execute();

                if($insert){ 
                    $statusMsg = "The file ".$fileName. " has been uploaded successfully."; 
                }else{ 
                    $statusMsg = "File upload failed, please try again."; 
                }  
            }else{ 
                $statusMsg = "Sorry, there was an error uploading your file."; 
            } 
        }else{ 
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
        } 
    }else{ 
        $statusMsg = 'Please select a file to upload.'; 
    } 
} 
 
// Display status message 
echo $statusMsg; 
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Bootstrap Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="style.css"></script>

</head>
<body>
    <div class="container">
        <h1 class="text-center">Book for Date:<?php echo date('d F Y',strtotime($date)) ?></h1><hr>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php echo isset($msg)?$msg:'';?>
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                <!-- <form action="" method="post" autocomplete="off" > -->

                

                    <div class="formgroup">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="formgroup">
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="formgroup">
                        <label for="reason">Reason for Consult</label>
                        <select id="reason" name="reason">
                            <option value="account">Create a Bank Account</option>
                            <option value="card">Create a Credit / Debit Card</option>
                            <option value="others">Others</option>
                    </select>

                    </div>
                    <div class="formgroup">
                        <label for="reason">Additional Notes<span style="font-size:10px">(Enter reason if others is selected)</span></label>
                        <input type="text" class="form-control" placeholder="Type Here..." name="notes">

                    </div>
                    <div class="formgroup">

                    <!-- Select image to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload"> w3sch -->
                    
                    Select Image File to Upload:
                    <input type="file" name="file">
                    <!-- <input type="submit" name="submit" value="Upload"> -->
                    
                    </div>
                    <button class="btn btn-primary" type="submit" name="submit" value="Upload">Submit</button>
                    <!-- <button class="btn btn-primary" type="submit" name="submit" >Submit</button> -->

                </form>
            </div>
        </div>
    </div>
</body>
</html>
