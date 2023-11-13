<?php
if(isset($_GET['date']))
{
    $date = $_GET['date'];
}

if(isset($_POST['submit']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli = new mysqli('localhost','root','','apptcalendar');
    $stmt = $mysqli->prepare("insert into booking (name,email,date) values (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $date);
    $stmt->execute();
    $msg="<div class='alert alert-success'>Booking Successful</div>";
    $stmt->close();
    $mysqli->close();
}
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
                <form action="upload.php" method="post" autocomplete="off" enctype="multipart/form-data">
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
                        <label for="reason">Additional Notes <span style="font-size:10px">(Enter reason if others is selected)</span></label>
                        <input type="text" class="form-control" placeholder="Type Here..." name="reason">

                    </div>
                    <!-- <div class="formgroup"> -->

                    Select image to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload Image" name="submit">
                    
                    <!-- </div> -->
                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
