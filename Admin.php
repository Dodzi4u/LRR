<?php
include 'NoDirectPhpAcess.php';
?>

<?php
$page = "admin";
include 'Header.php';
?>

<?php
if ($_SESSION['user_type'] != "Lecturer") {
  $_SESSION["info_login"] = "You must log in first.";
  echo $_SESSION["info_login"];
  header("Location: index.php");
}
?>

<style>
  .colmd4 {
    borderright: 1px solid skyblue;
  }
</style>

<br>
<div style="width: 80%;margin: auto;">
  <h2> Administration Panel </h2>
</div>

<hr>
<div class="row" style="width: 80%;margin: auto;">

  <!<h4>General system Settings</h4><hr>
        <a href="" class="btn btnlg btnprimary">View System Log </a>
        <hr>
     Lab Privacy Mode: (STUDENT VERIFICATION)
        <hr>
    >
  <div class="colmd6">
    <h4> User Account Management </h4>
    <hr>

    <b>Lecturer / TA Accounts </b><br>

    <div class="container">

      <! Nav tabs >
      <ul class="nav navtabs" role="tablist">

        <li class="navitem">
          <a class="navlink active" datatoggle="tab" href="#home">Create New Account</a>
        </li>

        <li class="navitem">
          <a class="navlink" datatoggle="tab" href="#menu2" id="batch_tab">Batch Create New Student Accounts</a>
        </li>

        <li class="navitem">
          <a class="navlink" datatoggle="tab" href="#menu1" id="existing_accounts_tab">Existing Accounts</a>
        </li>

      </ul>

      <! Tab panes >
      <div class="tabcontent">

        <div id="home" class="container tabpane active"><br>

          <b>Create Lecturer/TA Accounts </b>
          <form method="post" action="Script.php" id="create_account_form">
            <input type="hidden" name="frm_createlecturrer" value="true" required="" />
            Full_Name
            <input type="text" name="fullname" placeholder="Full Name" class="formcontrol" required="">
            Email
            <input type="text" name="email" placeholder="Email / Student Number" class="formcontrol" required="">

            Passport_Number / ID (Used as Intial Password)
            <input type="text" class="formcontrol" name="passport" placeholder="Passport No./ID" required="">
            <br> User Type :
            <input type="radio" name="type" value="Lecturer" required="" id="role_lecturer"> Lecturer
            <input type="radio" name="type" value="TA" required="" id="role_TA"> T/A
            <input type="submit" class="btn btnprimary" value="Create" id="create_btn"><br>
            <?php

            error_reporting(E_ALL);
            if (isset($_SESSION['info_Admin_Users'])) {
              echo  '<hr><div class="alert alertinfo" role="alert">' . $_SESSION['info_Admin_Users'] . '</div>';
              $_SESSION['info_Admin_Users'] = null;
            }
            if (isset($_SESSION['info_Admin_Users'])) {
              echo  '<hr><div class="alert alertinfo" role="alert">' . $_SESSION['info_Admin_Users'] . '</div>';
              $_SESSION['info_Admin_Users'] = null;
            }

            ?>

          </form>

          <hr>

        </div>

        <div id="menu1" class="container tabpane fade"><br>

          <table class="tablebordered" style="fontsize: 10pt;">
            <tr style="fontsize:10pt;">
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Passport / ID </th>
              <th>Reset Password </th>
              <th>Block/Activate </th>
            </tr>
            <?php

            $result = mysqli_query(
              $con,
              "SELECT * FROM Users_Table  WHERE UserType in ('Lecturer','TA')"
            );
            while ($row = mysqli_fetch_assoc($result)) {
              $pass = $row['Passport_Number'];
              $btn = "<button class='btnprimary' onclick=\"updatePass(" . $row['User_ID'] . ",'$pass')\">Reset</button>";
              if ($row['Status'] == "Active") {
                $newstatus = "Blocked";
                $btnBlock = "<button class='btndanger' onclick=\"blockUser(" . $row['User_ID'] . ",'$newstatus')\" id=\"block_account_1\">Block</button>";
              } else {
                $newstatus = "Active";
                $btnBlock = "<button class='btnsuccess' onclick=\"blockUser(" . $row['User_ID'] . ",'$newstatus')\" id=\"activate_account_1\">Activate</button>";
              }

              echo "<tr><td>" . $row['User_ID'] . "</td><td>" . $row['Full_Name'] . "</td><td>" . $row['Email'] . "</td> <td>" . $row['Passport_Number'] . "</td><td>$btn</td><td>$btnBlock</td></tr>";
            }
            ?>
          </table>

        </div>

        <! code contributed by Xu Xiaopeng (https://github.com/xxp1999) >

        <div id="menu2" class="container tabpane fade" style="margintop:10px">
          <b>Separate two student numbers with a space.</b><br>
          <form action="batch_insert.php" method="post" id="batch_form">
            <p>
              <textarea cols="80" rows="16" name="users" required=""></textarea>
            </p>
            <input type="submit" class="btn btnprimary" value="Register Students" id="register_btn"><br>
          </form>
        </div>

      </div>
    </div>

  </div>

  <div class="colmd6">

    <div class="container">
      <! Nav tabs >
      <ul class="nav navtabs" role="tablist">
        <li class="navitem">
          <a class="navlink active" datatoggle="tab" href="#menub" id="existing_courses">Existing Courses</a>
        </li>

      </ul>

      <! Tab panes >

        </div>

        <div id="menub" class="container tabpane active"><br>

          <b> Existing Course Portals </b>
          <hr>
          <table class="tablebordered" style="fontsize: 10pt;">
            <tr>
              <th>Course Name </th>
              <th> Faculty </th>
              <th>Lecturer </th>
              <th>TAs</th>
              <th>Assign new TA </th>
            </tr>
            <?php
            $result = mysqli_query($con, "SELECT `Course_ID`, `Course_Name`, `Academic_Year`, `Faculty`, `Lecturer_User_ID`, `TA_User_ID`, `Course_Code`, `URL`, `Verify_New_Members`   , users_table.Full_Name  FROM `courses_table` INNER JOIN users_table ON users_table.User_ID=courses_table.Lecturer_User_ID");
            if (mysqli_num_rows($result) == 0) {
            } else {
              $counter = 0;
              while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['Course_Name'];
                $code = $row['Course_Code'];
                $faculty = $row['Faculty'];
                $lecturer = $row['Full_Name'];
                $academic = $row['Academic_Year'];
                $c_id = $row['Course_ID'];
                $counter += 1;

                $resultTA = mysqli_query($con, "SELECT `Course_ID`, `TA`,users_table.Full_Name as TA_NAME FROM `course_ta`
INNER JOIN users_table on users_table.User_ID=course_ta.TA
where course_ta.Course_ID=$c_id");

                $ta = "";
                while ($rowTA = mysqli_fetch_assoc($resultTA)) {
                  $ta = $ta . "   " . $rowTA['TA_NAME'];
                }

                echo "  
                          <tr> <td>$code  $name</td>  <td>$faculty </td> <td>$lecturer</td><td>$ta</td>  <td><form method='get' action='Script.php' id='drop_menu_form_$counter'> <select name='ta' class=''>";

                $resultx = mysqli_query($con, "SELECT * FROM Users_Table WHERE UserType='TA'");
                if (mysqli_num_rows($resultx) == 0) {
                } else {
                  while ($row = mysqli_fetch_assoc($resultx)) {
                    $id = $row['User_ID'];
                    $name = $row['Full_Name'];
                    echo "<option value='$id'> $name </option>";
                  }
                }

                echo "</select>  <input type='hidden' name='assignTA' value='true'> <input type='hidden' name='id' value='$c_id'>  <input type='submit' value='assign' id='assign_btn_$counter'></form> </td></tr>
                         ";
              }
            } ?>

          </table>

        </div>

      </div>

    </div>

    <script>
      function updatePass(id, pass) {
        if (!confirm('Are you to Reset User Password')) {
          return;
        }

        window.location.href = "\Script.php\?action=passchange&uid=" + id + "&pass=" + pass;
      }

      function blockUser(id, status) {
        if (!confirm('Are you to change User Status')) {
          return;
        }
        window.location.href = "\Script.php\?action=statuschange&uid=" + id + "&status=" + status;
      }
    </script>