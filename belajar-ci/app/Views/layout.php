<?php
$hlm = "Home";
if(uri_string()!=""){
  $hlm = ucwords(uri_string());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

<title> Pejabat Ferarri <?php echo $hlm ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?= base_url()?>NiceAdmin/assets/img/favicon.png" rel="icon">
  <link href="<?= base_url()?>NiceAdmin/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.snow.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.bubble.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.3.0/dist/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= base_url()?>NiceAdmin/assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Mar 09 2023 with Bootstrap v5.2.3
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
<!-- header -->
 <?= $this->include('components/header') ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Tables</h1>
      <nav>
        <ol class="breadcrumb">
  <li class="breadcrumb-item">Home</li>
  <?php
	if($hlm!="Home"){
	  ?>
	  <li class="breadcrumb-item"><?php echo $hlm?></li> 
	  <?php
	}
  ?> 
</ol>
      </nav>
    </div><!-- End Page Title -->
<?= $this->include('components/sidebar') ?>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Pejabat Devin</h5>

           <?= $this->renderSection('content') ?>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
<?= $this->include('components/footer') ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.umd.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.3.0/dist/simple-datatables.js"></script>
  <script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo base_url('NiceAdmin/assets/js/main.js'); ?>"></script>

</body>

</html>