<?php $this->extend('layout') ?>
<?php $this->section('content') ?>


<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-0">
        <div class="row g-0">

     
          <!-- Info kanan -->
          <div class="col-md-9 col-sm-12 profile-right">
            <h5 class="card-title">Profile Information</h5>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-person-badge-fill me-1"></i> Nama Lengkap</div>
              <div class="col-lg-9 col-md-8 value"><?= esc($nama) ?></div>
            </div>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-person-fill me-1"></i> Username</div>
              <div class="col-lg-9 col-md-8 value">
                <?= esc($username) ?> &nbsp;
                
                </span>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-shield-lock-fill me-1"></i> Role</div>
              <div class="col-lg-9 col-md-8 value text-capitalize"><?= esc($role) ?></div>
            </div>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-envelope-fill me-1"></i> Email</div>
              <div class="col-lg-9 col-md-8 value">
                <a href="mailto:<?= esc($email) ?>" class="text-decoration-none text-primary"><?= esc($email) ?></a>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-clock-fill me-1"></i> Login Time</div>
              <div class="col-lg-9 col-md-8 value"><?= esc($loginTime) ?></div>
            </div>

            <div class="row">
              <div class="col-lg-3 col-md-4 label"><i class="bi bi-wifi me-1"></i> Status</div>
              <div class="col-lg-9 col-md-8 value">
                <?php if ($isLoggedIn): ?>
                  <span class="badge-status"><i class="bi bi-check-circle-fill"></i> Sudah Login</span>
                <?php else: ?>
                  <span style="color:#dc3545;font-weight:600;"><i class="bi bi-x-circle-fill"></i> Belum Login</span>
                <?php endif; ?>
              </div>
            </div>

          </div><!-- End info kanan -->
        </div><!-- End row g-0 -->
      </div>
    </div>
  </div>
</div>

<?php $this->endSection() ?>