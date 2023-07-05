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
			<div id="content" data-url="<?= base_url('kasir') ?>">
				<!-- load Topbar -->
				<?php $this->load->view('partials/topbar.php') ?>

				<div class="container-fluid">
					<div class="clearfix">
						<div class="float-left">
							<h1 class="h3 m-0 text-gray-800"><?= $title ?></h1>
						</div>
					</div>
					<hr>
					<?php if ($this->session->flashdata('success')) : ?>
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<?= $this->session->flashdata('success') ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php elseif($this->session->flashdata('error')) : ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<?= $this->session->flashdata('error') ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif ?>
					
					<div class="row">

			            <!-- Earnings (Monthly) Card Example -->
			            <div class="col-xl-3 col-md-6 mb-4">
			              <div class="card border-left-primary shadow h-100 py-2">
			                <div class="card-body">
			                  <div class="row no-gutters align-items-center">
			                    <div class="col mr-2">
			                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Checking</div>
			                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($checkings) ?></div>
			                    </div>
			                    <div class="col-auto">
			                      <i class="fas fa-box fa-2x text-gray-300"></i>
			                    </div>
			                  </div>
			                </div>
			              </div>
			            </div>

			            <!-- Earnings (Monthly) Card Example -->
			            <div class="col-xl-3 col-md-6 mb-4">
			              <div class="card border-left-success shadow h-100 py-2">
			                <div class="card-body">
			                  <div class="row no-gutters align-items-center">
			                    <div class="col mr-2">
			                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Avg Kinerja Checking</div>
			                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $avg_kinerja_checking ?></div>
			                    </div>
			                    <div class="col-auto">
			                      <i class="fas fa-users fa-2x text-gray-300"></i>
			                    </div>
			                  </div>
			                </div>
			              </div>
			            </div>

			            <!-- Earnings (Monthly) Card Example -->
			            <div class="col-xl-3 col-md-6 mb-4">
			              <div class="card border-left-info shadow h-100 py-2">
			                <div class="card-body">
			                  <div class="row no-gutters align-items-center">
			                    <div class="col mr-2">
			                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Barang</div>
			                      <div class="row no-gutters align-items-center">
			                        <div class="col-auto">
			                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= count($barang) ?></div>
			                        </div>
			                      </div>
			                    </div>
			                    <div class="col-auto">
			                      <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
			                    </div>
			                  </div>
			                </div>
			              </div>
			            </div>

			            
			        </div>

			        <div class="row">
			          	<div class="col-md-12">
							<div class="card shadow">
								<div class="card-header"><strong>Kinerja Bulan - <?= date('F Y') ?></strong></div>
								<div class="card-body" style="overflow-x: scroll;">
									<table width="auto" class="table table-bordered">
										<thead>
											<tr>
												<th style="width: 15%;">Tanggal</th>
												<?php foreach ($working_days as $day) { ?>
													<th ><?= $day ?></th>
												<?php } ?>
											</tr>
										</thead>
										<tbody> 
											<!-- dashboard per barang -->
											<tr>
												<td>Kinerja</td>
											<?php 
											foreach ($working_days as $new_day) {
												$found = false;
												foreach ($checkings as $checking) {
													if (date('j', strtotime($checking->date)) == $new_day) {
														if ($checking->kinerja < 95) {
															$label = "danger";
														}elseif ($checking->kinerja > 100) {
															$label = 'warning';
														}else {
															$label = 'success';
														}

														echo 
														'<td><a href="'.base_url('checking/view_detail/'.$checking->checking_id).'">
															<label class="btn btn-sm btn-'.$label.'">'
																.$checking->kinerja.'%
															</label>
															</a>
														</td>';
														$found = true;
														break;
													}
												}
												if (!$found) {
													echo "<td></td>";
												}
											} ?>
											</tr>
										</tbody>
									</table>
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
	<script src="<?= base_url('sb-admin/js/demo/datatables-demo.js') ?>"></script>
	<script src="<?= base_url('sb-admin') ?>/vendor/datatables/jquery.dataTables.min.js"></script>
	<script src="<?= base_url('sb-admin') ?>/vendor/datatables/dataTables.bootstrap4.min.js"></script>
</body>
</html>