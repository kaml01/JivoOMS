<?php
  $page_title = 'add_sale';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
 
<?php
$partyData=party();
$productdata=join_product_table();

if (isset($_POST['add_sale'])) {
    $req_fields = array('Item', 'qty', 'price', 'partyname', 'CardCode', 'Deliverydate');
    validate_fields($req_fields);

  if (empty($errors)) {
      $s_date = make_date();
      $my_user = $_SESSION['user_id'];
      $selectedPartyName = $db->escape($_POST['partyname']);
      $selectedPartyId=$db->escape($_POST['CardCode']);
      $Deliverydate = $db->escape($_POST['Deliverydate']);

      $sql = "INSERT INTO salesreport (PersonID, Date, CardCode, CardName,DeliveryDate) VALUES ";
      $sql .= "('{$my_user}', '{$s_date}','{$selectedPartyId}', '{$selectedPartyName}', '{$Deliverydate}')";
      $db->query($sql);
 
      $lastid = GetSaleId();

      $sql1 = "INSERT INTO sales_audit (";
      $sql1 .= " salesid,personid,dttm,status_to";
      $sql1 .= ") SELECT ";
      $sql1 .= "'{$lastid}','{$my_user}',now(),'1' FROM DUAL1";
      $sql1 .= "";
      $db->query($sql1);

      for ($i = 0; $i < count($_POST['Item']); $i++) {
          $p_id = $db->escape((int)$_POST['Item'][$i]);
          $s_qty = $db->escape((int)$_POST['qty'][$i]);
          $s_price = $db->escape((int)$_POST['price'][$i]);
          $date = $db->escape($_POST['Date'][$i]);

          $sql2 = "INSERT INTO productsold (";
          $sql2 .= " SalesId,ProductId,Price,Qty";
          $sql2 .= ") VALUES (";
          $sql2 .= "'{$lastid}','{$p_id}','{$s_price}','{$s_qty}'";
          $sql2 .= ")";

          $db->query($sql2);

          //update_product_qty($s_qty, $p_id);
      }

      $session->msg('s', "Sale added and order Id is $lastid");
      redirect('add_sale.php', false);
  } else {
      $session->msg("d", $errors);
      redirect('add_sale.php', false);
  }
}

if (isset($_POST['CancelOrder'])) {
 
      $session->msg('d', " Cancel Order ");
      redirect('add_sale.php', false);
   
 
}
?>
<?php include_once('layouts/header.php'); ?>
<style>
   label{
    background-color:#182667;
    color: white;
    padding: 5px;
   }
/* Center the loader */
#loader {
  color: #182667;
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 80px;
  height: 80px;
  margin: -75px 0 0 -75px;
  border: 10px solid #C2C2C2;
  border-radius: 50%;
  border-top: 10px solid #182667;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <form method="post" action="add_sale.php" id="sale-form">
      <div style="display:flex;width:100%;">
        <div style="width: 50%;">
          <div class="col-md-15">
            <div class="input-group">
              <label for="partyname" class="form-label">Party Name </label>
              <select style="width:300px;height:30px" required class="form-select" name="partyname" id="partyname" style="padding:5px;">
                <option  value="" disabled selected>--select--</option>
                <?php foreach ($partyData as $party): ?>
                    <option value="<?php echo $party['CardName']; ?>" data-cardcode="<?php echo $party['CardCode']; ?>">
                      <?php echo ucwords($party['CardName']); ?></option>
                      <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-15">
            <div class="input-group">
              <div id="PartyDetails" style="border:2px solid black;padding:5px"></div>
            </div>
          </div>
        </div>

        <input type="hidden" id="CardCode" name="CardCode" value="">
        <input type="hidden" id="CardName" name="CardName" value="">

        <div style="display: block;width: 50%;">
          <div class="form-group" style="margin-left:20px">
            <div class="input-group">
              <label for="todaydate" class="form-label">Date </label>
              <input style="padding:4px;" type="text" class="form-select" name="todaydate" value="<?php echo date('d-m-Y'); ?>" readonly>
            </div>
          </div>
          <div class="form-group" style="margin-left:20px">
            <div class="input-group">
              <label for="Deliverydate" class="form-label">Delivery Date </label>
              <input required style="height:29px;" type="date" class="form-select" name="Deliverydate">
            </div>
          </div> 
          <!-- <div class="form-group" style="margin-left:20px">
            <div class="input-group">
              <label for="OMS" class="form-label">OMS</label>
              <input style="padding:3.5px;" type="text" class="form-select" name="OMS">
            </div>
          </div> -->
        </div>
      </div>

      <table class="table table-bordered" style="margin-top: 10px;">
        <thead>
          <th>Category</th>
          <th>Variety</th>
          <th>Item</th>
          <th>Pcs</th>
          <th>QTY (Boxes)</th>
          <th>Price</th>
          <th>Amount</th>
          <th>Action</th>
        </thead>
        <tbody id="product_info">
          <tr>
            <td>
    <select required class="form-control Category" name="Category">
        <option>--Select--</option>
        <option value="Oil">Oil</option>
        <option value="WG">WG</option>
        <option value="WG-SF">WG-SF</option>
        <option value="Soda">Soda</option>
        <option value="Water">Water</option>
        <option value="Immunity Booster">Immunity Booster</option>
    </select>
            </td>
            <td>
                <select required class="form-control Variety" name="Variety">
                </select>
            </td>
            <td>
                <select required class="form-control product-select" name="Item[]">
                </select>
            </td>

            <td><input class="form-control pcs" name="pcs" readonly></td>
            <td><input required type="number" class="form-control" name="qty[]" placeholder="Enter quantity" oninput="calculateAmount(this)"></td>
            <td><input required type="number" class="form-control" name="price[]" placeholder="Enter price" oninput="calculateAmount(this)"></td>
            <td><input type="number" class="form-control" name="amount" readonly></td>
            <td class="text-center">
              <div class="btn-group">
                <button type="button" class="btn btn-xs btn-danger" onclick="removeRow(this)" data-toggle="tooltip" onblur="addRowOnBlur()">
                  <i class="glyphicon glyphicon-remove"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div style="padding:5px">
        <div style="display:flex; justify-content: right;" class="form-group">
          <div class="input-group">
            <label for="totalamount" class="form-label">Total Amount</label>
            <input style="padding:3.5px;width:150px" type="number" class="form-select" name="totalamount" readonly>
          </div>
        </div>
        <div style="display:flex; justify-content: right;" class="form-group">
          <div class="input-group">
            <label for="tax" style="width:100px" class="form-label">TAX</label>
            <input style="padding:3.5px;width:150px" type="number" class="form-select" name="tax" readonly>
          </div>
        </div>
      </div>

      <div style="border:2px solid #ddd;padding:5px;">
        <div class="input-group" style="display: flex;justify-content:right;" >
          <label style="width: 100px;" for="grandtotal"  class="form-label">Grand Total</label>
          <input style="padding:2px;width:150px;height:30px;" type="number" class="form-select" name="grandt" readonly>
        </div>
        <div style="display:flex;justify-content:left;">
          <div class="btn-group">
            <button style="padding:5px;width:70px;margin-right:5px" type="submit" class="btn btn-xs btn-success" name="add_sale" data-toggle="tooltip" onclick="validateForm()">ADD</button>
          </div>
          <div class="btn-group">
            <button style="padding:5px;width:70px" type="submit" class="btn btn-xs btn-danger" data-toggle="tooltip" name="CancelOrder" title="cancel" id="cancel">Cancel</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<div id="loader" style="display:none;"></div> 
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
// Attach the change event to Category and Variety dropdowns
$(document).on('change', '.Category', function () {
    var row = $(this).closest('tr');
    updateVarietyOptions(row);
});

$(document).on('change', '.Variety', function () {
    var row = $(this).closest('tr');
    updateProductOptions(row);
});


$('.Category').trigger('change');

function updateVarietyOptions(row) {
    var selectedCategory = row.find('.Category').val();
    var varietyDropdown = row.find('.Variety');
    varietyDropdown.empty();

    var varietyOptions = {
        "Water": ["--Select--","Water"],
        "Oil": ["--Select--","Cold Press", "Extra Light", "Pomace", "Mustard Kacchi Ghani", "Mustard Pakki Ghani", "Refined", "Desi Ghee", "Desi Ghee A2", "Soya", "Kachi Ghani", "Soyabean", "Mustard", "Sunflower", "Groundnut", "Olive", "Cotton Seed Oil", "Extra Virgin", "Cold Press + Olive", "Jivo Lite", "Rice Bran", "Canola", "Gold"],
        "WG": ["--Select--","Cola", "Ginger Ale", "Mango", "Lemon", "Apple", "Orange", "Blueberry", "Jeera", "Mojito", "Rose"],
        "WG-SF": ["--Select--","Ginger Ale SF"],
        "Immunity Booster": ["--Select--","Immunity Booster", "Seeds", "Pain Oil", "AYURVEDIC"],
        "Soda": ["--Select--",]
    };

    if (selectedCategory in varietyOptions) {
        for (var i = 0; i < varietyOptions[selectedCategory].length; i++) {
            var option = $('<option></option>').attr('value', varietyOptions[selectedCategory][i]).text(varietyOptions[selectedCategory][i]);
            varietyDropdown.append(option);
        }
    }

    // Trigger the change event to update the Product options
    varietyDropdown.trigger('change');
}

function updateProductOptions(row) {
    var selectedVariety = row.find('.Variety').val();
    var productDropdown = row.find('.product-select');
    var pcs=row.find('.pcs').val();
    var products = <?php echo json_encode($productdata); ?>;

    productDropdown.empty();
    productDropdown.append('<option value="">--select--</option>');

    products.forEach(function (product) {
        if (product.Variety === selectedVariety) {
            var option = $('<option></option>').attr('value', product.id).text(product.name);
            option.data('pcs', product.Pcs); 
            option.data('tax', product.TAX);
            productDropdown.append(option);
            //alert((option.data('pcs', product.Pcs)).val());
        }
    });

    productDropdown.trigger('change');
}

$(document).on('change', '.product-select','.pcs', function () {
    var row = $(this).closest('tr');
    updatePcsField(row);
});

function updatePcsField(row) {
    var selectedProduct = row.find('.product-select :selected');
    //alert(selectedProduct.value);
    var pcsField=$(this).find('.product-select :selected').data('pcs');
    var pcs = row.find('[name="pcs"]');
    
    pcs.val(selectedProduct.data('pcs') || 0);
}
</script>
<script>
    var removeButtonFocused = false;
    var taxRate = [];

    function calculateAmount(input) {
        var row = $(input).closest('tr');
        var qty = parseFloat(row.find('[name="qty[]"]').val()) || 0;
        var price = parseFloat(row.find('[name="price[]"]').val()) || 0;
      
        var amount = qty * price;
        row.find('[name="amount"]').val(amount.toFixed(2));
            
        calculateTotalAmount();
    }

    function calculateTotalAmount() {
        var totalAmount = 0;
        var totaltax = 0;
        var grandTotal = 0;

        $('#product_info tr').each(function () {
            var amount = parseFloat($(this).find('[name="amount"]').val()) || 0;
            var selectedId = $(this).find('[name="Item[]"]').val();
            totalAmount += amount;
            var tax = (amount * $(this).find('.product-select :selected').data('tax')) / 100;
            totaltax += tax;
        });

        grandTotal = totalAmount + totaltax;

        $('[name="totalamount"]').val(totalAmount.toFixed(2));
        $('[name="tax"]').val(totaltax.toFixed(2));
        $('[name="grandt"]').val(grandTotal.toFixed(2));
    }

    function removeRow(button) {
        var row = $(button).closest('tr');
        var amountToRemove = parseFloat(row.find('[name="amount"]').val()) || 0;
        row.remove();
        updateTotalAmount(-amountToRemove);
        removeButtonFocused = true;
    }

    function updateTotalAmount(amount) {
        var currentTotal = parseFloat($('[name="totalamount"]').val()) || 0;
        var newTotal = currentTotal + amount;
        $('[name="totalamount"]').val(newTotal.toFixed(2));
    }

    function addRow() {
        var tbody = document.getElementById('product_info');
        var firstRow = tbody.getElementsByTagName('tr')[0];
        var newRow = firstRow.cloneNode(true);
        var inputs = newRow.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }
        tbody.appendChild(newRow);
    }

    $(document).on('focusout', '.btn-danger', function () {
        // Check if the remove button lost focus
        if (!removeButtonFocused) {
            addRow();
        }
        removeButtonFocused = false;
    });
</script>
<script>  
 document.getElementById('PartyDetails').style.display = 'none';
 document.getElementById('partyname').addEventListener('change', function() {
    document.getElementById('PartyDetails').style.display = 'block';
    var selectedOption = this.options[this.selectedIndex];
    var selectedCardCode = selectedOption.getAttribute('data-cardcode') || '';
    
    document.getElementById('CardCode').value = selectedCardCode;
    
    <?php foreach ($partyData as $party): ?>
        if ('<?php echo $party['CardCode']; ?>' === selectedCardCode) {
            document.getElementById('PartyDetails').innerHTML = 'Party Name: <?php echo $party['CardName']; ?><br>' +
                                                                'Address: <?php echo $party['Address']; ?><br>' +
                                                                'State: <?php echo $party['State']; ?><br>';
        }
    <?php endforeach; ?>
});

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>  
function validateForm() {
        var form = document.getElementById("sale-form");
        var elements = form.elements;
        var isValid = true;

        for (var i = 0; i < elements.length; i++) {
          if (elements[i].type !== "button" && elements[i].required) {
                if (elements[i].value.trim() === "") {
                    isValid = false;
                    alert("Please fill in all fields.");
                    break;
                }
            }
        }

        if (isValid) {
          document.getElementById('loader').style.display='block';
    $("#loader").show();
        }
    }
</script> 
<?php include_once('layouts/footer.php'); ?>