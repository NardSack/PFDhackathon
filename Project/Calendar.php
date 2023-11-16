<!DOCTYPE html>
<?php
session_start();
$branch = $_SESSION['branch'];
   if(isset($_GET['branch']))
{
       $branch = $_GET['branch'];
        $_SESSION['branch'] = $branch;
}
function build_calendar($month,$year,$branch){

    $mysqli = new mysqli('localhost','root','','apptcalendar');

    $stmt = $mysqli->prepare("select * from branches");
    $branches = "";
    $first_branch = 0;
    $i = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                if($i==0){
                    $first_branch = $_SESSION['branch'];
                }
                // $branches.="<option value='".$row['id']."'>".$row['Branch']."</option>";
                $branches.="<option value='".$row['Branch']."'>".$row['Branch']."</option>";
                $i++;
            }
            $stmt->close();
        }
    }


    $stmt = $mysqli->prepare("select * from booking where MONTH(date) = ? AND YEAR(date) = ? and branch = ?");
    $stmt->bind_param('sss',$month,$year,$first_branch);
    // echo $branch;
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }


    // array containing the names of all days in a week
    $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    // retrieve the first day of the monthin the arugment of the function
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    //get the number of days in the month
    $numberDays = date('t',$firstDayOfMonth);
    // get infomation about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);
    // get the name of this month   
    $monthName = $dateComponents['month'];
    //get index value 0-6 of the first day of the month
    $dayOfWeek = $dateComponents['wday'];

    //get current date
   $dateToday=date('Y-m-d');
   $prev_month = date('m',mktime(0,0,0,$month-1,1,$year));
   $prev_year = date('Y',mktime(0,0,0,$month-1,1,$year));
   $next_month = date('m',mktime(0,0,0,$month+1,1,$year));
   $next_year = date('Y',mktime(0,0,0,$month+1,1,$year));


   $calendar = "<center><h2>$monthName $year</h2>";
   
   if ($prev_month < date('m') && $year == date('Y'))
   {

   }
   else 
   {
       $calendar.= "<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year."'>Prev Month</a>";
   }
    $calendar.= "<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a>";
    $calendar.="<a class = 'btn btn-primary btn-xs' href='?month=".$next_month."&year=$next_year'>Next Month</a></center>";

    $calendar.="<br>
    <form id ='branch_select_form'>
    <div class='row'>
        <div class ='col-md-6 col-md-offset-3 form-group'>
            <label>Select Branch</label>
            <select class='form-control' id='branch_select' name='branch'>
            <option value=''> Blank</option>".$branches."
            </select>
            <input type='hidden' name='month'value='".$month."'>
            <input type='hidden' name='year'value='".$year."'>
        </div>
    </div>
    </form>
    
    <table class='table table-bordered'>";
    $calendar.="<tr>";
    //create the calendar headers
    foreach($daysOfWeek as $day){
        $calendar.="<th class='header'>$day</th>";
    }

    $calendar.="</tr><tr>";
    $currentDay= 1;
    if($dayOfWeek>0){
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar.="<td class='empty'></td>";
        }
    }

    $month = str_pad($month,2,"0",STR_PAD_LEFT);
    while($currentDay<=$numberDays){
        //if seventh column (saturday) reached, start a new row
        if($dayOfWeek==7){
            $dayOfWeek=0;
            $calendar.="</tr><tr>";
        }
        $currentDayRel = str_pad($currentDay,2,"0",STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayname = strtolower(date('l',strtotime($date)));
        $today = $date == date('Y-m-d')? "today" : "";

        if($dayname=='sunday')
        {
            $calendar.="<td class='$today'><h4>$currentDay</h4><a class='btn btn-danger btn-xs';>Holiday</a></td>";
        }
        elseif($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDay</h4><a class='btn btn-danger btn-xs';>N/A</a></td>";
        }
        else{
            
            $_SESSION['dayname'] = $dayname;
            $totalbookings= checkSlots($mysqli,$date,$branch);
            if ($totalbookings==6)
            {
                $calendar.="<td class='$today'><h4>$currentDay</h4><a href='#' class='btn btn-danger btn-xs';>Fully booked</a></td>";
            }else{
                $availableslots = 6-$totalbookings;
                $_SESSION['branch'] = $branch;
                $calendar.="<td class='$today'><h4>$currentDay</h4><a href='book.php?date=$date' class='btn btn-success btn-xs';>Book</a><small><i>$availableslots slots left</i></small></td>";
            }
        }
        $currentDay++;
        $dayOfWeek++;
    }

    if($dayOfWeek<7)
    {
        $remainingDays = 7-$dayOfWeek;
        for($i=0;$i<$remainingDays;$i++){
            $calendar.="<td class='empty'></td>";
        }
    }
    $calendar.="</tr></table>";
    return $calendar;
}

function checkSlots($mysqli,$date,$branch)
{
    $stmt = $mysqli->prepare("select * from booking where date = ? and branch = ?");
    $stmt->bind_param('ss',$date,$branch);
    $totalbookings = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $totalbookings ++;
            }
            $stmt->close();
        }
    }
    return $totalbookings;
}
?>
<html>
<head>
    <title>My Calendar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add Bootstrap JS -->
    <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        @media only screen and (max-width: 760px), (min-device-width: 802px) and (max-device-width: 1020px){
            
            table,thead,tbody,tr,td{
                display: block;
            }
            .empty{
                display: none;
            }
            th{
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr{
                border: 1px solid #ccc;
            }
            td{
                border:none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50px;
            }
            
            td:nth-of-type(1):before{
                content: "Sunday";
            }
            td:nth-of-type(2):before{
                content: "Monday";
            }
            td:nth-of-type(3):before{
                content: "Tuesday";
            }
            td:nth-of-type(4):before{
                content: "Wednesday";
            }
            td:nth-of-type(5):before{
                content: "Thursday";
            }
            td:nth-of-type(6):before{
                content: "Friday";
            }
            td:nth-of-type(7):before{
                content: "Saturday";
            }
        }
        @media(min-width:641px){
        table{table-layout: fixed;}
        td{width: 33%;}
        .row{margin-top: 20px;}
        .today{
            background: yellow;
        }
        }
        
        </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <h1>Branch Name : <?php
            //  echo $_SESSION['branch'];
            echo $branch
            // ." hi here";
             ?></h1>
            <div class="col-md-12">
                <button class="btn btn-primary"onclick="window.location.href='../GoogleMapsJS-master/index.html'">Back to Map</button>
                <?php
                    $dateComponents = getdate();
                    if(isset($_GET['month']) && isset($_GET['year'])){
                        $month = $_GET['month'];
                        $year = $_GET['year'];
                    }else{
                        $month = $dateComponents['mon'];
                        $year = $dateComponents['year'];
                    }
                    if(isset($_GET['branch'])){
                        $branch = $_GET['branch'];
                    }else{
                        $branch = $_SESSION['branch'];
                    }
                    echo build_calendar($month,$year,$branch);
                ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script>
    $("#branch_select").change(function(){
        $("#branch_select_form").submit();
    });

    $("#branch_select option[value='<?php echo $branch?>']").attr('selected','selected');
    console.log($branch);
    </script>
</body>
</html>