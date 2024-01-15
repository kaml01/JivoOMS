<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
?>
<?php include_once('layouts/header.php'); ?>
<body style="background: #182667;">
<div class="login-page" style="margin:0px 100px ">
    <div class="text-center">
       <img style="width:200px;height:100px;" src="libs/images/order.png">
       <h1 style="color: #182667;font-weight:600">Login</h1>
       <h4 style="font-weight:600">Jivo Orders</h4>
     </div>
     <?php echo display_msg($msg); ?>
      <form method="post" action="auth.php" class="clearfix">
        <div class="form-group">
              <label for="username" class="control-label">Username</label>
              <input type="name" class="form-control" name="username" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="Password" class="control-label">Password</label>
            <input type="password" name= "password" class="form-control" placeholder="Password">
        </div>
        <div class="form-group" style="text-align: center;">
                <button type="submit" class="btn btn-danger">Login</button>
        </div>
    </form>
</div>
</body>

<?php include_once('layouts/footer.php'); ?>
