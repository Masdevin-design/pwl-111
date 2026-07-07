<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

        <?= form_hidden('username', session()->get('username')) ?>
        <?= form_input(['type' => 'hidden', 'name' => 'total_harga', 'id' => 'total_harga']) ?>

        <div class="col-12">
            <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
            <?= form_input([
                'name' => 'nama', 'id' => 'nama', 'class' => 'form-control',
                'value' => session()->get('username'), 'readonly' => true
            ]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
            <?= form_input(['name' => 'alamat', 'id' => 'alamat', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
            <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?>
            <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
            <?= form_input(['name' => 'ongkir', 'id' => 'ongkir', 'class' => 'form-control', 'readonly' => true]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Kode Voucher', 'voucher_code', ['class' => 'form-label']) ?>
            <?= form_input(['name' => 'voucher_code', 'id' => 'voucher_code', 'class' => 'form-control', 'placeholder' => 'Opsional']) ?>
            <small class="text-muted">Tersedia: FLASH10, FLASH15, MEMBER20</small>
        </div>
        <div class="col-12">
            <?= form_submit('submit', 'Buat Pesanan', ['class' => 'btn btn-primary']) ?>
        </div>

        <?= form_close() ?>
    </div>
    <div class="col-lg-6">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th><th>Harga</th><th>Jumlah</th><th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)) : foreach ($items as $item) : ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                <tr>
                    <td colspan="3">Subtotal (Total Harga)</td>
                    <td><?= number_to_currency($total, 'IDR') ?></td>
                </tr>
                <tr class="text-danger">
                    <td colspan="3">Diskon Voucher</td>
                    <td id="diskon_voucher">-IDR 0</td>
                </tr>
                <tr>
                    <td colspan="3">PPN (11%)</td>
                    <td id="ppn">IDR 0</td>
                </tr>
                <tr>
                    <td colspan="3">Biaya Admin</td>
                    <td id="biaya_admin">IDR 0</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Subtotal (+PPN+Admin-Voucher)</strong></td>
                    <td><strong id="subtotal_after"><?= number_to_currency($total, 'IDR') ?></strong></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Grand Total (+Ongkir)</strong></td>
                    <td><strong><span id="total"><?= number_to_currency($total, 'IDR') ?></span></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function() {
        let ongkir = 0;
        let subtotal = <?= $total ?>;

        const voucherMap = {
            'FLASH10': 0.10,
            'FLASH15': 0.15,
            'MEMBER20': 0.20
        };

        function getAdminRate(total) {
            if (total <= 20000000) return 0.006;
            if (total <= 40000000) return 0.008;
            return 0.010;
        }

        function toIDR(n) {
            return 'IDR ' + Math.round(n).toLocaleString('id-ID');
        }

        function hitungTotal() {
            let code = $('#voucher_code').val().trim().toUpperCase();
            let voucherRate = voucherMap[code] || 0;

            let diskonVoucher = subtotal * voucherRate;
            let ppn = subtotal * 0.11;
            let biayaAdmin = subtotal * getAdminRate(subtotal);
            let subtotalAfter = subtotal - diskonVoucher + ppn + biayaAdmin;
            let grandTotal = subtotalAfter + ongkir;

            $("#ongkir").val(ongkir);
            $("#diskon_voucher").text('-' + toIDR(diskonVoucher));
            $("#ppn").text(toIDR(ppn));
            $("#biaya_admin").text(toIDR(biayaAdmin));
            $("#subtotal_after").text(toIDR(subtotalAfter));
            $("#total").text(toIDR(grandTotal));
            $("#total_harga").val(subtotal);
        }

        hitungTotal();
        $('#voucher_code').on('input', hitungTotal);

        $('#kelurahan').select2({
            placeholder: 'Cari daerah tujuan',
            minimumInputLength: 3,
            ajax: {
                url: '<?= site_url('ajax/destinations') ?>',
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term }),
                processResults: data => data,
                cache: true
            }
        });

        $("#kelurahan").on('change', function() {
            $("#layanan").empty();
            ongkir = 0;
            hitungTotal();
            $.ajax({
                url: "<?= site_url('ajax/costs') ?>",
                dataType: "json",
                data: { destination: $(this).val() },
                success: function(data) {
                    data.forEach(function(item) {
                        $("#layanan").append(
                            $('<option>', {
                                value: item.cost,
                                text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                            })
                        );
                    });
                }
            });
        });

        $("#layanan").on('change', function() {
            ongkir = parseInt($(this).val());
            hitungTotal();
        });
    });
</script>
<?= $this->endSection() ?>