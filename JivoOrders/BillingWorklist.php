<?php
$page_title = 'Billing All sale';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(3);

if (isset($_POST['update-pass'])) {
  if (isset($_POST['order_id'])) {
   
      $my_user = $_SESSION['user_id'];
      $sales_id = (int)$_POST['order_id'];
      $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'2')";
      $db->query($query);
    
      //echo "Success";
      $session->msg('s', "Sale punched. ");
      redirect('BillingWorklist.php', false);
    
    
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('BillingWorklist.php', false);
  }
} 
if (isset($_POST['CancelOrder'])) {
  if (isset($_POST['order_id'])) {
   
      $my_user = $_SESSION['user_id'];
      $sales_id = (int)$_POST['order_id'];
      $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'8')";
      $db->query($query);
    
      //echo "Success";
      $session->msg('s', "Order Cancel of OrderId $sales_id");
      redirect('BillingWorklist.php', false);
    
    
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('BillingWorklist.php', false);
  }
}
$salesreport = find_all_sale3();
?>
<?php
 
if (isset($_POST['uploadfile'])) {
  $req_fields = array('order_id', 'SAP_Id');
  validate_fields($req_fields);
  if (empty($errors)) {
    if (isset($_POST['order_id'])) { 
        if (isset($_POST['SAP_Id']) && !empty($_POST['SAP_Id'])) {
            $sap_id = $_POST['SAP_Id'];
            $uploadedFile = $_FILES['file']['tmp_name'];
            $sales_id = (int)$_POST['order_id'];
            $filename = $_FILES['file']['name'];
            $destinationFile = 'C:/xampp/htdocs/JivoOrders/uploadFile/' . $sales_id . '_' . $filename;

            if (move_uploaded_file($uploadedFile, $destinationFile)) {
                $my_user = $_SESSION['user_id'];
              
                $PI = $sales_id . '_' . $_FILES['file']['name']; 

                $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'3')";
                $db->query($query);

                $query2 = "UPDATE salesreport set SAP_Id='{$sap_id}',PI='{$PI}' where SalesId='{$sales_id}'";
                $db->query($query2);

                $session->msg('s', "PDF file uploaded successfully of OrderId $sales_id.");
                redirect('BillingWorklist.php', false);
            } else {
                $session->msg('d', "Error uploading PDF file.");
                redirect('BillingWorklist.php', false);
            }
        }
    } else {
        echo "Error: Order ID not provided.";
        $session->msg("d", $errors);
        redirect('BillingWorklist.php', false);
    }
}
}

if (isset($_POST['PISenttoDB'])) {
  if (isset($_POST['order_id'])) {
    $my_user = $_SESSION['user_id'];
    $sales_id = (int)$_POST['order_id'];
    $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'4')";
    $db->query($query);
    echo "Success";
    $session->msg('s', "PI Sent. ");
    redirect('BillingWorklist.php', false);
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('BillingWorklist.php', false);
  }
}

if (isset($_POST['PaydonetoDB'])) {
  if (isset($_POST['order_id'])) {
    $my_user = $_SESSION['user_id'];
    $sales_id = (int)$_POST['order_id'];
    $query = "insert into sales_audit (salesid,personid,dttm,status_to) values('{$sales_id}','{$my_user}',now(),'5')";
    $db->query($query);
    echo "Success";
    $session->msg('s', "Payment Done");
    redirect('BillingWorklist.php', false);
  } else {
     echo "Error: Order ID not provided.";
    $session->msg("d", $errors);
    redirect('BillingWorklist.php', false);
  }
} 
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></head>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Sales</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" >OrderId</th>
              <th class="text-center" >Date </th>
              <th class="text-center" >Party Name </th>
              <th class="text-center" >User Name </th>
              <th class="text-center" >Delivery Date </th>
              <th class="text-center" >Order</th>
              <th class="text-center" >Action</th>
            </tr>
          </thead>
          <tbody>  
          <?php foreach ($salesreport as $sale) :   
             $statusTo = getStatusTo($sale['SalesId']);?>
            <?php if ($statusTo !=7 && $statusTo !=8) : ?>
              <tr> 
                <td class="text-center" style="width: 8%; "><?php echo remove_junk($sale['SalesId']); ?></td>
                <td class="text-center" ><?php echo remove_junk($sale['Date']); ?></td>
                <td class="text-center" >
                  <a href="#" onclick="showPopup('<?php echo remove_junk($sale['SalesId']); ?>')">
                    <?php echo remove_junk($sale['CardName']); ?>
                  </a>
                </td>
                <td class="text-center"><?php echo remove_junk($sale['name']); ?></td>
                <td class="text-center"><?php echo remove_junk($sale['DeliveryDate']); ?></td>
                <td class="text-center">
                    <?php
                    if ($statusTo == 1) {
                        echo '<form method="post" action="">
                                <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">
                                <button type="submit" name="update-pass" class="btn btn-primary">Accept</button>
                                <button style="margin-top:2px;padding:5px 13px 5px 13px" type="submit" name="CancelOrder" class="btn btn-primary">Reject</button>
                              </form>';
                    } elseif ($statusTo == 2) { 
                        echo '<button class="btn btn-disable" disabled>Accepted</button>';
                        echo '
                        <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '"> 
                        <label for="file_' . $sale['SalesId'] . '" class="form-label" style="border: 1px solid grey; margin-top: 5px; width: 60px; cursor: pointer;">Choose</label>
                        <input type="file" class="form-select" accept=".pdf" name="file" id="file_' . $sale['SalesId'] . '" style="display: none;" onchange="displayFileName(' . $sale['SalesId'] . ')">
                        <span id="fileNameDisplay_' . $sale['SalesId'] . '" style="margin-left: 5px;"></span>
                        <input type="text" id="SAP_Id" name="SAP_Id" style="margin-left: 5px;width:100px;height:25px" placeholder="Enter SAP Id">
                        <button type="submit" name="uploadfile">Upload</button> 
                       </form>';  
                       
                    } elseif ($statusTo !=1 || $statusTo !=2) {
                      echo '<button class="btn btn-disable" disabled>Accepted</button><br>';
                      echo '<button style="margin-top:5px" class="btn btn-disable" disabled>Uploaded</button>';                            
                  } 
                    ?>
                </td>
                <td class="text-center">
                <?php
                    if ($statusTo == 1 || $statusTo == 2) {
                        echo '
                        <form method="post" action="">
                            <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">      
                            <div style="display:flex;justify-content:center"">
                                <button style="margin:0px 5px 5px 0px" type="submit" value="PI Sent" name="PI-Sent" class="btn btn-disable" disabled> <span class="glyphicon glyphicon-send"></span></button>
                                <button style="margin:0px 5px 5px 0px" type="submit" name="Pay-done" class="btn btn-disable" disabled><span class="fa fa-inr"></span></button>                     
                            </div>
                            
                        </form>';
                    } elseif ($statusTo == 3) {
                        echo '
                        <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">
                        <div style="display:flex;justify-content:center">
                            <button style="margin:0px 5px 5px 0px" type="submit" name="PISenttoDB" title="PI Sent" class="btn btn-info"><span class="glyphicon glyphicon-send"></span></button>
                            <button style="margin:0px 5px 5px 0px" type="submit" name="Pay-done" title="Payment Done" class="btn btn-disable" disabled><span class="fa fa-inr"></span></button>
                        </div>
                        
                    </form>';
                    }elseif ($statusTo == 4) {
                      echo '
                      <form method="post" action="" enctype="multipart/form-data">
                      <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">
                      <div style="display:flex;justify-content:center">
                          <button style="margin:0px 5px 5px 0px" type="submit" name="PI-Sent" title="PI Sent" class="btn btn-disable" disabled><span class="glyphicon glyphicon-send"></span></button>
                          <button style="margin:0px 5px 5px 0px" type="submit" name="PaydonetoDB" title="Payment Done" class="btn btn-warning"><span class="fa fa-inr"></span></button>
                      </div>
                      
                  </form>';
                  }elseif ($statusTo == 5) {
                    echo '
                    <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">
                    <div style="display:flex;justify-content:center">
                        <button style="margin:0px 5px 5px 0px" type="submit" name="PI-Sent" title="PI Sent" class="btn btn-disable" disabled><span class="glyphicon glyphicon-send"></span></button>
                        <button style="margin:0px 5px 5px 0px" type="submit" name="Pay-done" title="Payment Done" class="btn btn-disable" disabled><span class="fa fa-inr"></span></button>
                    </div>
                    
                </form>';
                }elseif ($statusTo == 6) {
                  echo '
                  <form method="post" action="" enctype="multipart/form-data">
                  <input type="hidden" name="order_id" value="' . $sale['SalesId'] . '">
                  <div style="display:flex;justify-content:center">
                      <button style="margin:0px 5px 5px 0px" type="submit" name="PI-Sent" title="PI Sent" class="btn btn-disable" disabled><span class="glyphicon glyphicon-send"></span></button>
                      <button style="margin:0px 5px 5px 0px" type="submit" name="Pay-done" title="Payment Done" class="btn btn-disable" disabled><span class="fa fa-inr"></span></button>
                  </div>
                  
              </form>';
              }
                    ?>
                </td>    
              </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody> 
        </table>
      </div>
    </div>
  </div>
</div>
<div id="popup" class="popup">
  <div class="popup-content">
    <span class="close" onclick="closePopup()">&times;</span>
    <p id="popupSalesId"></p>
    <div id="productDetails"></div>
  </div>
</div>
<style>
  /* Add this to your CSS */

  .popup {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.7);
}

.popup-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #fefefe;
  padding: 20px;
  border-radius: 5px;
  text-align: center;
}

.close {
  color: black;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover {
  color: black;
}
</style>
<script>
    function displayFileName(salesId) {
        var fileInput = document.getElementById('file_' + salesId);
        var fileNameDisplay = document.getElementById('fileNameDisplay_' + salesId);

        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = '';
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  function showPopup(salesId) {
    var popup = document.getElementById('popup');
    var popupSalesId = document.getElementById('popupSalesId');
    var productDetailsContainer = $('#productDetails');

    // Set the SalesId in the popup
    popupSalesId.innerHTML = "SalesId: " + salesId;

    // Make an AJAX request to fetch product details
    $.ajax({
      type: 'POST',
      url: 'get_product_details.php',
      data: { salesId: salesId },
      success: function(response) {
        // Clear the previous content and display the product details in the popup
        productDetailsContainer.html(response);

        // Check the status of the "Accept" button
        var acceptButton = $('#acceptButton');
        var action2Buttons = $('.action2-buttons');

        // Check if the "Accept" button is present
        if (acceptButton.length) {
          var isAccepted = !acceptButton.hasClass('btn-disable');
          action2Buttons.prop('disabled', !isAccepted);
        }

        popup.style.display = 'block';
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", status, error);
      }
    });
  }
 
  function closePopup() {
    var popup = document.getElementById('popup');
    var productDetailsContainer = $('#productDetails');

    // Clear the product details when closing the popup
    productDetailsContainer.empty();
    popup.style.display = 'none';
  }
</script>


<?php include_once('layouts/footer.php'); ?>
