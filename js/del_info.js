
  function fnOpenNormalDialog() {
  	
	 var href=this.href;

    var content = $(this).attr("title");
    
    $("#dialog-confirm").html("Czy na pewno chcesz usunąć?<br>"+content);
			
    // Define the Dialog and its properties.
   $("#dialog-confirm").dialog({
        resizable: false,
        modal: true,
        title: "Uwaga!",
        height: 250,
        width: 400,
        buttons: {
            "Tak": function () {
            	            	
                $(this).dialog('close');
                location = href
            },
                "Anuluj": function () {
                $(this).dialog('close');
             
            }
        }
    });
      return false;
}


$(function(){
   $("a.del_cfg").click(fnOpenNormalDialog); 
    });
$(function(){
    $("a.del_s").click(fnOpenNormalDialog); 
   });
