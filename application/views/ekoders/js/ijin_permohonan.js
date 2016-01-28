$( document ).ready(function() {
	$('.nama_user').selectpicker();
	$( ".datepicker" ).datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: "dd/mm/yy"
	});
	
	$(".submit").on('click',function(){
		
		$(".form-ijin").ajaxSubmit({
			url		: "{base_url}ijin/proses/",
			type	: "POST",
			success : function(data){
				if(typeof(data.ok) != "undefined" && data.ok != "")
				{
					bootbox.confirm("<span class='text-center'>Ijin Telah Di ajukan</span>", function(result) {
								$(".form-ijin")[0].reset();
								window.location.href="{base_url}ijin/persetujuan";
					});
				} 
			},
			error	: function(e){
				bootbox.confirm("<span class='text-primary text-center'>IJIN GAGAL DI AJUKAN</span>", function(result) {
								$(".form-ijin")[0].reset();
					});
			}
		
		});
	});
	
	
});

