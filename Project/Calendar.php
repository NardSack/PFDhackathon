<!DOCTYPE html>
<?php
function build_calendar($month,$year){
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
   
   //create HTML table 
   $calendar = "<table class='table table-bordered'>";
   $calendar.="<center><h2>$monthName $year</h2>";

    $calendar.="<tr>";
    //create the calendar headers
    foreach($daysOfWeek as $day)
    {
        $calendar.= "<th class='header'>$day</th>";
    }
    $calendar.= "</tr><tr>";
    //7 comlumns on the table
    if($dayOfWeek > 0){
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar.="<td></td>";
        }
    }

    //initiating the day counter
    $currentDay = 1;
    // Getting the month number
    $month=str_pad($month,2,"0", STR_PAD_LEFT);

    while ($currentDay<=$numberDays){

        //7th column reached,start a new row
        if ($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calendar.="</tr><tr>";
        }


        $currentDayRel = str_pad($currentDay,2,"0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";  
        $calendar.= "<td><h4>$currentDay</h4>";
        $calendar.= "</td>";

        //Incrementing the counters
        $currentDay++;
        $dayOfWeek++;
    }
    //completing the row of the last week in the month
    if($dayOfWeek != 7){
        $remainDays = 7-$dayOfWeek;
        for($i=0;$i<$remainDays;$i++){
            $calendar.="<td></td>";
        }
    }
    $calendar.= "</tr>";
    $calendar.= "</table>";
    echo $calendar;
}
?>
<html>
<head>
    <title>My Calendar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                    $dateComponents =getdate();
                    $month= $dateComponents["mon"];
                    $year= $dateComponents["year"];
                    echo build_calendar($month,$year);
                ?>
            </div>
        </div>
    </div>
</body>
</html>
