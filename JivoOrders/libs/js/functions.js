function suggestion() {
    $('#sug_input').keyup(function(e) {
      var formData = {
        'product_name': $('input[name=title]').val()
      };
  
      if (formData['product_name'].length >= 1) {
        // process the form
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: formData,
            dataType: 'json',
            encode: true
          })
          .done(function(data) {
            $('#result').html(data).fadeIn();
            $('#result li').click(function() {
              $('#sug_input').val($(this).text());
              $('#result').fadeOut(500);
            });
  
            $("#sug_input").blur(function() {
              $("#result").fadeOut(500);
            });
          });
      } else {
        $("#result").hide();
      }
  
      e.preventDefault();
    });
  }
  
  $('#sug-form').submit(function(e) {
    var formData = {
      'p_name': $('input[name=title]').val()
    };
  
    // process the form
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        data: formData,
        dataType: 'json',
        encode: true
      })
      .done(function(data) {
        // Create a new row for each item
        $('#product_info').append(data);
        total();
        $('.datePicker').datepicker('update', new Date());
        
      })
      .fail(function() {
        // handle errors if needed
      });
  
    e.preventDefault();
  });
  
  function total() {
    $('#product_info input').unbind().change(function(e) {
      var price = +$(this).closest('tr').find('input[name=price]').val() || 0;
      var qty = +$(this).closest('tr').find('input[name=quantity]').val() || 0;
      var total = qty * price;
      $(this).closest('tr').find('input[name=total]').val(total.toFixed(2));
    });
  }
  
  $(document).ready(function() {
    // tooltip
    $('[data-toggle="tooltip"]').tooltip();
  
    $('.submenu-toggle').click(function() {
      $(this).parent().children('ul.submenu').toggle(200);
    });
  
    // suggestion for finding product names
    suggestion();
  
    // calculate total amount
    total();
  
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      todayHighlight: true,
      autoclose: true
    });
  });
  