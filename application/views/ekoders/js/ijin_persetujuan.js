$( document ).ready(function() {
	$('.selectpicker').selectpicker();
	var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };
    var tableElement = $('#persetujuan');

    tableElement.dataTable({
        "paginationType": 'bootstrap',
        "language"      : {
            'lengthMenu': "_MENU_ ",
			"zeroRecords": "Tidak ada permohonan ijin pegawai",
			//"info": "Showing page _PAGE_ of _PAGES_",
            //"infoEmpty": "Tidak ada permohonan ijin pegawai",
            "infoFiltered": "(Show from _PAGES_ Total records)"
        },
		"iDisplayLength":10,
        "processing": true,
        "serverSide": true,
		"order": [[ 0, 'desc' ]],
		"columnDefs": [
			{ "className": "text-center", "targets": [ 0 ],"width":"12%"},
			{ "className": "text-center", "targets": [ 1 ]},
			{ "className": "text-center", "targets": [ 2 ],"width":"7%"},
			{ "className": "text-left", "targets": [ 3 ],"orderable":false},
			{ "className": "text-center", "targets": [ 4],"width":"6%" },
			{ "className": "text-center", "targets": [ 5],"width":"15%","orderable":false }
		],
		"ajax"		: '{base_url}ijin/json/',
		// for more option please email philtyphils@gmail.com;08118779995
		
        preDrawCallback: function () {
            // Initialize the responsive datatables helper once.
            if (!responsiveHelper) {
                responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
            }
        },
        rowCallback    : function (nRow) {
            responsiveHelper.createExpandIcon(nRow);
        },
        drawCallback   : function (oSettings) {
            responsiveHelper.respond();
        }
    });
	
	
});

function view_detail(id)
{
	url = "{base_url}ijin/details/"+id;
	$.get(url,function(e){
	
		bootbox.dialog({
			message: e.html,
			buttons: 			
			{
				"success" :
				{
					"label" : "<i class='fa fa-check'></i> Disetujui",
					"className" : "btn-sm btn-success",
					"callback": function() {
						$("#perstujuan_ijin").ajaxSubmit({
							url 	: "{base_url}ijin/q/1/",
							type 	: "POST",
							success	: function(e){
								bootbox.confirm("<span class='text-center'>Ijin Telah Di setujui</span>", function(result) {
									window.location.href="{base_url}ijin/persetujuan";
								});
							},
							error	: function(err)
							{
								alert("Persetujuan Gagal");
							}
							
						});
					}
				},
				"danger" :
					{
						"label" : "<i class='fa fa-ban'></i> Tidak Disetujui",
						"className" : "btn-sm btn-danger",
						"callback": function() {
						$("#perstujuan_ijin").ajaxSubmit({
							url 	: "{base_url}ijin/q/2/",
							type 	: "POST",
							success	: function(e){
								bootbox.confirm("<span class='text-center'>Ijin Telah Di <u>Tolak</u></span>", function(result) {
									window.location.href="{base_url}ijin/persetujuan";
								});
							},
							error	: function(err)
							{
								alert("Persetujuan Ijin Gagal");
							}
							
						});
					}
				}, 
				"inverse" :
					{
						"label" : "Close",
						"className" : "btn-sm btn-line",
						"callback": function() {
						//Example: console.log("");
					}
				}
			}
		});
	},"JSON");
}

function getnote(id)
{
	url = "{base_url}ijin/note/"+id;
	$.get(url,function(e){
		
		bootbox.confirm(e.data.fld_ijinrem, function(result) {});
	});
}