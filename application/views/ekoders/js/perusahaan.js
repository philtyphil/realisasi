$(document).ready(function() {
	// Initialize DatePicker - @Philtyphils
	$( ".datepicker" ).datepicker({
		
		changeMonth: true,
        changeYear: true
	});
	// Intialize Alert 
	$('textarea#maxL-4').maxlength({
				alwaysShow: true,
				placement: 'top-left'
			});
	// Define Select Picker - @Philtyphils
	$('.selectpicker').selectpicker('show');
	
	var lok = $("#lokasi_rekap_pegawai").val();

	// :Default load all data by Location - @philtyphils
	var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };
   	var tableElement = $('#pegawai');

    var oTable = tableElement.DataTable({
        "paginationType": 'bootstrap',
		"dom"			: 'T<"top"l>rt<"bottom"ip><"clear">',
        "language"      : {
            'lengthMenu': "_MENU_ ",
			//"zeroRecords": "Nothing found - sorry",
			//"info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "Data Pegawai Tidak Ditemukan",
            "infoFiltered": "(Show from _PAGES_ Total records)"
        },
		"iDisplayLength":10,
        "processing": true,
        "serverSide": true,
		"order": [[ 0, 'asc' ], [ 2, 'asc' ]],
		"ajax"		: '{base_url}pegawai/json/'+lok+'/',
		// for more option please email philtyphils@gmail.com;08118779995
		"columnDefs": [
    		{ "width": "10%", "targets": 0 },
    		{ "width": "10%", "targets": 1 },
    		{ "width": "10%", "targets": 2 },
    		{ "width": "10%", "targets": 3 },
    		{ "width": "10%", "targets": 4 },
    		{ "width": "10%", "targets": 5 },
    		{ "width": "15%", "targets": 6 },
    		{ "width": "15%", "targets": 7 },
    		{ "width": "10%", "targets": 8 },
  		],
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
	$("#lokasi_rekap_pegawai").change(function(){
		var lok = $("#lokasi_rekap_pegawai").val();
		var nik = $("#nik").val("");
		var nama = $("#nama").val("");
		oTable.ajax.url('{base_url}pegawai/json/'+lok+'/' ).load();
		
	});
	
	$("#nik").keyup(function(){
		oTable.columns(1).search($(this).val()).draw();
	});
	
	$("#nama").keyup(function(){
		oTable.columns(2).search($(this).val()).draw();
	});
	
	
});