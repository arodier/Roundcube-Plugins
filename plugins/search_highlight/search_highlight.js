
$(document).ready(function()
{
  var searchbox = $('#quicksearchbox');

  searchbox.keydown(function(e) {
    if ( e.which == 13 )
      rcmail.http_post('plugin.store_query', "query="+$(this).val()+"&");
  });
});
