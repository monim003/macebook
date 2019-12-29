<?php
session_start();
include 'connect.php';


//friend related functions
function get_users()
{
  global $connect;
  $query = "SELECT * FROM users WHERE user_id != '".$_SESSION['user_id']."' AND user_id NOT IN(SELECT friends_with FROM friends WHERE user_id = '".$_SESSION['user_id']."') AND user_id NOT IN(SELECT friend_request_to FROM friend_list WHERE friend_request_from = '".$_SESSION['user_id']."')";
  $result = mysqli_query($connect, $query);

  while($row = mysqli_fetch_assoc($result))
  {
    echo '<div class="w3-container">
      <img src="https://www.w3schools.com/w3images/avatar6.png" alt="Avatar" style="width:50%"><br>
      <a style="text-decoration: none;" href="user_profile.php?id='.$row['user_id'].'">'.$row['user_name'].'</a>
      <div id="request_sent'.$row['user_id'].'" class="w3-row w3-opacity">
        <div class="w3-half">
          <button id="add_friend" data-friend_user_id="'.$row['user_id'].'" class="w3-button w3-block w3-green w3-section" title="Accept"><i class="fa fa-check"></i></button>
        </div>
        <div class="w3-half">
          <button class="w3-button w3-block w3-red w3-section" title="Decline"><i class="fa fa-remove"></i></button>
        </div>
      </div>
    </div>';
  }
}

function add_friend()
{
  global $connect;
  $status = 0;
  $from = $_SESSION['user_id'];
  $to = $_POST['friend_user_id'];
  $query = "INSERT INTO friend_list (friend_request_from,friend_request_to,status) VALUES ($from,$to,$status)";

  mysqli_query($connect,$query);

  echo "Friend added !!";
}

function get_friend_requests()
{
  global $connect;

  $query = "SELECT * FROM users WHERE user_id IN(SELECT friend_request_from FROM friend_list WHERE friend_request_to = '".$_SESSION['user_id']."' AND status = 0)";

  $result = mysqli_query($connect, $query);
  if($result)
  {
    while($row = mysqli_fetch_assoc($result))
    {
      echo '<div class="w3-container">
        <img src="https://www.w3schools.com/w3images/avatar6.png" alt="Avatar" style="width:50%"><br>
        <a style="text-decoration: none;" href="user_profile.php?id='.$row['user_id'].'">'.$row['user_name'].'</a>
        <div id="request_accepted'.$row['user_id'].'" class="w3-row w3-opacity">
          <div class="w3-half">
            <button id="accept_request" data-friend_user_id="'.$row['user_id'].'" class="w3-button w3-block w3-green w3-section" title="Accept"><i class="fa fa-check"></i></button>
          </div>
          <div class="w3-half">
            <button class="w3-button w3-block w3-red w3-section" title="Decline"><i class="fa fa-remove"></i></button>
          </div>
        </div>
      </div>';
    }
  }
}

function accept_request()
{
  global $connect;
  $query = "UPDATE friend_list SET status = 1
  WHERE friend_request_from = '".$_POST['friend_user_id']."' AND friend_request_to = '".$_SESSION['user_id']."'";
  mysqli_query($connect,$query);

  $to = $_SESSION['user_id'];
  $from = $_POST['friend_user_id'];

  $query = "INSERT INTO friends(user_id,friends_with) VALUES($to,$from)";
  mysqli_query($connect,$query);

  $query = "INSERT INTO friends(user_id,friends_with) VALUES($from,$to)";
  mysqli_query($connect,$query);
  echo "Friend request accepted !! ";
}

function get_friends()
{
  global $connect;
  $query = "SELECT * FROM users WHERE user_id IN(SELECT friends_with FROM friends WHERE user_id = '".$_SESSION['user_id']."')";
  $result = mysqli_query($connect, $query);

  while($row = mysqli_fetch_assoc($result))
  {
    echo '<div class="w3-container">
      <img src="https://www.w3schools.com/w3images/avatar6.png" alt="Avatar" style="width:50%"><br>
      <span>'.$row['user_name'].'</span>
      <div class="w3-row w3-opacity">
        <a href="user_profile.php?id='.$row['user_id'].'" class="w3-button w3-block w3-green w3-section">View Profile</a>
      </div>
    </div>';
  }
}

//post related functions
function get_username($user_id)
{
  global $connect;
  $query = "SELECT * FROM users WHERE user_id = '$user_id'";
  $result = mysqli_query($connect, $query);
  $user_name = '';
  while($row = mysqli_fetch_assoc($result))
  {
    $user_name = $row['user_name'];
  }
  return $user_name;
}
function get_user_id($post_id)
{
  global $connect;
  $query = "SELECT user_id FROM posts WHERE post_id = '$post_id'";
  $result = mysqli_query($connect,$query);
  $id = 1;

  while($row = mysqli_fetch_assoc($result))
  {
    $id = $row['user_id'];
  }
  return $id;
}
function count_likes()
{
  global $connect;
  $post_id = $_POST['post_id'];
  $query = "SELECT * FROM likes WHERE post_id = '$post_id' AND like_status = 1";
  $result = mysqli_query($connect, $query);
  echo mysqli_num_rows($result)." likes";
}

function like_status()
{
  global $connect;
  $post_id = $_POST['post_id'];
  $query = "SELECT * FROM likes WHERE user_id = '".$_SESSION['user_id']."' AND post_id = '".$post_id."'";
  $result = mysqli_query($connect,$query);
  if(mysqli_num_rows($result)>0)
  {
    echo '<button type="button" id="unlike" data-post_id="'.$post_id.'" data-user_id="'.get_user_id($post_id).'" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fa fa-thumbs-down"></i>  Unlike</button>';
  }
  else
  {
    echo '<button type="button" id="like" data-post_id="'.$post_id.'" data-user_id="'.get_user_id($post_id).'" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fa fa-thumbs-up"></i>  Like</button>';
  }
}

function get_posts()
{
  global $connect;
  $query = "SELECT * FROM posts WHERE user_id IN(SELECT friends_with FROM friends WHERE user_id = '".$_SESSION['user_id']."') OR user_id = '".$_SESSION['user_id']."' ORDER BY post_id DESC";

  $result = mysqli_query($connect, $query);

  while($row = mysqli_fetch_assoc($result))
  {
    echo '<div class="w3-container w3-card w3-white w3-round w3-margin"><br>
      <img src="https://www.w3schools.com/w3images/avatar5.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
      <span class="w3-right w3-opacity">'.date('M-d h:i a',strtotime($row['time'])).'</span>
      <h4>'.get_username($row['user_id']).'</h4><br>
      <hr class="w3-clear">
      <p>'.$row['user_post'].'</p>
      <p id="count_likes" data-post_id="'.$row['post_id'].'"><p id="like_status'.$row['post_id'].'"></p></p>
      <span id="btn_type'.$row['post_id'].'"></span>
      <button type="button" id="comment" data-post_id="'.$row['post_id'].'" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i>  Comment</button>
      <div id="comment_area'.$row['post_id'].'" data-post_id="'.$row['post_id'].'" class="w3-container comment_area">
        <p id="update_comments" data-post_id="'.$row['post_id'].'"><p id="comment_copy_here'.$row['post_id'].'" style="overflow:auto; height:7vw; width:43vw; word-break: break-all;"></p></p>
        <textarea id="ucomment'.$row['post_id'].'" style="height:4vw; width: 43vw;resize:none;"></textarea>
        <br>
        <br>
        <button id="write_comment" data-user_id="'.$row['user_id'].'" data-post_id="'.$row['post_id'].'" type="button" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i>  OK</button>
      </div>

    </div>';
  }
}

function get_specific_user_posts($user_id)
{
  global $connect;
  $query = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY post_id DESC";

  $result = mysqli_query($connect, $query);

  while($row = mysqli_fetch_assoc($result))
  {
    echo '<div id="p_id'.$row['post_id'].'" class="w3-container w3-card w3-white w3-round w3-margin"><br>
      <img src="https://www.w3schools.com/w3images/avatar5.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
      <span class="w3-right w3-opacity">'.date('M-d h:i a',strtotime($row['time'])).'</span>
      <h4>'.get_username($row['user_id']).'</h4><br>
      <hr class="w3-clear">
      <p>'.$row['user_post'].'</p>
      <p id="count_likes" data-post_id="'.$row['post_id'].'"><p id="like_status'.$row['post_id'].'"></p></p>
      <span id="btn_type'.$row['post_id'].'"></span>
      <button type="button" id="comment" data-post_id="'.$row['post_id'].'" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i>  Comment</button>
      <div id="comment_area'.$row['post_id'].'" data-post_id="'.$row['post_id'].'" class="w3-container comment_area">
        <p id="update_comments" data-post_id="'.$row['post_id'].'"><p id="comment_copy_here'.$row['post_id'].'" style="overflow:auto; height:7vw; width:43vw; word-break: break-all;"></p></p>
        <textarea id="ucomment'.$row['post_id'].'" style="height:4vw; width: 43vw;resize:none;"></textarea>
        <br>
        <br>
        <button id="write_comment" data-post_id="'.$row['post_id'].'" type="button" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i>  OK</button>
      </div>

    </div>';
  }
}

function get_comments()
{
  global $connect;
  $output = '';
  $post_id = $_POST['post_id'];
  $query = "SELECT * FROM comments WHERE post_id = '$post_id'";
  $result = mysqli_query($connect, $query);
  while($row = mysqli_fetch_assoc($result))
  {
    $output .= '<strong style="color:blue;">'.get_username($row['user_id']).'</strong>'.' - '.$row['user_comment'].'<span class="w3-right w3-opacity">'.date('M-d h:i a',strtotime($row['time'])).'</span>'.'<br><br>';
  }
  echo $output;
}

function like_post()
{
  global $connect;
  $post_id = $_POST['post_id'];
  $user_id = $_SESSION['user_id'];
  $like_staus = 1;
  $query = "INSERT INTO likes(post_id, user_id, like_status) VALUES($post_id, $user_id, $like_staus)";
  mysqli_query($connect, $query);

  $user_id_from = $_SESSION['user_id'];
  $user_id_to = $_POST['user_id'];
  $notification_about = "likes on your post";
  $status = 0;
  $query = "INSERT INTO notifications(notification_from,notification_to,post_id,notification_about,status) VALUES('$user_id_from','$user_id_to','$post_id','$notification_about','$status')";
  mysqli_query($connect,$query);
}

function unlike_post()
{
  global $connect;
  $post_id = $_POST['post_id'];
  $user_id = $_SESSION['user_id'];
  $like_staus = 0;
  $query = "DELETE FROM likes WHERE post_id = '".$post_id."' AND user_id = '".$user_id."'";
  mysqli_query($connect, $query);

  $user_id_from = $_SESSION['user_id'];
  $user_id_to = $_POST['user_id'];
  $notification_about = "likes on your post";
  $status = 0;
  $query = "DELETE FROM notifications WHERE notification_from = '$user_id_from' AND notification_to = '$user_id_to' AND post_id = '$post_id' AND notification_about = '$notification_about'";
  mysqli_query($connect,$query);
}

function post_comment()
{
  global $connect;
  $post_id = $_POST['post_id'];
  $user_id = $_SESSION['user_id'];
  $user_comment = $_POST['user_comment'];
  $query = "INSERT INTO comments(post_id, user_id, user_comment) VALUES('$post_id', '$user_id', '$user_comment')";
  mysqli_query($connect, $query);

  $user_id_from = $_SESSION['user_id'];
  $user_id_to = $_POST['user_id'];
  $notification_about = "commented on your post";
  $status = 0;
  $query = "INSERT INTO notifications(notification_from,notification_to,post_id,notification_about,status) VALUES('$user_id_from','$user_id_to','$post_id','$notification_about','$status')";
  mysqli_query($connect,$query);
}

function update_status()
{
  global $connect;
  $post = $_POST['status'];
  $id = $_SESSION['user_id'];
  $query = "INSERT INTO posts(user_id,user_post) VALUES('$id', '$post')";
  mysqli_query($connect,$query);
}

//notification related functions

function get_notifications()
{
  global $connect;
  $query = "SELECT * FROM notifications WHERE notification_to = '".$_SESSION['user_id']."' ";
  $result = mysqli_query($connect, $query);
  $output = '';
  while($row = mysqli_fetch_assoc($result))
  {
    $output .= '<a href="user_profile.php?id='.$_SESSION['user_id'].'#p_id'.$row['post_id'].'" class="w3-bar-item w3-button">'.get_username($row['notification_from']).' '.$row['notification_about'].'</a>';
  }
  echo $output;
}

function count_new_notification()
{
  global $connect;
  $status = 0;
  $query = "SELECT * FROM notifications WHERE notification_to = '".$_SESSION['user_id']."' AND status = '$status'";
  $result = mysqli_query($connect,$query);

  echo mysqli_num_rows($result);
}

if(isset($_POST['request']))
{
  if($_POST['request'] == "add_friend")
  {
    add_friend();
  }
  if($_POST['request'] == "accept_friend")
  {
    accept_request();
  }
  if($_POST['request'] == "like_post")
  {
    like_post();
  }
  if($_POST['request'] == "unlike_post")
  {
    unlike_post();
  }
  if($_POST['request'] == "count_likes")
  {
    count_likes();
  }
  if($_POST['request'] == "update_status")
  {
    update_status();
  }
  if($_POST['request'] == "get_comments")
  {
    get_comments();
  }
  if($_POST['request'] == "post_comment")
  {
    post_comment();
  }
  if($_POST['request'] == "like_status")
  {
    like_status();
  }
  if($_POST['request'] == "get_notifications")
  {
    get_notifications();
  }
  if($_POST['request'] == "count_new_notification")
  {
    count_new_notification();
  }
}

?>
