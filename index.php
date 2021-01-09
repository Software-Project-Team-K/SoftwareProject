<?php 
            require 'assets/classes.php';
           
            session_start();
            if(!isset($_SESSION['user']))header("location: hello/");
            $connect =new connection ;
            $con = $connect->conn;   
            $userloggedin= $_SESSION['user']->get_id(); 
            $userloggedin_query=mysqli_query($con, "SELECT * FROM users WHERE id='$userloggedin' ");
             $user= mysqli_fetch_array($userloggedin_query);
           
            echo '
                 <!DOCTYPE html>
                    <html>
                        <head>
                                <link rel="stylesheet" type="text/css" href="styling/style.css">
                                <link rel="stylesheet" href="main.css">
                                <link rel="stylesheet" type=text/css href="styling/bootstrap.css">
                                <meta charset="utf-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Chatverse | Newsfeed</title>
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                                <script src="assets/js/bootstrap.js"></script>
                                <script src="assets/js/bootbox.min.js"></script>
                                <link rel="icon" href="assets/img/icn_logo.png">
            
                                 <!--Navigation Bar-->
                                 <div id="nav">
                                    <a href=""><img src="assets/img/icn_logo.png" style="width: 30px;  margin: 5px 20px;"></a>
                                    <input type="text" style="width:20%; position: relative; left:10px; bottom:15px; border-radius:10px;">
                                        <div id="navbuttons">
                                                <button><a href='.$_SESSION["user"]->get_id().'><img src="'.$_SESSION["user"]->get_id()."/".$_SESSION["user"]->get_profile_pic().'"></a></button>
                                                <button><a href="messages" ><img src="assets/img/icn_msg.png"></a></button>
                                                <button><img src="assets/img/icn_notification.png"></button>
                                                <button id="arrow"><img src="assets/img/icn_settings.png"></button>
                                                <ul id="menu">
                                                        <li><a href="settings">Account Settings</li>
                                                        <li><a href="assets/operation/logout.php">Logout</a></li>
                                                </ul>
                                            </div> 
                                        </div>
                                <div style="height:40px; background-color: white;"></div>
                                <script>
                                var arrow = document.getElementById("arrow");
                                var menu = document.getElementById("menu");  
                                arrow.onclick = function() {
                                    if(menu.style.display == "block")menu.style.display = "none"
                                    else menu.style.display = "block";}
                                 </script>
                            </head>
                        <body>'
?>
<?php 
    if (isset($_POST['post'])) 
    {
        //echo "Hello from post";
        //die();
        $post= new Post($con,$userloggedin);
        //it takes body and user to fro now we make it none just in the start 
        $post->submitpost($_POST['post_text'],'none');
        header("Location:index.php");
    }
    //create group	
    if (isset($_POST['create_group'])) 
    {
         
            $error_array=array();
            $group_name=strip_tags($_POST['group_name']);//remove any html tags 
            $group_name=str_replace(' ','',$group_name);//replace any spce in the name with no space
            $group_name=ucfirst(strtolower($group_name));//only the first letter will be uppercase    
               
            
            $sql = "INSERT INTO groups VALUES('','$group_name','','$userloggedin',',')";
            $query=mysqli_query($con,$sql);
            header("Location:index.php");
        }
    
?>
 
<!-- home -->
            <!--<div style="width:60%; margin: 20px 20%;  border: #96979e 1px solid; display:inline-block; border-radius: 5px;">-->
    <div class="wrapper">
        <div class="user_details column">
            <a href="profile.php"><img src="<?php echo $userloggedin."/".$user['profile_pic'];?>" >
            </a>
            <div class="user_details_left_right">
                <a href="<?php echo $userloggedin; ?>">	
                    <?php
                        echo $user['f_name']." ".$user['l_name'] ."<br>"; 
                        ?>
                </a>
            </div>
        </div>
        <div class="main_column column">
            <div id="postDiv" style="margin-bottom: 60px;">
                <form action="index.php" class="post_form" style="width: 80%;height: 60px;border-radius: 5px;margin-right: 5px;border:1px solid rgb(224, 219, 219);font-size: 13px;" method="POST">
                    <textarea name="post_text" id ="post_text" placeholder="what's in your mind?"></textarea>
                    <br>
                    <input type="submit" name="post" id="post_button" value="post">
                    <hr>
                </form>          
            </div>
            <div class="posts_area"> </div>
		    <img id="loading" src="assets/img/loading.gif">

        </div>
        <div class="user_details column">
            <!--group part-->
            <form method="POST" >
                <input type="text" name="group_name" placeholder="group name" required>
                <input type="submit" href="" name="create_group" value="create group!" method="POST">
                <?
                    $show_group=mysqli_query($con,"SELECT * FROM groups WHERE admin='$userloggedin'");
                    $group_row=mysqli_fetch_array($show_group);
                    $name=$group_row['$group_name'];

                ?> 
            </from>
            <hr>
            <h4>Groups</h4>

            <div class="group_list">
                <?php 
                $query = mysqli_query($con, "SELECT * FROM groups ORDER BY id asc ");

                foreach ($query as $row) {
                    $group_name = $row['group_name'];
                    $group_id= "groups?group_id=".$row ['id'];
                    $element = '<a href="'. $group_id. '">'.$group_name .'</a>';
                    echo "<div style='padding: 2px'>";
                    echo $element;
                    echo "</div>";
                }
                ?>
            </div>
            <h4>Popular</h4>

            <div class="trends">
                <?php 
                $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

                foreach ($query as $row) {
                    
                    $word = $row['title'];
                    $word_dot = strlen($word) >= 14 ? "..." : "";

                    $trimmed_word = str_split($word, 14);
                    $trimmed_word = $trimmed_word[0];

                    echo "<div style'padding: 1px'>";
                    echo $trimmed_word . $word_dot;
                    echo "<br></div><br>";
                }
                ?>
            </div>
    </div>

	<script>
		var userloggedin ='<?php echo $userloggedin; ?>';

		$(document).ready(function(){

			$('#loading').show();
			// ajax for loading posts 
			$.ajax({
				url:"assets/operation/ajax.php",
				type:"POST",
				data:"page=1&userloggedin=" + userloggedin,
				cache:false,

				success:function(data)
				{
					$('#loading').hide(); //dont show loading sign again 
					$('.posts_area').html(data);
				}
			});  //end of ajax
		$(window).scroll(function(){
		var height=$('.posts_area').height(); //div containing posts
		var scroll_top=$(this).scrollTop();
		var page=$('.posts_area').find('.nextpage').val();//   created int post class
		var nomoreposts=$('.posts_area').find('.nomoreposts').val();
		//alert("hello");

		// function to scroll to the bottom //rl moshkla msh radi yd5ol  el if aslnn
		if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && nomoreposts=='false')
		{
			
			$('#loading').show();
			alert("hello");
			var ajaxReq = $.ajax({
			url:"assets/operation/ajax.php",
			type:"POST",
			data:"page=" + page + "&userloggedin=" + userloggedin,
			cache:false,

				success:function(response)
				{
					$('.posts_area').find('.nextpage').remove();
					$('.posts_area').find('.nomoreposts').remove();

					$('#loading').hide();
					$('.posts_area').append(response);//add new posts to the existing posts
				}
			});  

		}//end if

		return false;


		});//end $(window).scroll(function(){*/



		});

	</script>





<!--</div>-->
                                                
           















<div>

    </body>  
</html>
            