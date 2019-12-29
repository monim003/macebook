<?php

  session_start();

  include 'connect.php';

  if($_SERVER['REQUEST_METHOD'] == "POST")
  {
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_pass = $_POST['user_pass'];

    $query = "INSERT INTO users(user_name,user_email,user_pass) VALUES('$user_name','$user_email','$user_pass')";

    $subquery = "SELECT * FROM users WHERE user_email = '$user_email'";
    $subresult = mysqli_query($connect, $subquery);

    if(mysqli_num_rows($subresult)>0)
    {
      $_SESSION['message'] = "E-mail already used ! Try another one !";
      header("Location: http://localhost/social/register.php");
    }
    else
    {
      mysqli_query($connect,$query);
      $query = "SELECT * FROM users WHERE user_email = '$user_email' AND user_pass = '$user_pass'";

      $result = mysqli_query($connect, $query);
      while($row = mysqli_fetch_assoc($result))
      {
        $_SESSION['user_name'] = $row['user_name'];
        $_SESSION['user_email'] = $row['user_email'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['address'] = $row['address'];
        $_SESSION['date_of_birth'] = $row['date_of_birth'];
        $_SESSION['proffession'] = $row['proffession'];
        $_SESSION['interests'] = $row['interests'];
      }
      header("Location: http://localhost/social/index.php");
    }
  }

?>
<!DOCTYPE html>
<html>
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body>

<header class="w3-container w3-teal">
  <h1>Register</h1>
</header>

<div class="w3-container w3-display-middle w3-half w3-margin-top">

  <form action="" class="w3-container w3-card-4" method="post">


        <p> <label style="color:red;"><?php if(isset($_SESSION['message'])) echo $_SESSION['message'];?></label> </p>
        <p>
          <label>Name</label>
          <input class="w3-input" type="text" name="user_name" style="width:90%" required>
        </p>
        <p>
        <p>
          <label>E-mail</label>
          <input class="w3-input" type="email" name="user_email" style="width:90%" required>
        </p>
        <p>
          <label>Password</label>
          <input class="w3-input" type="password" name="user_pass" style="width:90%" required>
        </p>

        <p>
        <input id="milk" class="w3-check" type="checkbox" checked="checked">
        <label>Stay logged in</label></p>

        <p>
        <a href="login.php" class="w3-button w3-section w3-teal w3-ripple"> Login </a>
        <button type="submit" class="w3-button w3-section w3-teal w3-ripple"> Register </button></p>

  </form>

</div>

</body>
</html>
