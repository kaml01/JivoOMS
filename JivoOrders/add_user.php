<?php
  $page_title = 'Add User';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $groups = find_all('user_groups');
  $State=all_State(); 
  $MainGroup=MainGroup(); 
?>
<?php
  if(isset($_POST['add_user'])){
  
   $req_fields = array('full-name','username','password','level','MainGroup','State','email' );
   validate_fields($req_fields);

   if(empty($errors)){
      $name   = remove_junk($db->escape($_POST['full-name']));
       $username   = remove_junk($db->escape($_POST['username']));
       $password   = remove_junk($db->escape($_POST['password']));
       $user_level = (int)$db->escape($_POST['level']);
       $password = sha1($password);   
      //  $MainGroup = remove_junk($db->escape($_POST['MainGroup']));
      //  $State = remove_junk($db->escape($_POST['State']));
      $MainGroup = isset($_POST['MainGroup']) ? implode(',', $_POST['MainGroup']) : '';
      $State = isset($_POST['State']) ? implode(',', $_POST['State']) : '';

       $email   = remove_junk($db->escape($_POST['email']));
        $query = "INSERT INTO users (";
        $query .="name,username,password,user_level,MainGroup,State,email,Status";
        $query .=") VALUES (";
        $query .=" '{$name}', '{$username}', '{$password}', '{$user_level}', '{$MainGroup}', '{$State}', '{$email}','1'";
        $query .=")";
        if($db->query($query)){
          //sucess
          $session->msg('s',"User account has been creted! ");
          redirect('add_user.php', false);
        } else {
          //failed
          $session->msg('d',' Sorry failed to create account!');
          redirect('add_user.php', false);
        }
   } else {
     $session->msg("d", $errors);
      redirect('add_user.php',false);
   }
 }
?>
<?php include_once('layouts/header.php'); ?>
  <?php echo display_msg($msg); ?>
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New User</span>
       </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
          <form method="post" action="add_user.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input required type="text" class="form-control" name="full-name" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input required type="text" class="form-control" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input  required type="password" class="form-control" name ="password"  placeholder="Password">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input required type="email" class="form-control" name="email" placeholder="your@jivo.in">
            </div>
            <div class="form-group">
              <label for="MainGroup">Main Group</label>
              <select required class="form-control" name="MainGroup[]" multiple>
                  <?php foreach ($MainGroup as $Group): ?>
                      <option value="<?php echo $Group['MainGroup']; ?>"><?php echo ucwords($Group['MainGroup']); ?></option>
                  <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
                <label for="State">State</label>
                <select required class="form-control" name="State[]" multiple>
                    <?php
                    // Define an associative array for state codes and their full names
                    $stateMapping = array(
                        'UP' => 'Uttar Pradesh',
                        'DL' => 'Delhi',
                        'PB' => 'Punjab',
                        'MP' => 'Madhya Pradesh',
                        'MH' => 'Maharashtra',
                        'GJ' => 'Gujarat',
                        'HR' => 'Haryana',
                        'UK' => 'Uttarakhand',
                        'WB' => 'West Bengal',
                        'HP' => 'Himachal Pradesh',
                        'JK' => 'Jammu and Kashmir',
                        'KT' => 'Karnataka',
                        'SK' => 'Sikkim',
                        'AS' => 'Assam',
                        'RJ' => 'Rajasthan',
                        'TE' => 'Telangana',
                        'AP' => 'Andhra Pradesh',
                        'OR' => 'Odisha',
                        'KE' => 'Kerala',
                        'A&N' => 'Andaman and Nicobar Islands',
                        'CH' => 'Chandigarh',
                        'GO' => 'Goa',
                        'AD' => 'Arunachal Pradesh',
                        'BH' => 'Bihar',
                        'ME' => 'Meghalaya',
                        'TN' => 'Tamil Nadu',
                        'PY' => 'Puducherry',
                        'MZ' => 'Mizoram',
                        'AR' => 'Arunachal Pradesh',
                        'MN' => 'Manipur'
                        // Add more states as needed
                    );
                  
                    foreach ($State as $state):
                        // Use the state code to get the full name from the array
                        $fullName = isset($stateMapping[$state['State']]) ? $stateMapping[$state['State']] : $state['State'];
                    ?>
                        <option value="<?php echo $state['State']; ?>"><?php echo ucwords($fullName); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
              <label for="level">User Role</label>
                <select required class="form-control" name="level">
                  <?php foreach ($groups as $group ):?>
                   <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
                </select>
            </div>
            <div class="form-group clearfix">
              <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </div>
        </form>
        </div>

      </div>

    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
