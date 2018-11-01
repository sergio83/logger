$("#clearBtn").click(function() {

	$.blockUI({ message: null });

	$.ajax({

	    url : "services.php?action=clear",

        data :  null,
    
        type : 'GET',
    
        dataType : 'json',

        success : function(json) {	 	       
        	$("#body").empty();
        },
        error : function(jqXHR, status, error) {
	        alert("error");
        },
        complete : function(jqXHR, status) {
         $.unblockUI();
         
        }
    }); 

});

 $(document).on('change', '#levelgroup input:radio', function (e) {
    var url = window.location.href;    

    if (url.indexOf('?') > -1){
      url = url.substring(0, url.indexOf('?'));
    }
    window.location.href = url+"?level="+e.target.id;    
});