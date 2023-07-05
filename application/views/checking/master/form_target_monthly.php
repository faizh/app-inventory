<table width="100%">
	<tr>
		<td>Tanggal</td>
		<?php foreach ($working_days as $days) { ?>
			<td><?= $days ?></td>
		<?php } ?>
	</tr>
	<?php foreach ($all_petugas as $petugas) { ?>
		<tr>
			<td><?= $petugas->nama ?></td>
			<?php foreach ($working_days as $days) { ?>
				<td>
					<input type="number" class="form-control" style="width: 90px" name="target[<?= $petugas->id ?>][<?= $days ?>]">
				</td>
			<?php } ?>
		</tr>
	<?php } ?>

</table>