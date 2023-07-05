<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->load->view('partials/head.php') ?>
</head>

<body id="page-top">
	<div id="wrapper">
		<!-- load sidebar -->
		<?php $this->load->view('partials/sidebar.php') ?>

		<div id="content-wrapper" class="d-flex flex-column">
			<div id="content" data-url="<?= base_url('checking') ?>">
				<!-- load Topbar -->
				<?php $this->load->view('partials/topbar.php') ?>

				<div class="container-fluid">
				<div class="clearfix">
					<div class="float-left">
						<h1 class="h3 m-0 text-gray-800"><?= $title ?></h1>
					</div>
					<div class="float-right">
						<a href="<?= base_url('checking') ?>" class="btn btn-secondary btn-sm"><i class="fa fa-reply"></i>&nbsp;&nbsp;Kembali</a>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col">
						<div class="card shadow">
							<div class="card-header"><strong>Detail Transaksi Checking</strong></div>
							<div class="card-body">
								<form method="POST">
									<h5>Data Petugas</h5>
									<hr>
									<div class="form-row">
										<div class="form-group col-4">
											<label>Kode Petugas</label>
											<input type="text" name="kode_petugas" value="<?= $checking->kode_petugas ?>" readonly class="form-control">
										</div>
										<div class="form-group col-4">
											<label>Nama Petugas</label>
											<input type="text" name="petugas_id" value="<?= $checking->nama_petugas ?>" readonly class="form-control">
										</div>
										<div class="form-group col-4">
											<label>Jenis Pekerjaan</label>
											<input type="text" name="nama_petugas" value="Checking" readonly class="form-control">
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<h5>Data Barang</h5>
											<hr>
											<div class="form-row">

												<input type="hidden" name="checking_id" id="checking-id" value="">

												<div class="form-group col-3">
													<label>Tanggal</label>
													<input type="text" value="<?= $checking->date ?>" class="form-control" readonly>
												</div>
												<div class="form-group col-3">
													<label for="barang">Nama Barang</label>
													<input type="text" value="<?= $checking->nama_barang ?>" class="form-control" readonly>
												</div>
												<div class="form-group col-2">
													<label>Target</label>
													<input type="text" value="<?= $checking->target ?>" class="form-control" readonly>
												</div>
												<div class="form-group col-2">
													<label>Hasil</label>
													<input type="text" value="<?= $checking->hasil ?>" class="form-control" readonly>
												</div>
												<div class="form-group col-2">
													<label>Kinerja</label>
													<div class="input-group">
												        <input type="text" value="<?= $checking->kinerja ?>" class="form-control" readonly>
												        <div class="input-group-prepend">
												          <div class="input-group-text">%</div>
												        </div>
											      	</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>				
						</div>
					</div>
				</div>
				</div>
			</div>
			<!-- load footer -->
			<?php $this->load->view('partials/footer.php') ?>
		</div>
	</div>
	<?php $this->load->view('partials/js.php') ?>
	<script>
		$(document).ready(function(){
			$('tfoot').hide()

			$(document).keypress(function(event){
		    	if (event.which == '13') {
		      		event.preventDefault();
			   	}
			})

			$('#nama_supplier').on('change', function(){
				$(this).prop('disabled', true)
				$('#reset').prop('disabled', false)
				$('input[name="nama_supplier"]').val($(this).val())
			})

			$(document).on('click', '#reset', function(){
				$('#nama_supplier').val('')
				$('#nama_supplier').prop('disabled', false)
				$(this).prop('disabled', true)
				$('input[name="nama_supplier"]').val('')
			})

			$('#nama_barang').on('change', function(){
return false;
				if($(this).val() == '') reset()
				else {
					const url_get_all_barang = $('#content').data('url') + '/get_all_barang'
					$.ajax({
						url: url_get_all_barang,
						type: 'POST',
						dataType: 'json',
						data: {nama_barang: $(this).val()},
						success: function(data){
							$('input[name="kode_barang"]').val(data.kode_barang)
							$('input[name="harga_barang"]').val(data.harga_jual)
							$('input[name="jumlah"]').val(1)
							$('input[name="satuan"]').val(data.satuan)
							$('input[name="max_hidden"]').val(data.stok)
							$('input[name="jumlah"]').prop('readonly', false)
							$('button#tambah').prop('disabled', false)

							$('input[name="sub_total"]').val($('input[name="jumlah"]').val() * $('input[name="harga_barang"]').val())
							
							$('input[name="jumlah"]').on('keydown keyup change blur', function(){
								$('input[name="sub_total"]').val($('input[name="jumlah"]').val() * $('input[name="harga_barang"]').val())
							})
						}
					})
				}
			})

			$(document).on('click', '#tambah', function(e){
				const url_keranjang_barang = $('#content').data('url') + '/keranjang_barang'
				const data_keranjang = {
					nama_barang: $('select[name="nama_barang"]').val(),
					kode_barang: $('input[name="kode_barang"]').val(),
					jumlah: $('input[name="jumlah"]').val(),
					satuan: $('input[name="satuan"]').val(),
				}

				$.ajax({
					url: url_keranjang_barang,
					type: 'POST',
					data: data_keranjang,
					success: function(data){
						if($('select[name="nama_barang"]').val() == data_keranjang.nama_barang) $('option[value="' + data_keranjang.nama_barang + '"]').hide()
						reset()

						$('table#keranjang tbody').append(data)
						$('tfoot').show()

						$('#total').html('<strong>' + hitung_total() + '</strong>')
						$('input[name="total_hidden"]').val(hitung_total())
					}
				})
			})


			$(document).on('click', '#tombol-hapus', function(){
				$(this).closest('.row-keranjang').remove()

				$('option[value="' + $(this).data('nama-barang') + '"]').show()

				if($('tbody').children().length == 0) $('tfoot').hide()
			})

			$('button[type="submit"]').on('click', function(){
				$('input[name="kode_barang"]').prop('disabled', true)
				$('select[name="nama_barang"]').prop('disabled', true)
				$('input[name="satuan"]').prop('disabled', true)
				$('input[name="jumlah"]').prop('disabled', true)
			})

			function hitung_total(){
				let total = 0;
				$('.sub_total').each(function(){
					total += parseInt($(this).text())
				})

				return total;
			}

			function reset(){
				$('#nama_barang').val('')
				$('input[name="kode_barang"]').val('')
				$('input[name="jumlah"]').val('')
				$('input[name="jumlah"]').prop('readonly', true)
				$('button#tambah').prop('disabled', true)
			}
		})

		function calculateKinerja() {
			var target 	= document.getElementById("target").value;
			var hasil 	= document.getElementById("hasil").value;

			if (target && hasil) {
				var kinerja = hasil / target * 100;
				document.getElementById("kinerja").value = kinerja.toFixed(2);
			}
		}

		function getBarangByTanggal(tanggal) {
			var choosen_date 	= tanggal.value;

			const url_get_all_barang = $('#content').data('url') + '/get_related_barang'
			$.ajax({
				url: url_get_all_barang,
				type: 'POST',
				dataType: 'html',
				data: {
					tgl_checking: choosen_date
				},
				success: function(data){
					$("#barang").html(data);
				}
			})
		}

		function getTarget(checking) {
			var checking_id 	= checking.value;
			$("#checking-id").val(checking_id);
			const url_get_all_barang = $('#content').data('url') + '/get_target_checking'
			$.ajax({
				url: url_get_all_barang,
				type: 'POST',
				dataType: 'json',
				data: {
					checking_id: checking_id
				},
				success: function(data){
					$("#target").val(data.target);
				}
			})
		}

	</script>
</body>
</html>