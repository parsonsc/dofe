jQuery.noConflict();
(function($){
  $(document).ready(function() {
    var opts = {
        dateFormat: 'dd-mm-yy',
        showSecond: true,
        timeFormat: 'hh:mm:ss'
    };
    $('#start_date').datetimepicker( opts );
    $('#end_date').datetimepicker( opts );
  });
})(jQuery);   