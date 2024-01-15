<?php
  require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id)
{
  global $db;
  $id = (int)$id; 
    if(tableExists($table)){
          $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
          if($result = $db->fetch_assoc($sql))
            return $result;
          else
            return null;
     }
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  global $db;
  if(tableExists($table))
   {
    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE id=". $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/

function count_by_id($table){
  global $db;
  if(tableExists($table))
  {
    $sql    = "SELECT COUNT(id) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
     return($db->fetch_assoc($result));
  }
}
function count_by_Salesid($table){
  global $db;
  if(tableExists($table))
  {
    $sql    = "SELECT COUNT(SalesId) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
     return($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['password'] ){
        return $user['id'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username='', $password='') {
     global $db;
     $username = $db->escape($username);
     $password = $db->escape($password);
     $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
     $result = $db->query($sql);
     if($db->num_rows($result)){
       $user = $db->fetch_assoc($result);
       $password_request = sha1($password);
       if($password_request === $user['password'] ){
         return $user;
       }
     }
    return false;
   }


  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,u.email,";
      $sql .="g.group_name ";
      $sql .="FROM users u ";
      $sql .="LEFT JOIN user_groups g ";
      $sql .="ON g.group_level=u.user_level ORDER BY u.name ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

  function find_all_customer(){
    global $db;
    $results = array();
    $sql = "SELECT C.CustomerId,C.CustomerName,C.Address,C.State,C.City,";
    $sql .="g.group_name ";
    $sql .="FROM Customer C ";
    $sql .="LEFT JOIN user_groups g ";
    $sql .="ON g.group_level=C.CustomerId ORDER BY C.CustomerName ASC";
    $result = find_by_sql($sql);
    return $result;
}
 function updateLastLogIn($user_id)
	{
		global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
	}

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     //if user not login
     if (!$session->isUserLoggedIn(true)):
            $session->msg('d','Please login...');
            redirect('index.php', false);
      //if Group status Deactive
     elseif($login_level['group_status'] === '0'):
           $session->msg('d','This level user has been band!');
           redirect('home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
     elseif($current_user['user_level'] <= (int)$require_level):
              return true;
      else:
            $session->msg("d", "Sorry! you dont have permission to view the page.");
            redirect('home.php', false);
        endif;

     }
   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
  function join_product_table(){
     global $db;
    $sql  =" SELECT p.id,p.name,p.Pcs,p.TAX,p.deleted,p.date,p.Variety";
    $sql  .=" FROM tbl_products p";
    $sql  .=" ORDER BY p.id ASC";
    return find_by_sql($sql);

   }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT name FROM tbl_products WHERE name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM tbl_products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .= " LIMIT 1";
    return find_by_sql($sql);
  }
  /*--------------------------------------------------------------*/
  /* Function for Finding last insertId from SaleTable
  /*--------------------------------------------------------------*/
   function GetSaleId(){
    global $db;
    $sql = "SELECT LAST_INSERT_ID() id";
    $result = $db->query($sql);

    if ($result->num_rows >= 0) {
      // Fetch the result
      $row = $result->fetch_assoc();
      $lastInsertedId = $row['id'];
      send_mail($lastInsertedId);
      //SaveSaleWithUserLoginId($lastInsertedId);
      return $lastInsertedId;
  } else {
      return null;
  }
  
}

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE tbl_products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }

  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
 function find_recent_product_added($limit){
   global $db;
   $sql   = " SELECT p.id,p.name,p.Pcs,p.deleted,p.Variety";
   $sql  .= " FROM tbl_products p";
   $sql  .= " ORDER BY p.id DESC LIMIT ".$db->escape((int)$limit);
   return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for Find Highest saleing Product
 /*--------------------------------------------------------------*/
 function find_higest_saleing_product($limit){
   global $db;
   $sql  = "SELECT p.name, COUNT(ps.ProductId) AS totalSold, SUM(ps.Qty) AS totalQty";
   $sql .= " FROM productsold ps";
   $sql .= " LEFT JOIN tbl_products p ON p.id = ps.ProductId ";
   $sql .= " GROUP BY ps.ProductId";
   $sql .= " ORDER BY SUM(ps.Qty) DESC LIMIT ".$db->escape((int)$limit);
   return $db->query($sql);
 }
 /*--------------------------------------------------------------*/
 /* Function for find all sales(only use in My worklist)
 /*--------------------------------------------------------------*/
 function find_all_sale(){
  global $db;
  $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name,s.Status";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN tbl_products p ON s.product_id = p.id";
  $sql .= " ORDER BY s.date DESC";
  return find_by_sql($sql);
}
 /*--------------------------------------------------------------*/
 /* Function for find all sales
 /*--------------------------------------------------------------*/
 function find_all_sale1(){
  global $db;
 
  $sql  = "SELECT s.SalesId,s.PersonId,p.name,ps.Price,ps.Qty,s.Date";
  $sql .= " FROM salesreport s ";
  $sql .= " JOIN productsold ps ON s.SalesId = ps.salesId ";
  $sql .= " JOIN tbl_products p ON p.id = ps.ProductId";
  $sql .= " ORDER BY s.SalesId DESC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
 /* Function for find all sales based on UserLoginId
 /*--------------------------------------------------------------*/
 function find_all_sale2() {
  global $db;
  $userId = $_SESSION['user_id'];

  $subquery = "
      (SELECT status_to
      FROM sales_audit sa
      WHERE sa.salesid = s.SalesId
      AND status_to IS NOT NULL
      ORDER BY dttm DESC 
      LIMIT 1) AS status_to
  ";

  $sql = "
      SELECT s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate,PI, {$subquery} 
      FROM salesreport s
      JOIN users u ON u.id = s.PersonId
      WHERE s.PersonID = '{$userId}'
      ORDER BY s.SalesId DESC
  ";

  return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
 /* Function for find all sales based on UserLoginId
 /*--------------------------------------------------------------*/
 function find_all_saleforAdmin() {
  global $db;

  $subquery = "
      (SELECT status_to
      FROM sales_audit sa
      WHERE sa.salesid = s.SalesId
      AND status_to IS NOT NULL
      ORDER BY dttm DESC 
      LIMIT 1) AS status_to
  ";

  $sql = "
      SELECT s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate, {$subquery} 
      FROM salesreport s
      JOIN users u ON u.id = s.PersonId
      ORDER BY s.SalesId DESC
  ";

  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
 /* Function for find all sales for punch button
 /*--------------------------------------------------------------*/
function find_all_sale3() {
  global $db;
  
  $sql  = "SELECT s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate";
  $sql .= " FROM salesreport s ";
  $sql .= " JOIN users u ON u.id = s.PersonId";
  $sql .= " ORDER BY s.SalesId DESC";
  return find_by_sql($sql);
}
 /*--------------------------------------------------------------*/
 /* Function for Display Recent sale
 /*--------------------------------------------------------------*/
function find_recent_sale_added($limit){
  global $db;
  $sql  = "SELECT s.id,s.qty,s.price,s.date,p.name";
  $sql .= " FROM sales s";
  $sql .= " LEFT JOIN tbl_products p ON s.product_id = p.id";
  $sql .= " ORDER BY s.date DESC LIMIT ".$db->escape((int)$limit);
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate sales report by two dates
/*--------------------------------------------------------------*/
function find_sale_by_dates($start_date,$end_date){
  global $db;
  $start_date  = date("Y-m-d", strtotime($start_date));
  $end_date    = date("Y-m-d", strtotime($end_date));
  $sql  = "SELECT s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate";
  $sql .= " FROM salesreport s ";
  $sql .= " JOIN users u ON u.id = s.PersonId";
  $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
  return $db->query($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate Daily sales report
/*--------------------------------------------------------------*/
function  dailySales($year,$month){ 
  global $db;
  $sql  = "SELECT  s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate, ";
  $sql .= " DATE_FORMAT(s.Date, '%Y-%m-%e') AS Date,u.name";
  // $sql .= "SUM(p.buy_price * s.qty) AS total_saleing_price";
  $sql .= " FROM salesreport s";
  $sql .= " JOIN users u ON u.id = s.PersonId";
  $sql .= " WHERE DATE_FORMAT(s.Date, '%Y-%m' ) = '{$year}-{$month}'";
  $sql .= " GROUP BY DATE_FORMAT( s.Date,  '%e' ),s.SalesId";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for Generate Monthly sales report
/*--------------------------------------------------------------*/
function  monthlySales($year){
  global $db;
  $sql  = "SELECT  s.SalesId, u.name, s.Date,s.CardName,s.DeliveryDate, ";
  $sql .= " DATE_FORMAT(s.Date, '%Y-%m-%e') AS Date,u.name,";
  //$sql .= "SUM(p.buy_price * s.qty) AS total_saleing_price";
  $sql .= " FROM salesreport s";
  $sql .= " JOIN users u ON u.id = s.PersonId";
  $sql .= " WHERE DATE_FORMAT(s.Date, '%Y' ) = '{$year}'";
  $sql .= " GROUP BY DATE_FORMAT( s.Date,  '%c' ),s.SalesId";
  $sql .= " ORDER BY date_format(s.Date, '%c' ) ASC";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
  /* Function for Update Status based on OrderId
  /*--------------------------------------------------------------*/
  function status_update($value,$id){  
    global $db;
    $sql = "UPDATE sales SET Status='{$value}' WHERE id ='{$id}'";
    return $db->query($sql);
    
}  
function SaveSaleWithUserLoginId($lastInsertedId){
  global $db;
  $userId=$_SESSION['user_id'] ;
  echo "$userId";
  $sql = "UPDATE sales SET User_login='{$userId}' WHERE id ='{lastInsertedId}'";
  return $db->query($sql);
}
// Add this function to your includes/load.php or a suitable location

function getStatusTo($salesId)
{
    global $db; // Assuming $db is your database connection object
    // Sanitize input
    $salesId = (int)$salesId;

    // Query to get the status-to value
    $query = "SELECT status_to FROM sales_audit WHERE salesid = {$salesId} ORDER BY dttm DESC LIMIT 1";
    $result = $db->query($query);

    if ($result) {
        $row = $db->fetch_assoc($result);
        return ($row) ? (int)$row['status_to'] : 0;
    } else {
        // Handle query error if necessary
        return 0;
    }
}

function all_State(){
  global $db;
  $sql  = "SELECT distinct(State)";
  $sql .= " FROM tbl_party WHERE State IS NOT NULL and State!=''";
  return find_by_sql($sql);
}
function MainGroup(){
  global $db;
  $sql  = "SELECT DISTINCT MainGroup";
  $sql .= " FROM tbl_party WHERE MainGroup IS NOT NULL and MainGroup!=''";
  return find_by_sql($sql);
}

function party(){
  global $db;
  $userId = $_SESSION['user_id'];

  $sq1 = "SELECT State, MainGroup from users where id='{$userId}'";
  $res = $db->query($sq1);
  $row1 = $db->fetch_assoc($res);
  $states = $row1['State'];
  $maingroups = $row1['MainGroup'];

  $states = preg_replace('/([^,]+)/', "'$1'", $states);
  $maingroups = preg_replace('/([^,]+)/', "'$1'", $maingroups);

  $sql  = "SELECT p.CardCode,p.CardName,p.Address,p.State";
  $sql .= " FROM tbl_party p ";
  $sql .= " Right JOIN users u ";
  $sql .= " ON p.State in ({$states}) Where u.id='{$userId}' and p.MainGroup in ({$maingroups})";

  return find_by_sql($sql);
}

function partyforadmin(){
  global $db;

  $sql  = "SELECT pr.CardCode,pr.CardName,pr.Address,";
  $sql .= " FROM tbl_party pr ";
  
  return find_by_sql($sql);
}
function find_products_by_sales_id($salesId) {
  global $db;

  $sql = "SELECT ps.SalesId, p.name, ps.Qty, ps.Price";
  $sql .= " FROM productsold ps";
  $sql .= " JOIN tbl_products p ON p.id = ps.ProductId";
  $sql .= " WHERE ps.SalesId = '" . $db->escape($salesId) . "'";
 
  return find_by_sql($sql);
}

function TotalSaleDetails(){
  global $db;
  $sql="SELECT status_to, COUNT(*)as count FROM (
    SELECT salesid, case when status_to BETWEEN 1 AND 6 then 'Pending' when status_to=7 then 'Dispatch'
                         when status_to>=8 then 'Rejected' END status_to
    FROM sales_audit s
    WHERE dttm=(SELECT MAX(dttm) FROM sales_audit s1 WHERE s.salesid=s1.salesid)) X
    GROUP BY status_to
    ";
    return find_by_sql($sql);
}

function TotalValueOfOrder(){
  global $db;
  $sql="SELECT case when status_to BETWEEN 1 AND 6 then 'Pending' when status_to=7 then 'Dispatch'
  when status_to>=8 then 'Rejected' END status_to, 
  SUM(p.qty*p.price) as value
  FROM salesreport s, productsold p, sales_audit sa
  WHERE s.salesid=p.salesid AND
  sa.salesid=p.salesid AND
  sa.dttm=(SELECT MAX(dttm) FROM sales_audit sa1 WHERE sa1.salesid=sa.salesid) 
  GROUP BY 1";
  return find_by_sql($sql);
}
function TotalSaleDetailsOfUser(){
  global $db;
  $userId = $_SESSION['user_id'];
  $sql="SELECT status_to, COUNT(*)as count FROM (
    SELECT s.salesid, case when status_to BETWEEN 1 AND 6 then 'Pending' when status_to=7 then 'Dispatch'
                          when status_to>=8 then 'Rejected' END status_to
    FROM sales_audit s, salesreport sr
    WHERE dttm=(SELECT MAX(dttm) FROM sales_audit s1 WHERE s.salesid=s1.salesid) AND
    sr.salesid=s.salesid AND sr.personid='{$userId}') X
    GROUP BY status_to";
    return find_by_sql($sql);
}

function TotalValueOfOrderUser(){
  global $db;
  $userId = $_SESSION['user_id'];
  $sql="SELECT case when status_to BETWEEN 1 AND 6 then 'Pending' when status_to=7 then 'Dispatch'
  when status_to>=8 then 'Rejected' END status_to, 
  SUM(p.qty*p.price) as value
  FROM salesreport s, productsold p, sales_audit sa
  WHERE s.salesid=p.salesid AND
  sa.salesid=p.salesid AND
  s.personid='{$userId}' AND
  sa.dttm=(SELECT MAX(dttm) FROM sales_audit sa1 WHERE sa1.salesid=sa.salesid) 
  GROUP BY 1";
  return find_by_sql($sql);
}