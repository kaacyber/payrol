    <section class="content">
      <div class="container-fluid">
        <!-- Main row -->
        <div class="row">

          <section class="col-lg-12 connectedSortable">

            <!-- Map card -->
            <div class="card">
              <div class="card-header"> <?=$title?> </h3>
              </div>
              <form method="post" action="<?=base_url('admin/pegawai_update/'.$detail->nip)?>">
                <div class="card-body">
                  <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" value="<?=$detail->nip?>" class="form-control" required="" readonly>
                  </div>
                  <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" value="<?=$detail->nama?>" class="form-control" required="">
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?=$detail->email?>" class="form-control" required="">
                  </div>
                  <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                      <option value="" selected="" disabled="">Pilih Jenis Kelamin</option>
                      <option <?php if ($detail->jenis_kelamin == 'L') {echo 'selected'; }?> value="L">Laki-Laki</option>
                      <option <?php if ($detail->jenis_kelamin == 'P') {echo 'selected'; }?> value="P">Perempuan</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Jabatan</label>
                    <select name="departemen" class="form-control">
                      <option value="" selected="" disabled="">Pilih Jabatan</option>
                      <?php foreach ($data as $d) { ?>
                      <option <?php if ($detail->id_departemen == $d->departemen_id) {echo "selected";}?> value="<?=$d->departemen_id?>"><?=$d->departemen?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Waktu masuk</label>
                    <input type="date" name="masuk" value="<?=$detail->waktu_masuk?>" class="form-control" required="">
                  </div>
                  <div class="form-group">
                    <label>Gaji</label>
                    <input type="number" name="gaji" value="<?=$detail->gaji?>" class="form-control" required="">
                  </div>
                </div>
                <div class="card-footer">
                  <a href="<?=base_url('admin/pegawai')?>" class="btn btn-danger">Kembali</a>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
          </section>
        </div>
      </div>
    </section>