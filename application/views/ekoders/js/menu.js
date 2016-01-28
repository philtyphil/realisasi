var oTable;
$(document).ready(function() {
	$('.selectpicker').selectpicker('show');
	
	$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings ){
		return {
		  "iStart":         oSettings._iDisplayStart,
		  "iEnd":           oSettings.fnDisplayEnd(),
		  "iLength":        oSettings._iDisplayLength,
		  "iTotal":         oSettings.fnRecordsTotal(),
		  "iFilteredTotal": oSettings.fnRecordsDisplay(),
		  "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		  "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
		};
    };
      
    $.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
		if(oSettings.oFeatures.bServerSide === false){
			var before = oSettings._iDisplayStart;
			oSettings.oApi._fnReDraw(oSettings);
			oSettings._iDisplayStart = before;
			oSettings.oApi._fnCalculateEnd(oSettings);
		}
		oSettings.oApi._fnDraw(oSettings);
    };  
	var responsiveHelper = undefined;
    var breakpointDefinition = {
        tablet: 1024,
        phone : 480
    };
    var tableElement = $('#menu');

	oTable = tableElement.dataTable({
        "paginationType": 'bootstrap',
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
		"order":[['3','asc'],['4','asc']],
		"columnDefs": [
			{ "className": "text-center", "targets": [ 0 ],"width":"12%"},
			{ "className": "text-center", "targets": [ 1 ],"width":"12%"},
			{ "className": "text-left", "targets": [ 2 ] },
			{ "className": "text-left", "targets": [ 3 ] },
			{ "className": "text-center", "targets": [ 4],"width":"6%" },
			{ "className": "text-center", "targets": [ 5],"width":"8%","orderable":false },
		{ "className": "text-center", "targets": [ 6],"width":"10%","orderable":false }
		],
		"ajax"		: '{base_url}menu/json/',
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
	
	$("#button_action").on('click',function(e){
		
		var data  = {
			nama_akun 	: $("#nama_akun").val(),
			parent_akun : $("#parent_akun").val(),
			descr		: $("#desc").val(),
			status		: $('input[name=status]:radio').val(),
			url			: $("#url").val(),
			action		: $("#your_action").val(),
			id			: $("#id").val()
		}
		$.ajax({
				type: "POST",
				url: "{base_url}menu/action_akun",
				data: data,
				success: function(e){
					console.log(e);
					if(e.sukses)
					{
						bootbox.confirm("<span class='text-info text-center'>Proses Berhasil!</span>", function(result) {
						oTable.fnStandingRedraw();
						});
					}
					
				},
				error:function(){
				bootbox.confirm("<span class='text-primary text-center'>Sorry, Proses Gagal!</span>", function(result) {});
				},
				dataType: "JSON"
			});
		
	});
	   
	
	$("#form_show").click(function(){
		
		$("#form_action").attr("aria-expanded","false");
		$("#form_action").attr("aria-expanded","true");
		
		$("#form_action").attr("aria-expanded","false");
		$("#form_action").attr("aria-expanded","true");
		$("#ft-1").addClass("collapsing");
		setTimeout(function(){
			$("#ft-1").removeClass("collapsing");
			$("body,html").animate({scrollTop:0},800);
		},600);
		
		$("#ft-1").addClass("collapse in");
	});
	
	$("#form_tree_button").click(function(){
		$("#loading_the_form_tree").fadeIn('slow');
		$("#form_tree").attr("aria-expanded","false");
		$("#form_tree").attr("aria-expanded","true");
		
		$("#form_tree").attr("aria-expanded","false");
		$("#form_tree").attr("aria-expanded","true");
		$("#ft-tree").addClass("collapsing");
		setTimeout(function(){
			$("#ft-tree").removeClass("collapsing");
			$("body,html").animate({scrollTop:0},800);
			$("#loading_the_form_tree").fadeOut('slow');
		},600);
		
		$("#ft-tree").addClass("collapse in");
	});
	
	
	$('.dd').nestable();
	$('.dd-handle a').on('mousedown', function(e){
		e.stopPropagation();
	});	

	$('.dd').on('change',function(e){
		var data = $('.dd').nestable('serialize');
		alert(window.JSON.stringify(data));
		
	});
});


function load_akun()
{
	$("#loading ").fadeIn('slow');
	$("#list_akun").fadeIn('fast');
	
	setTimeout(function(){
		$("#load_akun").load("{base_url}manage/akun_list/");
	},1000)
	
}

function delete_akun(id)
{
	$("#ft-1").removeClass("collapse in");
	$(".the-form")[0].reset();
	$(".selectpicker").selectpicker('refresh');
	bootbox.confirm("<span class='text-primary text-center'>Apakah Anda yakin Akan Menghapus menu ini ?</span>", function(result) {
		if(result)
		{
			var data = {
				id:id
			};
			$.ajax({
				type: "POST",
				url: "{base_url}menu/delete_akun",
				data: data,
				success: function(e){
					if(e.success)
					{
						oTable.fnStandingRedraw();
					}
					
				},
				error:function(){
				bootbox.confirm("<span class='text-info text-center'>Gagal Delete Akun!</span>", function(result) {});
				},
				dataType: "JSON"
			});
		}
	});
}

function edit_akun(id)
{
	$("#ft-1").removeClass("collapse in");
	$(".the-form").fadeOut('fast');
	$("#loading_the_form").fadeIn('slow');
	
	var url = "{base_url}menu/edit_akun/"+id+"/";
	
	$.get(url,function(e){
		
		$("input[name=status][value=" + e.data[0]['fld_menusts'] + "]").prop('checked', true);
		$("#nama_akun").val(e.data[0]['fld_menunm']);
		$("#url").val(e.data[0]['fld_menuurl']);
		$("#id").val(e.data[0]['fld_menuid']); // Define The id <Hidden Value>
		$("#your_action").val("edit"); // Define The Action <hidden Value>
		
		$(".selectpicker").selectpicker('val',e.data[0]['fld_menuidp']);
		
		$("#loading_the_form").fadeOut('slow');
		$(".the-form").fadeIn('slow');
		
	},"JSON");
	setTimeout(function(){
		$("#ft-1").removeClass("collapsing");
		$("body,html").animate({scrollTop:0},500);
	},2000);
	
	$("#ft-1").addClass("collapse in");
}

//Set to Write Descr
function write_desc(id)
{
	var nama_akun = $("#nama_akun").val();
	if(id == 0)
	{
		$("#desc").val(nama_akun);
	}
	else
	{
	
		var url = "{base_url}menu/get_descr/"+id+"/";
		$.get(url,function(e){
			
	
				
				$("#desc").val(e.descr + " > " + nama_akun);
		
			
		},"JSON");
	}
}