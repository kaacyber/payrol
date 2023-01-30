    <section class="content">
      <div class="container-fluid">
        <!-- Main row -->
        <div class="row">

          <section class="col-lg-12 connectedSortable">

            <!-- Map card -->
            <div class="card">
              <div class="card-header"> <?=$title?> </h3>
              </div>
              <div class="card-body table-responsive">
                <table id="myTable" class="table table-bordered table-striped text-center">
                    <thead>
                      <th width="1%">No</th>
                      <th>Nama</th>
                      <th>Jenis</th>
                      <th>Waktu</th>
                      <th>Keterangan</th>
                      <th>Status</th>
                      <th>Opsi</th> 
                    </thead>
                    <tbody>
                      <?php $no=1; foreach ($data as $d) { 
                        $cek = $this->db->query(" select min(tanggal) as mulai,max(tanggal) as akhir from detailcuti where id_cuti = '$d->id_cuti' ")->row();
                      ?>
                      <tr>
                        <td width="1%"><?=$no++?></td>
                        <td><?=ucfirst($d->nama)?></td>
                        <td><?=ucfirst($d->jenis_cuti)?></td>
                        <td><?=date('d/m/Y', strtotime($cek->mulai))?> - <?=date('d/m/Y', strtotime($cek->akhir))?></td>
                        <td>
                          <?=ucfirst($d->alasan)?><br>
                          <?php if ($d->jenis_cuti == 'sakit') { ?>
                            <small>Bukti  <a target="_blank" href="<?=base_url('bukti/'.$d->bukti)?>" >Klik disini</a></small>
                          <?php } ?>
                        </td>
                        <td><?=ucfirst($d->status)?></td>
                        <td>
                          <?php if ($d->status == 'diajukan') { ?>
                          <a onclick="return confirm('apakah anda yakin ingin menerima pengajuan cuti ini?')" href="<?=base_url('admin/cuti_terima/'.$d->id_cuti)?>" class="btn btn-primary btn-sm"><span class="fa fa-check"></span></a>
                          <a onclick="return confirm('apakah anda yakin ingin menolak pengajuan cuti ini?')" href="<?=base_url('admin/cuti_tolak/'.$d->id_cuti)?>" class="btn btn-danger btn-sm"><span class="fa fa-trash"></span></a>
                          <?php } ?>
                           <?php if ($d->status == 'diterima') { ?>
                            <button class="btn btn-primary btn-sm">Anda menerima pengajuan</button>
                          <?php } ?>
                          <?php if ($d->status == 'ditolak') { ?>
                            <button class="btn btn-danger btn-sm">Anda menolak pengajuan</button>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
              </div>
            </div>
          </section>
        </div>
      </div>
    </section>