<?php
session_start();
$mysqli = new mysqli('localhost','root','','apptcalendar');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}


$prebranch = $_SESSION['branch'];
if (isset($_GET['date']))
{
    $date = $_GET['date'];
    $_SESSION['date'] = $date;
}
$stmt = $mysqli->prepare("select * from booking where date = ? and branch = ?");
$stmt->bind_param('ss',$date,$prebranch);
$bookings = array();
if($stmt->execute()){
    $result = $stmt->get_result();
    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            $bookings[] = $row['timeslots'];
        }
        $stmt->close();
    }

}

if(isset($_POST['submit']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslots= $_POST['timeslots'];
    $reason = $_POST['reason'];
    $notes = $_POST['notes'];
    $branch = $_POST['branch'];
      // Check connection

      $stmt = $mysqli->prepare("select * from booking where date = ? and timeslots = ? and branch = ?");
      $stmt->bind_param('sss',$date, $timeslots, $prebranch);
    //   $bookings = array();
      if($stmt->execute()){
          $result = $stmt->get_result();
          if($result->num_rows>0){
            //   while($row = $result->fetch_assoc()){
            //       $bookings[] = $row['timeslots'];
            //   }
            //   $stmt->close();
            $msg="<div class='alert alert-danger'>already booked</div>";
          }else{
              
              $stmt = $mysqli->prepare("insert into booking (name,email,date,timeslots,reason,notes,branch) values (?,?,?,?,?,?,?)");
              if (!$stmt) {
                  echo "Error preparing query: " . $mysqli->error;
                  exit;
                }
          
              $stmt->bind_param("sssssss", $name, $email, $date,$timeslots, $reason, $notes, $branch);
              $stmt->execute();
              $msg="<div class='alert alert-success'>Booking Successful</div>";
              $bookings[]=$timeslots;
              if ($stmt->affected_rows > 0) {
                  $msg = "<div class='alert alert-success'>Booking Successful</div>";
                } else {
                  $msg = "<div class='alert alert-danger'>Booking Failed</div>";
                }
              $stmt->close();
          }
          
        }
        
    // $mysqli->close();
}

$targetDir = "uploads/"; 
$statusMsg = '';
if(isset($_POST["submit"])){ 
    if (!empty($_FILES["file"]["name"])) {
        foreach ($_FILES["file"]["name"] as $index => $fileName) {
            $targetFilePath = $targetDir . basename($fileName);
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $allowTypes = array('jpg','png','jpeg','gif','pdf'); 
    
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"][$index], $targetFilePath)) {
                    // Insert image file name into database
                    $insert = $mysqli->prepare("INSERT INTO images (email, picture,branch) VALUES (?,?,?)");
                    $insert->bind_param("sss", $email, $fileName, $branch);
                    $insert->execute();
                    $insert->close();
                    
                    if ($insert) {
                        $statusMsg .= "The file " . $fileName . " has been uploaded successfully.\n";
                    } else {
                        $statusMsg .= "File upload failed for " . $fileName . ", please try again.\n";
                    }
                } else {
                    $statusMsg .= "Sorry, there was an error uploading " . $fileName . ".\n";
                }
            } else {
                $statusMsg .= "Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.\n";
            }
        }
    }

    // if(!empty($_FILES["file"]["name"])){ 
    //     $fileName = basename($_FILES["file"]["name"]); 
    //     echo $fileName;
    //     $targetFilePath = $targetDir . $fileName; 
    //     $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
     
    //     // Allow certain file formats 
    //     $allowTypes = array('jpg','png','jpeg','gif','pdf'); 
    //     if(in_array($fileType, $allowTypes)){ 
    //         // Upload file to server 
    //         if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
    //             // Insert image file name into database 
    //             $insert = $mysqli->prepare("INSERT INTO images (email, picture) VALUES (?,?)"); 
    //             $insert->bind_param("ss", $email, $fileName);
    //             $insert->execute();
    //             $insert->close();
    //             $mysqli->close();
    //             if($insert){ 
    //                 $statusMsg = "The file ".$fileName. " has been uploaded successfully."; 
    //             }else{ 
    //                 $statusMsg = "File upload failed, please try again."; 
    //             }  
    //         }else{ 
    //             $statusMsg = "Sorry, there was an error uploading your file."; 
    //         } 
    //     }else{ 
    //         $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.'; 
    //     } 
    // }else{ 
    //     $statusMsg = 'Please select a file to upload.'; 
    // } 
    // echo $statusMsg;




    // header('refresh:5; url=http://localhost/Calendar/GoogleMapsJS-master/index.html');     //////////////////////////////////////////ddddddddddddddddddddddddddddddddddddddddddd
} 
 // File upload directory 
$duration=60;
$cleanup=0;
$start="09:00";
$end="15:00";

function timeslots($duration,$cleanup,$start,$end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval= new DateInterval("PT".$cleanup."M");
    $slots= array();
    for($intStart = $start; $intStart<$end; $intStart->add($interval)->add($cleanupInterval))
    {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if($endPeriod>$end)
        {
            break;
        }
        $slots[] = $intStart->format("H:iA")."-".$endPeriod->format("H:iA");
    }
    return $slots;
}
// Include the database configuration file 

 
// $statusMsg = ''; 
 
// // Display status message 
// echo $statusMsg; 

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
<style>
     body
     {
            background-color: #eed9c4
        }
        .btn-primary 
        {
            background-color: #e11a27;
            border: none
        }
</style>
<body>
    <div class="container">
        <h1 class="text-center">Branch name: <?php echo $prebranch?></h1>
        <h1 class="text-center">Book for Date:<?php echo date('d F Y',strtotime($date)) ?></h1><hr>
        <button class='btn btn-primary' onclick="window.location.href='Calendar.php'">Back to calendar</button>
<hr>
        <div class="row">
            
            <?php $timeslots= timeslots($duration,$cleanup,$start,$end);
            foreach($timeslots as $ts){
            ?>
            <div class="col-md-2">
                <?php if(in_array($ts,$bookings)){?>
                    <button class="btn btn-danger"><?php echo $ts;?></button>
                <?php }else{?>
                    <button class="btn btn-success book"data-timeslot="<?php echo $ts;?>"><?php echo $ts;?></button>
                <?php }?>
            </div>
            <?php }?>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Booking: <spanid="slot"></span></h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
        
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="">Branch</label>
                        <input required type="text" readonly class="form-control" name="branch" value="<?php echo $_SESSION['branch']?>">
                    </div>

                    <div class="form-group">
                        <label for="">Timeslot</label>
                        <input required type="text" readonly name="timeslots" id="timeslot">
                    </div>
                    <div class="formgroup">
                            <label for="">Name</label>
                            <input required type="text" class="form-control" name="name">
                    </div>
                    <div class="formgroup">
                        <label for="">Email</label>
                        <input required type="email" class="form-control" name="email">
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
                    
                    <div id="file-input-container">
                        <div class="formgroup">
                        Select Image File to Upload:
                        <input type="file" name="file[]" Multiple>
                        </div>
                    </div>
                    <!-- <button id='addfiles' onclick="addfiles">add more files</button> -->
                    
                    <button class="btn btn-primary" type="submit" name="submit" value="Upload">Submit</button>
                    </br>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    
                </div>
            </div>
        </div>
        </div>
    </div>
    
</div>
</div>



        </div>
    </div>
    <script>
        $(".book").click(function(){
            var timeslot=$(this).attr('data-timeslot');
            $("#slot").html(timeslot);
            $("#timeslot").val(timeslot);
            $("#myModal").modal("show");
        })
        // document.getElementById('addfiles').addEventListener('click', function(e) {
        // e.preventDefault();
        // // Create a new file input element
        // var newFileInput = document.createElement('input');
        // newFileInput.type = 'file';
        // newFileInput.name = 'file'; // Multiple file upload

        // // Append the new file input element to the container
        // document.getElementById('file-input-container').appendChild(newFileInput);
        // // Prevent page reload
        // return false;
        // });
        
    </script>
</body>
</html>

