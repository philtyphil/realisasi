$(document).ready(function() {
	$('.selectpicker').selectpicker('show');
	$('input.maxL-2').maxlength({
		alwaysShow: false,
		warningClass: "label ",
		limitReachedClass: "label label-danger",
		separator: ' of ',
		preText: 'You have ',
		postText: ' chars remaining.',
		validate: true,
		threshold: 10
	});
			
	$('input#maxL-4').maxlength({
		alwaysShow: false,
		warningClass: "label ",
		limitReachedClass: "label label-danger",
		separator: ' of ',
		preText: 'You have ',
		postText: ' chars remaining.',
		validate: true,
		threshold: 10
	});

	$(".tc").click(function(){
		if($(this).prop("checked"))
		{
			$('.refresher').selectpicker('refresh');
			$(".form-hidden").fadeIn('slow');
			$(".advance_search").fadeOut('slow');
		}
		
	});
	
	$(".edit_master_submit").on('click',function(){
		
		$(".form_edit_master").ajaxSubmit({
			url		: "{base_url}master/p_edit_master/",
			type	: "POST",
			success : function(data){
				if(typeof(data.error_group) != "undefined" && data.error_group != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_group+"</span>", 
						function(result) {
								
						}
					);
				}

				if(typeof(data.error_code) != "undefined" && data.error_code != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_code+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.error_name) != "undefined" && data.error_name != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_name+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.error_description) != "undefined" && data.error_description != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_description+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.status) != "undefined" && data.status != "404" && data.status != "")
				{
					bootbox.confirm("<span class='text-success text-center'>Successfull Insert Master Data.</span>", 
						function(result) {
							if(result)
							{
								window.location.reload();
							}
						}
					);
				}
				
				
			},
			error	: function(e){
				bootbox.confirm("<span class='text-primary text-center'>Master Gagal Di Insert</span>", function(result) {
						
					});
			}
		
		});
	});
	
	$(".insert_master_submit").on('click',function(){
		
		$(".form_insert_master").ajaxSubmit({
			url		: "{base_url}master/insert/",
			type	: "POST",
			success : function(data){
				if(typeof(data.error_group) != "undefined" && data.error_group != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_group+"</span>", 
						function(result) {
								
						}
					);
				}

				if(typeof(data.error_code) != "undefined" && data.error_code != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_code+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.error_name) != "undefined" && data.error_name != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_name+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.error_description) != "undefined" && data.error_description != "")
				{
					bootbox.confirm("<span class='text-center'>"+data.error_description+"</span>", 
						function(result) {
								
						}
					);
				}
				
				if(typeof(data.status) != "undefined" && data.status != "404" && data.status != "")
				{
					bootbox.confirm("<span class='text-success text-center'>Successfull Insert Master Data.</span>", 
						function(result) {
							if(result)
							{
								window.location.reload();
							}
						}
					);
				}
				
				
			},
			error	: function(e){
				bootbox.confirm("<span class='text-primary text-center'>Master Gagal Di Insert</span>", function(result) {
						
					});
			}
		
		});
	});
	
});

function cari_click()
{
	$("#loading").fadeIn("slow");
	$("#bulan_select").html($("#bulan").val());
	var data = {
		"lokasi" 	: $("#lokasi").val(),
		"tahun"		: $("#tahun").val()
	}
	var url = "{base_url}laporan/search";
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		dataType: "JSON",
		success: function(e){
			$("#load_jml_pegawai").fadeOut("fast");
			$("#load_jml_pegawai").html(e.table_absensi);
			$("#load_jml_pegawai").fadeIn("slow");
			$("#loading").fadeOut("slow");
		}
	});
}


function cancel()
{
	window.location.href="{base_url}home";
}

function print_excel()
{
	$("#loading_rekap_pegawai").fadeIn("slow");

	var awal_masa_ker	= ($("#awal").val() != "") ? $("#awal").val() : "null";
	var akhir_masa_ker	= ($("#akhir").val() != "") ? $("#akhir").val() : "null";
	var lokasi 			= $("#lokasi_rekap_pegawai").val();
	var golongan		= $("#golongan_rekap_pegawai").val();
	var status_pegawai	= $("#status_pegawai_detail").val();
	var pendidikan		= $("#pendidikan_detail").val();
	var status_keluarga	= $("#status_keluarga_detail").val();
	var usia			= ($("#usia").val() != "") ? $("#usia").val() : null;
	
	var gol = "";
	for(i=0;i<golongan.length;i++)
	{
		spliting = golongan[i].split("-");
		gol 	= gol + spliting[0] + "-";
	}
	golongan = gol;//back to default define;
	
	var sts = "";
	for(i=0;i<status_pegawai.length;i++)
	{
		splited = status_pegawai[i].split("-");
		sts 	= sts + splited[0] + "-";
	}
	status_pegawai = sts;
	
	var stk = "";
	for(i=0;i<status_keluarga.length;i++)
	{
		splited = status_keluarga[i].split("-");
		stk 	= stk + splited[0] + "-";
	}
	status_keluarga = stk;
	
	var pnd = "";
	for(i=0;i<pendidikan.length;i++)
	{
		splits = pendidikan[i].split("-");
		pnd 	= pnd + splits[0] + "-";
	}
	pendidikan = pnd;
	
	setTimeout(function(){
		window.open("{base_url}laporan/print_excel/"+lokasi+"/"+golongan+"/"+status_pegawai+"/"+awal_masa_ker+"/"+akhir_masa_ker+"/"+pendidikan+"/"+status_keluarga+"/"+usia,"Print Excel Data Pegawai","width=200, height=100");
		$("#loading_rekap_pegawai").fadeOut("fast");
	},1600);
}

function print_pdf()
{
	$("#loading_rekap_pegawai").fadeIn("slow");
	var awal_masa_ker	= ($("#awal").val() != "") ? $("#awal").val() : "null";
	var akhir_masa_ker	= ($("#akhir").val() != "") ? $("#akhir").val() : "null";
	var lokasi 			= $("#lokasi_rekap_pegawai").val();
	var golongan		= $("#golongan_rekap_pegawai").val();
	var status_pegawai	= $("#status_pegawai_detail").val();
	var pendidikan		= $("#pendidikan_detail").val();
	var status_keluarga	= $("#status_keluarga_detail").val();
	var usia			= ($("#usia").val() != "") ? $("#usia").val() : null;
	
	var gol = "";
	for(i=0;i<golongan.length;i++)
	{
		spliting = golongan[i].split("-");
		gol 	= gol + spliting[0] + "-";
	}
	golongan = gol;//back to default define;
	
	var sts = "";
	for(i=0;i<status_pegawai.length;i++)
	{
		splited = status_pegawai[i].split("-");
		sts 	= sts + splited[0] + "-";
	}
	status_pegawai = sts;
	
	var pnd = "";
	for(i=0;i<pendidikan.length;i++)
	{
		splits = pendidikan[i].split("-");
		pnd 	= pnd + splits[0] + "-";
	}
	pendidikan = pnd;
	
	var stk = "";
	for(i=0;i<status_keluarga.length;i++)
	{
		splited = status_keluarga[i].split("-");
		stk 	= stk + splited[0] + "-";
	}
	status_keluarga = stk;
	
	
	$("#loading_rekap_pegawai").fadeIn("slow");
	setTimeout(function(){
		window.open("{base_url}laporan/print_pdf/"+lokasi+"/"+golongan+"/"+status_pegawai+"/"+awal_masa_ker+"/"+akhir_masa_ker+"/"+pendidikan+"/"+status_keluarga+"/"+usia,"Print PDF Data Pegawai","width=200, height=100");
		$("#loading").fadeOut("fast");
	},2000);
}

function print_html()
{
	$("#loading_rekap_pegawai").fadeIn("slow");
	var lokasi 			= $("#lokasi_rekap_pegawai").val();
	var golongan		= $("#golongan_rekap_pegawai").val();
	var status_pegawai	= $("#status_pegawai_detail").val();
	var awal_masa_ker	= ($("#awal").val() != "") ? $("#awal").val() : "null";
	var akhir_masa_ker	= ($("#akhir").val() != "") ? $("#akhir").val() : "null";
	var pendidikan		= $("#pendidikan_detail").val();
	
	var gol = "";
	for(i=0;i<golongan.length;i++)
	{
		spliting = golongan[i].split("-");
		gol 	= gol + spliting[0] + "-";
	}
	golongan = gol;//back to default define;
	
	var sts = "";
	for(i=0;i<status_pegawai.length;i++)
	{
		splited = status_pegawai[i].split("-");
		sts 	= sts + splited[0] + "-";
	}
	status_pegawai = sts;
	
	var pnd = "";
	for(i=0;i<pendidikan.length;i++)
	{
		splits = pendidikan[i].split("-");
		pnd 	= pnd + splits[0] + "-";
	}
	pendidikan = pnd;
	$("#loading_rekap_pegawai").fadeIn("slow");
	setTimeout(function(){
		window.open("{base_url}laporan/print_html/"+lokasi+"/"+golongan+"/"+status_pegawai+"/"+awal_masa_ker+"/"+akhir_masa_ker+"/"+pendidikan,"_blank");
		$("#loading_rekap_pegawai").fadeOut("fast");
	},1500);
}

function deletemaster(id)
{
	bootbox.confirm("Anda Yakin akan menghapus data ini ?", function(result) {
		if(result)
		{
			$.get( "{base_url}master/delete/"+id, function( data ) 
			{
				if(data.status != '404')
				{
					alert("Data Berhasil di Hapus!");
					window.location.reload();
				}
				else
				{
					alert('Data Gagal di Hapus');
				}
			});
		}
				
	});
}

function proses_insert()
{
	$("#button_input").fadeOut('slow');
	$("#loading_insert").fadeIn("slow");
	var data = {
		nip			: $("#nip_absensi_insert").val(),
		tanggal		: $("#dtp_input2").val(),
		jam_datang	: $("#jam_datang").val(),
		jam_pulang	: $("#jam_pulang").val(),
		
	}
	var url = "{base_url}adm_kepegawaian/insert";
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		dataType: "JSON",
		success: function(e){
			$("#load_absensi_after_insert").fadeOut("fast");
			
				if(e.error_nip != "" && typeof(e.error_nip) != "undefined")
				{
					$("#loading_insert").fadeOut("slow");
					bootbox.confirm(e.error_nip, function(result) {
					});
				}
				else if(e.error_jam_datang != "" && typeof(e.error_jam_datang) != "undefined")
				{
					$("#loading_insert").fadeOut("slow");
					bootbox.confirm(e.error_jam_datang, function(result) {
						$("#jam_datang").focus();
						$("#jam_pulang").focus();
								
					});
				}
				else if(e.error_tanggal != "" && typeof(e.error_tanggal) != "undefined")
				{
					$("#loading_insert").fadeOut("slow");
					bootbox.confirm(e.error_tanggal, function(result) {
								
					});
				}
				else if(e.error_jam_pulang != "" && typeof(e.error_jam_pulang) != "undefined")
				{
					$("#loading_insert").fadeOut("slow");
					bootbox.confirm(e.error_jam_pulang, function(result) {
						$("#jam_datang").focus();
						$("#jam_pulang").focus();
								
					});
				}
				else if(e.error_insert != "" && typeof(e.error_insert) != "undefined")
				{
					$("#loading_insert").fadeOut("slow");
					bootbox.confirm(e.error_insert, function(result) {
						
								
					});
				}
				else
				{
					
					$("#loading_text").html("Proses Menampilkan Data . . .");
					$("#load_absensi_after_insert").html(e.table_absensi);
					
					setTimeout(function(){
						$("#load_absensi_after_insert").fadeIn("slow");
						$("#loading_insert").fadeOut("fast");
						$("#loading_text").html("Proses Input Data . . .");
					},2400);
				}
				
			$("#button_input").fadeIn('slow');
			
			
		}
	});
}

function rekap_click()
{
	$("#bulan_select").html($("#bulan").val());
	var data = {
		"tokenUnit" 	: $("#unit").val(),
		"bulan"			: $("#bulan_rekap").val(),
		"tahun"			: $("#tahun_rekap").val()
	}
	var url = "{base_url}adm_kepegawaian/rekap";
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		dataType: "JSON",
		success: function(e){
			$("#load_absensi").fadeOut("fast");
			$("#loading").fadeIn("slow");
			setTimeout(function(){
				$("#load_absensi").html(e.table_absensi);
				$("#load_absensi").fadeIn("slow");
				$("#loading").fadeOut("slow");
			},2000);
			
			
		}
	});
}

function search_master_click()
{
	$("#loading_rekap_pegawai").fadeIn("slow");
	var id_master 		= $("#id_master").val();

	var level			= $("#level").val();
	var data = {
		id_master : id_master,
		level : level
	};
	var url = "{base_url}master/rekap";
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		dataType: "JSON",
		success: function(e){
			if(e.error_lokasi != "" && typeof(e.error_lokasi) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_lokasi, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_golongan != "" && typeof(e.error_golongan) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_golongan, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_status_pegawai != "" && typeof(e.error_status_pegawai) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_status_pegawai, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_akhir != "" && typeof(e.error_akhir) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_akhir, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_awal != "" && typeof(e.error_awal) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_awal, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_pendidikan != "" && typeof(e.error_pendidikan) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_pendidikan, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else if(e.error_status_keluarga != "" && typeof(e.error_status_keluarga) != "undefined")
			{
				$("#loading_insert").fadeOut("slow");
				bootbox.confirm(e.error_status_keluarga, function(result) {
					$("#loading_rekap_pegawai").fadeOut("slow");
				});
			}
			else
			{
				$("#load_rekap_pegawai").fadeOut("fast");
				setTimeout(function(){
					$("#load_rekap_pegawai").html(e.table_rekap_pegawai);
					$("#load_rekap_pegawai").fadeIn("slow");
					$("#loading_rekap_pegawai").fadeOut("slow");
				},800);
			}
			
			
		},
		error:function()
		{
			bootbox.confirm("<span class='text-center'>Gagal menampilkan!!.<br/> Silakan Informasi Ke bagian terkait!.</span>", function(result) {
				$("#loading_rekap_pegawai").fadeOut("fast");
			});
		}
	});
	
}




