<?php
include 'NoDirectPhpAcess.php';
?>

<?php

$page = 'Courses';
include 'Header.php';

$user_d = $_SESSION['user_id'];

if ($_SESSION['user_type'] == "Lecturer" || $_SESSION['user_type'] == "TA") {
?>

    <!    FOR LECTURER>

    <div class="row" style="width:80%;margin:auto; textalign:left;">

        <script src="./css/jquery1.11.1.min.js"></script>
        <script src="./css/jqueryui.min.js"></script>
        <link rel="stylesheet" href="./css/jqueryui.css" />

        <script>
            function extend_deadline(id) {

                var dropstudents = $("#dropstudents").html();

                try {

                    $('<form id="frm" method="get" action="Script.php">\n\
    <input type="hidden" name="extenddeadline" value="true" >\n\
   <input type="hidden" name="id" value="' + id + '" > \n\
New Date/Time <br><input type="date" name="date" required="" id="new_date"> <input type="time" name="time" required=""> \n\
  \n\
<br><input type="radio" value="1" name="type" required="" id="extend_for_all"> Extend for All<hr>  \n\
<input type="radio" value="2" name="type" required=""> Extend for these Individual Students \n\
 ' + dropstudents + '   \n\
</form>').dialog({
                        modal: true,
                        title: 'Extend Deadline',
                        buttons: {
                            'Submit': function() {
                                $('#frm').submit();

                                $(this).dialog('close');
                            },
                            'X': function() {

                                $(this).dialog('close');
                            }

                        }
                    });

                } catch (e) {
                    alert(e);
                }
            }
        </script>

        <?php
        if (!empty($_GET["course"])) {
            $course_url = $_GET["course"];
            $result = mysqli_query($con, "SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`,"
                . " `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`  "
                . " , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table"
                . " ON users_table.User_ID=courses_table.Lecturer_User_ID where URL='$course_url' ");

            if (mysqli_num_rows($result) == 0) {
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = $row['Course_Name'];
                    $code = $row['Course_Code'];
                    $faculty = $row['Faculty'];
                    $lecturer = $row['Full_Name'];
                    $academic = $row['Academic_Year'];
                    $url = $row['URL'];
                    $id = $row['Course_ID'];
                    $course_id = $row['Course_ID'];
                    echo    "  
                  
                        <div class='alert> <a href='~\..\Courses.php?course=$url'>   <div class='panel'>
  ($code)  $name 
   <br> <span style='fontsize:8pt'>Faculty: $faculty | Year: $academic | Lecturer: $lecturer </span>
</div></a>
                        <hr></div></div> <div class='row' style='width:80%;margin:auto; textalign:left;'>
 ";

                    echo "<div class='colmd5'>";
                }

                // Editing Lab Assignment by Lecturer 

                if ($_GET['act'] == "edit") {
                    $getid = $_GET["cid"];
                    $result1 = mysqli_query($con, "SELECT * from lab_reports_table WHERE Lab_Report_ID = '$getid'");

                    while ($row1 = mysqli_fetch_assoc($result1)) {
                        $Deadline = $row1['Deadline'];
                        $_SESSION['Date'] = trim(strstr($Deadline, ' ', true));
                        $_SESSION['Time'] = trim(strstr($Deadline, ' '));
                        $_SESSION['Instructions'] = $row1['Instructions'];
                        $_SESSION['Title'] = $row1['Title'];
                        $_SESSION['Marks'] = $row1['Marks'];
                        $_SESSION['Type'] = $row1['Type'];
                    }

                    if (isset($_POST['frm_uploadlab'])) {
                        $deadlinedate = trim($_POST["deadlinedate"]); // remove spaces
                        $deadlinetime = trim($_POST["deadlinetime"]); // remove spaces
                        $instructions = $_POST["instructions"];
                        $title = $_POST["title"];
                        $marks = $_POST["marks"];
                        $type  = $_POST["type"];
                        $Deadline = $deadlinedate . " " . $deadlinetime;
                        $date =  date("Ymd H:i");

                        $sql = "UPDATE `lab_reports_table` SET `Deadline` = ('" . $Deadline . "'), `Instructions` = ('" . $instructions . "'), `Title` = ('" . $title . "'), `Marks` = ('" . $marks . "'), `Type` = ('" . $type . "') WHERE `lab_reports_table`.`Lab_Report_ID` = '$getid'";
                        if ($con>query($sql) === TRUE) {
                            $_SESSION["info_Updated"] = "Assignment information updated successfully.";
                        } else {
                            // echo "Error: " . $sql . "<br>" . $con>error;
                            echo "Serious error happened whiling updating assignment information.";
                        }
                    }

                    if ($_SESSION['user_type'] == "Lecturer") {
                        $Date = $_SESSION['Date'];
                        $Time = $_SESSION['Time'];
                        $Instructions = $_SESSION['Instructions'];
                        $Title = $_SESSION['Title'];
                        $Marks = $_SESSION['Marks'];
                        $Type = $_SESSION['Type'];

                        echo "  <h3><a href='Courses.php?course=" . $url . "'> Editing Lab Assignment </a></h3>";
        ?>
                        <form method='post' enctype='multipart/formdata' action=''>
                            <input type='hidden' name='frm_uploadlab' value='true' required='' />
                            <input type='hidden' name='course_id' value='<?php echo "$id" ?>' required='' />
                            <input type='hidden' name='url' value='<?php echo ".$course_url." ?>' required='' />

                            Dealine Date/Time
                            <div class='row'>
                                <div class='colmd7'><input type='date' id='date' name='deadlinedate' placeholder='' class='formcontrol' required='' value="<?php echo isset($_GET['act']) && $_GET['act'] == "edit" ? $Date : ""; ?>"> </div>
                                <div class='colmd5'> <input type='text' id='time' class='formcontrol' name='deadlinetime' value="<?php echo isset($_GET['act']) && $_GET['act'] == "edit" ? $Time : ""; ?>"> </div>
                            </div>

                            Title
                            <input type='text' name='title' placeholder='Ttle' class='formcontrol' required='' value="<?php echo isset($_GET['act']) && $_GET['act'] == "edit" ? $Title : ""; ?>">
                            Instructions
                            <textarea name='instructions' placeholder='Assignment Instructions' class='formcontrol' required=''><?php echo isset($_GET['act']) && $_GET['act'] == 'edit' ? $Instructions : ''; ?></textarea>
                            Marks
                            <input type='text' name='marks' placeholder='Marks' class='formcontrol' required='' value="<?php echo isset($_GET['act']) && $_GET['act'] == "edit" ? $Marks : ""; ?>">
                            Attachment 1
                            <input type='file' name='attachment1' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 2
                            <input type='file' name='attachment2' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 3
                            <input type='file' name='attachment3' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 4
                            <input type='file' name='attachment4' placeholder='Attachment 4' class='formcontrol'>
                            <br>

                            <?php
                            if ($Type == "Individual") {
                                echo "Submission Type  <input type='radio' name='type' value='Individual' checked /> Individual  <input type='radio' name='type' value='Group' /> Group";
                            } else {
                                echo "Submission Type  <input type='radio' name='type' value='Individual' /> Individual  <input type='radio' name='type' value='Group' checked> Group";
                            }
                            ?>

                            <hr>
                            <input type='submit' class='btn btnprimary' value='Post Lab Assignment'><br>
                        </form><br><br><br><br>
                    <?php
                    }
                } else {

                    // Posting New Lab Assignment

                    // Mysql to split 1 string into 2 similar to the tsrstr in php
                    // SELECT SUBSTRING_INDEX(Deadline, ' ', 1) as Date, SUBSTRING_INDEX(Deadline, ' ', 1) as Time from lab_reports_table

                    if ($_SESSION['user_type'] == "Lecturer") {

                    ?>

                        <h3> Post new Lab Assignment </a></h3>

                        <form method='post' enctype='multipart/formdata' action='Script.php' id="newlab_form">
                            <?php
                            $_SESSION['url'] = $url;
                            ?>
                            <input type='hidden' name='frm_uploadlab' value='true' required='' />
                            <input type='hidden' name='course_id' value='<?php echo "$id" ?>' required='' />
                            <input type='hidden' name='url' value='<?php echo ".$course_url." ?>' required='' />

                            Dealine Date/Time
                            <div class='row'>
                                <div class='colmd7'><input type='date' id='date' name='deadlinedate' placeholder='' class='formcontrol' required='' value=""> </div>
                                <div class='colmd5'> <input type='time' class='formcontrol' name='deadlinetime' value=""> </div>
                            </div>

                            Title
                            <input type='text' name='title' placeholder='Ttle' class='formcontrol' required='' value="" id="lab_title">
                            Instructions
                            <textarea name='instructions' placeholder='Assignment Instructions' class='formcontrol' required='' value="" id="lab_instructor"></textarea>
                            Marks
                            <input type='text' name='marks' placeholder='Marks' class='formcontrol' required='' value="" id="lab_mark">
                            Attachment 1
                            <input type='file' name='attachment1' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 2
                            <input type='file' name='attachment2' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 3
                            <input type='file' name='attachment3' placeholder='Attachment 1' class='formcontrol'>

                            Attachment 4
                            <input type='file' name='attachment4' placeholder='Attachment 4' class='formcontrol'>
                            <br>
                            Submission Type <input type='radio' name='type' value='Individual' required='' id="lab_individual"> Individual

                            <input type='radio' name='type' value='Group' required='' id="lab_group"> Group
                            <hr>
                            <input type='submit' class='btn btnprimary' value='Post Lab Assignment' id="submit_btn"><br>
                        </form><br><br><br><br>
        <?php
                    }
                }
            }
            echo "</div>";

            echo "<div class='colmd7'>  <h3> Lab Report Assignment list </h3>";

            error_reporting);
            if (isset($_SESSION["info_Updated"])) {
                echo '<hr><div class="alert alertinfo" role="alert">' . $_SESSION['info_Updated'] . '</div>';
                $_SESSION['info_Updated'] = null;
            }
            if (isset($_SESSION['info_courses'])) {
                echo '<hr><div class="alert alertinfo" role="alert">' . $_SESSION['info_courses'] . '</div>';
                $_SESSION['info_courses'] = null;
            }
            if (isset($_SESSION['info_courses'])) {
                echo '<hr><div class="alert alertinfo" role="alert">' . $_SESSION['info_courses'] . '</div>';
                $_SESSION['info_courses'] = null;
            }

            $result = mysqli_query($con, " SELECT `Lab_Report_ID`,Type,Marks, `Course_ID`, `Posted_Date`, `Deadline`, `Instructions`, `Title`, `Attachment_link_1`, `Attachment_link_2`, `Attachment_link_3`, "
                . "`Attachment_link_4` FROM `lab_reports_table` WHERE Course_ID=$id ORDER by Lab_Report_ID DESC");

            if ($_SESSION['user_type'] == "TA") {
                echo "<b style='color:gray'>*Only Lecturer can post a new lab report assignment</b><br>";
            }
            if (mysqli_num_rows($result) == 0) {
                echo "No assignments posted so far.";
            } else {
                $counter = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $marks = $row['Marks'];
                    $title = $row['Title'];
                    $ins = $row['Instructions'];
                    $posted = $row['Posted_Date'];
                    $deadline = $row['Deadline'];
                    $att1 = $row['Attachment_link_1'];
                    $att2 = $row['Attachment_link_2'];
                    $att3 = $row['Attachment_link_3'];
                    $att4 = $row['Attachment_link_4'];
                    $id = $row['Lab_Report_ID'];
                    $cours_id = $row['Course_ID'];
                    $as_type = $row['Type'];
                    $full_link = "<a href='~\..\Lab_Report_Assignments\\$att1'>$att1</a>";
                    $counter += 1;

                    if ($att2 != "") {
                        $full_link = $full_link . " &nbsp|&nbsp <a href='~\..\Lab_Report_Assignments\\$att2'>$att2</a>";
                    }
                    if ($att3 != "") {
                        $full_link = $full_link . " &nbsp|&nbsp <a href='~\..\Lab_Report_Assignments\\$att3'>$att3</a>";
                    }

                    if ($att4 != "") {
                        $full_link = $full_link . " &nbsp; | &nbsp <a href='~\..\Lab_Report_Assignments\\$att4'>$att4</a>";
                    }

                    $resultx1 = mysqli_query($con, "Select Count(*) as cnt from lab_report_submissions where lab_report_submissions.Lab_Report_ID=$id");
                    while ($row = mysqli_fetch_assoc($resultx1)) {
                        $count_subs = $row['cnt'];
                    }

                    $resultx2 = mysqli_query($con, "Select COUNT(*) as cnt from lab_report_submissions where lab_report_submissions.Lab_Report_ID=$id and Marks is not null");
                    if (mysqli_num_rows($resultx2) == 0) {
                        $count_marked = 0;
                    } else {
                        while ($row = mysqli_fetch_assoc($resultx2)) {
                            $count_marked = $row['cnt'];
                        }
                    }

                    $header = "Courses > " . $name . "($code) > Assignments > " . $title;

                    echo "      <div class='breakword btn btndefault' style='wordwrap: breakword;bordercolor:grey;'>
  $title ($as_type) <br> $ins
  
   <br> <span style='fontsize:8pt'>Posted : $posted  Deadline :  <b> $deadline </b> &nbsp; ($marks Marks)      &nbsp;    &nbsp; &nbsp; &nbsp; &nbsp;   "
                        . "<br>"

                        . "<span class='btndefault'> &nbsp;&nbsp; $count_subs Submissions ( $count_marked Marked ) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='Courses.php?course=" . $url . "&act=edit&cid=" . $id . "'>Edit</a>&nbsp;&nbsp; |&nbsp;&nbsp;<a href='~\..\Submissions.php?id=$id&header=$header&total=$marks' onclick='' id='view_submissions_link_$counter'> View </a>     &nbsp;&nbsp; |&nbsp;&nbsp;         <a href='#'  onclick='extend_deadline($id)' id='extend_deadline_link'> Extend Deadline </a>  </span>         <hr> Attachments : $full_link </span>"
                        . "&nbsp;&nbsp;</div>
                        ";
                }
            }
            echo "</div>";

            $resultx1 = mysqli_query($con, "SELECT course_students_table.Student_ID,users_table.Full_Name FROM 
`course_students_table`
INNER JOIN users_table on users_table.Student_ID=course_students_table.Student_ID
WHERE Course_ID=$course_id");

            echo "<span id='dropstudents' style='display:none;'> <select name='stdid'>";
            while ($row = mysqli_fetch_assoc($resultx1)) {
                $stdid = $row['Student_ID'];
                $stdname = $row['Full_Name'];

                echo "<option value='$stdid'> $stdname($stdid) </option> ";
            }
            echo "</select><br>Reason <input type='text' name='reason'>"
                . "<input type='hidden' name='url' value='$course_url'>"
                . " </span>";

            return;
        }

        ?>

        <div class="colmd8">

            <?php
            $user_name = $_SESSION['user_fullname'];

            echo    "  <div class='alert' style='marginleft:20px;borderbottom:2px solid #1D91EF;'> <a href='~\..\Courses.php?course=$url'>
  Course Portal  > $user_name (Lecturer) > Course Listing
   <br> <span style='fontsize:8pt'> </span>
</a></div>
 ";

            $result = mysqli_query($con, "SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`, "
                . "`Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`   , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table ON users_table.User_ID=courses_table.Lecturer_User_ID where courses_table.Lecturer_User_ID=$user_d");

            if ($_SESSION['user_type'] == "TA") {
                $result = mysqli_query($con, "SELECT course_ta.Course_ID, `Course_Name`, 
          `Academic_Year`, `Faculty`, `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`   FROM `courses_table` 
INNER JOIN 
course_ta ON course_ta.Course_ID=courses_table.Course_ID where course_ta.TA=$user_d");
            }
            // $result = mysqli_query($con,"SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`, `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`   , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table ON users_table.User_ID=courses_table.Lecturer_User_ID");

            if (mysqli_num_rows($result) == 0) {
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['Course_ID'];
                    $name = $row['Course_Name'];
                    $code = $row['Course_Code'];
                    $faculty = $row['Faculty'];
                    $lecturer = $row['Full_Name'];
                    $academic = $row['Academic_Year'];
                    $url = $row['URL'];

                    $resultTA = mysqli_query($con, "SELECT `Course_ID`, `TA`,users_table.Full_Name as TA_NAME FROM `course_ta`
INNER JOIN users_table on users_table.User_ID=course_ta.TA
where course_ta.Course_ID=$id");

                    $ta = "";
                    while ($rowTA = mysqli_fetch_assoc($resultTA)) {
                        $ta = $ta . "   " . $rowTA['TA_NAME'];
                    }

                    echo "  
                  
                         <a href='~\..\Courses.php?course=$url'>   <div class='btn btndefault'>
  ($code)  $name 
   <br> <span style='fontsize:8pt'>Faculty : $faculty &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Year :  $academic  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Lecturer  :$lecturer  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  TA:$ta </span>
</div></a>
                        ";
                }
            } ?>
        </div>
        <div class="colmd4">
            <br>
            <b> Course Joining Requests </b>

            <?php
            $lecturer_id = $_SESSION['user_id'];
            $result = mysqli_query($con, "SELECT  course_students_table.ID,users_table.Full_Name,  courses_table.Course_ID, `Course_Name`, `Academic_Year`, `Faculty`, `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members` FROM `courses_table` 
INNER JOIN course_students_table on  course_students_table.Course_ID=courses_table.Course_ID
INNER JOIN users_table on users_table.Student_ID=course_students_table.Student_ID
WHERE  Lecturer_User_ID=$lecturer_id and course_students_table.Status='Pending'");

            if (mysqli_num_rows($result) == 0) {

                echo "<br>  <i class='fa fainfocircle'></i> No Course joining request so far for all your courses <hr>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['ID'];

                    $name = $row['Course_Name'];
                    $code = $row['Course_Code'];
                    $faculty = $row['Faculty'];
                    $std_name = $row['Full_Name'];
                    $academic = $row['Academic_Year'];

                    echo "<div class='btn btndefault'>
 $std_name is Requesting to join <br> [($code)  $name ] &nbsp;&nbsp;&nbsp;&nbsp; <br><a href='~\..\Script.php?AcceptStudent=y&id=$id&rs=yes' class='btnsm btnsuccess' onclick=return confirm(\"are you sure to join this course?\")' > Accept </a>
 &nbsp;&nbsp;<a href='~\..\Script.php?AcceptStudent=y&id=$id&rs=no' class='btnsm btndanger' onclick=return confirm(\"are you sure to join this course?\")' > Decline </a>                     
</div>";
                }
            }
            ?>

            <?php
            if ($_SESSION['user_type'] == "TA") {
                echo "<center>Only Lecturers can Post new Lab report Assignments</center>";
            }
            if ($_SESSION['user_type'] == "Lecturer") { ?>

                <b>Create new Course Portal </b>

                <form method="post" action="Script.php">
                    <input type="hidden" name="frm_createCourse" value="true" required="" />
                    <input type="hidden" name="l" value="l" required="" />
                    Course Name
                    <input type="text" name="name" placeholder="Course Name" class="formcontrol" required="" id="course_name">

                    Course Code
                    <input type="text" name="code" placeholder="Course Code" class="formcontrol" required="" id="course_code">

                    URL (Leave blank to use Course Code & Year)
                    <input type="text" name="url" placeholder="Choose Custom URL " class="formcontrol">

                    Academic Year
                    <input type="text" name="academic" placeholder="Academic Year" class="formcontrol" required="" id="academic_year">

                    Faculty <br>
                    <input type="text" name="faculty" placeholder="Faculty" class="formcontrol" required="" id="faculty">

                    <input type="hidden" name="lecturer" value="<?php echo $_SESSION['user_id'];  ?>">

                    Verify Joining Students
                    <input type="radio" name="verify" value="1" id="join_yes"> Yes
                    <input type="radio" name="verify" value="0" checked="" id="join_no"> No

                    <br>
                    <input type="submit" class="btn btnprimary" value="Create Portal" id="portal_btn"><br>

                </form>

            <?php }  ?>

        </div>

        <!   END LECTURER   >

    <?php
}

if ($_SESSION['user_type'] == "Student") {
    ?>

        <!STUDENT CODE>
        <div class="row" style="width:80%;margin:auto; textalign:left;">
            <div class="colmd6">
                <br> Course Portal > Students <br>
                <?php

                error_reporting);
                if (isset($_SESSION['info_Courses_student'])) {
                    echo '<hr><span class="alert alertsuccess" role="alert">' . $_SESSION['info_Courses_student'] . '</span>';
                    $_SESSION['info_Courses_student'] = null;
                }
                ?>
                <br><br>
            </div>
            <div class="colmd6"></div>
        </div>

        <div class="row" style="width:80%;margin:auto; textalign:left;">
            <div class="colmd6">

            <?php
            error_reporting);
            $student_id = $_SESSION['user_student_id'];
            if (!empty($_GET["search"]) || !empty($_GET["faculty"])) {
                $search = trim($_GET["search"]);
                $faculty = $_GET["faculty"];

                if ($faculty == "") {
                    echo "<h4> Search Results for Course Code $search</h4><hr>";
                    $result = mysqli_query($con, "SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`,"
                        . " `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`  "
                        . " , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table"
                        . " ON users_table.User_ID=courses_table.Lecturer_User_ID where Course_Code like '%{$search}%' and courses_table.Course_ID not in (select course_id from course_students_table where Student_ID=$student_id)");
                } else {
                    echo "<h3> Find Courses under faculty $faculty</h3>";
                    $result = mysqli_query($con, "SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`,
       `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members` 
         , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table
         ON users_table.User_ID=courses_table.Lecturer_User_ID where Faculty='$faculty'  and courses_table.Course_ID not in (select course_id from course_students_table where Student_ID=$student_id)");
                }

                if (mysqli_num_rows($result) == 0) {
                    echo "No results found for your Search <hr>";
                } else {

                    while ($row = mysqli_fetch_assoc($result)) {
                        $name = $row['Course_Name'];
                        $code = $row['Course_Code'];
                        $faculty = $row['Faculty'];
                        $lecturer = $row['Full_Name'];
                        $academic = $row['Academic_Year'];
                        $url = $row['URL'];
                        $id = $row['Course_ID'];
                        $v = $row['Verify_New_Members'];
                        $msg2 = "Join Course";
                        if ($v > 0) {
                            $msg = "<i class='fa faexclamationcircle'></i> Lecturer verification required";
                            $msg2 = "Send Joining Request";
                        }

                        echo "<div class='btn btndefault' style='wordwrap:breakword'>
  [$code] $name <br>($url) <br>  <a href='~\..\Script.php?JoinCourse=y&id=$id&std=$student_id&joining=$v' class='btnsm btnsuccess' onclick=return confirm(\"Are you sure to join this course?\")' id='join_btn'> $msg2 </a>
   <br> <span style='fontsize:10pt'>Faculty: $faculty | Year: $academic | Lecturer: $lecturer </span><br>$msg</div>
                        ";
                    }
                }
            }

            echo "<h4> My Courses </h4>";
            $result = mysqli_query($con, "SELECT users_table.Full_Name, course_students_table.Status, courses_table.Course_ID, `Course_Name`, `Academic_Year`, `Faculty`, `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members` FROM `courses_table`
INNER JOIN users_table
          ON users_table.User_ID=courses_table.Lecturer_User_ID

INNER JOIN course_students_table on course_students_table.Course_ID=courses_table.Course_ID

                  where course_students_table.Student_ID=$student_id");

            if (mysqli_num_rows($result) == 0) {
                echo "<i class='fa faexclamationcircle'></i> You are not Enrolled in any Course";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = $row['Course_Name'];
                    $code = $row['Course_Code'];
                    $faculty = $row['Faculty'];
                    $lecturer = $row['Full_Name'];
                    $academic = $row['Academic_Year'];
                    $url = $row['URL'];
                    $id = $row['Course_ID'];
                    $Status = $row['Status'];

                    if ($Status == "Joined") {
                        echo "<a href='~\..\Course.php?url=$url'>   <div class='btn btndefault' style='wordwrap:breakword'>
  ($code)  $name <br>($url)       &nbsp;&nbsp;&nbsp; <i class='fa facheckcircle'></i> $Status   &nbsp;&nbsp;&nbsp;&nbsp; <a href='~\..\Course.php?url=$url' class='btnsm btnprimary'> Open</a>
   <br> <span style='fontsize:8pt'>Faculty : $faculty  Year :   $academic  Lecturer  :$lecturer </span></div></a>
                        ";
                    } else {
                        echo "<div class='btn btndefault'>
  ($code)  $name  <i class='btnsm btndanger'> $Status</i>
   <br> <span style='fontsize:8pt'>Faculty : $faculty  Year :   $academic  Lecturer  :$lecturer </span></div>
                        ";
                    }
                }
            }

            echo "</div><div class='colmd6'>
    
        <form method='get' action='Courses.php'>
            <div class='row'> 
            <div class='colmd10'> 
            <div class='row'><div class='colmd6'> Find course by Code
            <input  type='text' class='formcontrol' name='search' placeholder='Enter Course Code' id='search_field'>
            </div><div class='colmd6'>
List courses by faculty

<select name='faculty' class='formcontrol'>";
            $result = mysqli_query($con, "SELECT   DISTINCT(Faculty) as Faculty FROM `courses_table`");
            if (mysqli_num_rows($result) == 0) {
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $fname = $row['Faculty'];

                    echo "<option value=''> Search by faculty </option> <option value='$fname'> $fname </option>";
                }
            }

            echo "</select></div></div>

</div>
                 <div class='colmd1'> <br>
            <input type='submit' class='btn btnprimary' value='Find' id='find_btn'>
            </div>
       
        </div>
        </form>
        
    </div></div>";
        }

            ?>

            <style>
                .formcontrol {
                    paddingtop: 1px;
                    paddingbottom: 1px;
                }
            </style>