$(document).ready(function(){
	$("#command").focus();
	
	$(document).click(function(){
		$("#command").focus();
	});
	
	$("#command").keydown(function(e){
		// Enter key
		if(e.keyCode == 13){
			$("#command").prop('disabled', true);
			$.ajax({
				url: "http://localhost/httpServer.php",
				type: "POST",
				data: ({'command': $("#command").val()}),
				success: function(response){
					$("#log").append(">" + $("#command").val() + "</br>" + response + "</br>");
					$("#command").val("");
					$("#command").prop('disabled', false);
					$("#command").focus();
				},
				error: function(jqXHR, textStatus, errorMessage) {
					//alert(errorMessage);
				}
			});
		}
	});
});