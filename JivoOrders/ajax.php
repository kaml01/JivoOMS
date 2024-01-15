<?php
// Include necessary files and functions
require_once('includes/load.php');

// Fetch product data from the database or any other source
$productData = join_product_table();

// Return the product data as JSON
echo json_encode($productData);
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
        function updateProductTable(productData) {
            // Clear existing rows in the table
            $('#product_info').empty();

            // Populate the table with the fetched product data
            $.each(productData, function(index, product) {
                var newRow = '<tr>' +
                    '<td>' +
                    '<select class="form-control product-select" name="product_id[]">' +
                    '<option value="' + product.id + '">' + product.name + '</option>' +
                    '</select>' +
                    '</td>' +
                    '<td><input type="text" class="form-control" id="pcs" name="pcs" readonly></td>' +
                    '<td><input type="text" class="form-control" name="qty" placeholder="Enter quantity" oninput="calculateAmount(this)"></td>' +
                    '<td><input type="text" class="form-control" name="price" placeholder="Enter price" oninput="calculateAmount(this)"></td>' +
                    '<td><input type="text" class="form-control" name="amount" readonly></td>' +
                    '<td class="text-center">' +
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-xs btn-danger" onclick="removeRow(this)" data-toggle="tooltip" title="remove" id="Remove" onblur="addRowOnBlur()">' +
                    '<i class="glyphicon glyphicon-remove"></i>' +
                    '</button>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';

                // Append the new row to the table
                $('#product_info').append(newRow);
            });
        }
   
</script>
