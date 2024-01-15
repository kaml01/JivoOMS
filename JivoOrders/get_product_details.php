<?php
require_once('includes/load.php');

if (isset($_POST['salesId'])) {
  $salesId = $_POST['salesId'];

  $productDetails = find_products_by_sales_id($salesId);

  if ($productDetails) {
    echo '<p>Product Details:</p>';
    echo '<table>';
    echo '<thead><tr><th style="width: 5%;">Sr no.</th><th class="text-center" style="width: 40%;">Name</th><th class="text-center" style="width: 5%;" >Qty</th><th class="text-center" style="width: 10%;">Price</th></tr></thead>';
    echo '<tbody>';
    foreach ($productDetails as $key =>$product) {
      echo '<tr>';
      echo '<td>'. ($key + 1) . '</td>';
      echo '<td>' . $product['name'] . '</td>';
      echo '<td style="text-align: center; padding: 8px;">' . $product['Qty'] . '</td>';
      echo '<td class="text-center">' . $product['Price'] . '</td>';
      echo '</tr>';
    }
    echo '</tbody></table>';
  } else {
    echo 'No product details found for SalesId: ' . $salesId;
  }
} else {
  echo 'Invalid request. Please provide a SalesId.';
}
?>
