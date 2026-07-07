<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
 
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;
    private $token;

    function __construct()
    {  
        $this->transactionModel = new TransactionModel(); 
        $this->transactionDetailModel = new TransactionDetailModel(); 
        $this->token = env('MY_API_KEY');
    }

    private function authenticate()
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (empty($header)) {
            return false;
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return false;
        }

        return $matches[1] === $this->token;
    }

    private function unauthorized()
    {
        return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => false,
                        'message' => 'Unauthorized'
        ]);
    }

   public function index()
{
    $cart = \Config\Services::cart();

    return view('v_keranjang', [
        'items' => $cart->contents(),
        'total' => $cart->total(),
    ]);
}

public function cart_add()
{
    $productId = $this->request->getPost('id');
    $qty       = (int) ($this->request->getPost('qty') ?? 1);

    $product = (new \App\Models\ProductModel())->find($productId);

    if (!$product) {
        session()->setFlashdata('failed', 'Produk tidak ditemukan');
        return redirect()->back();
    }

    $cart = \Config\Services::cart();
    $cart->insert([
        'id'      => (string) $product['id'],
        'qty'     => $qty,
        'price'   => $product['harga'],
        'name'    => $product['nama'],
        'options' => ['foto' => $product['foto']],
    ]);

    session()->setFlashdata('success', 'Produk ditambahkan ke keranjang');
    return redirect()->to('keranjang');
}

public function cart_edit()
{
    $cart = \Config\Services::cart();

    $i = 1;
    foreach ($cart->contents() as $item) {
        $qty = $this->request->getPost('qty' . $i);
        if ($qty !== null) {
            $cart->update([
                'rowid' => $item['rowid'],
                'qty'   => (int) $qty,
            ]);
        }
        $i++;
    }

    session()->setFlashdata('success', 'Keranjang diperbarui');
    return redirect()->to('keranjang');
}

public function cart_delete($rowid = null)
{
    $cart = \Config\Services::cart();
    $cart->remove($rowid);

    return redirect()->to('keranjang');
}

public function cart_clear()
{
    $cart = \Config\Services::cart();
    $cart->destroy();

    return redirect()->to('keranjang');
}
public function checkout()
{
    $cart = \Config\Services::cart();

    return view('v_checkout', [
        'items' => $cart->contents(),
        'total' => $cart->total(),
    ]);
}
public function destinations()
{
    $search = $this->request->getGet('q');

    if (empty($search)) {
        return $this->response->setJSON(['results' => []]);
    }

    $client = \Config\Services::curlrequest();

    $response = $client->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
        'query' => [
            'search' => $search,
            'limit'  => 50,
        ],
        'headers' => [
            'key' => getenv('RAJAONGKIR_API_KEY'),
        ],
        'http_errors' => false,
    ]);

    $data = json_decode($response->getBody(), true);
    $destinations = $data['data'] ?? [];

    $results = [];
    foreach ($destinations as $dest) {
        $results[] = [
            'id'   => $dest['id'],
            'text' => $dest['label'],
        ];
    }

    return $this->response->setJSON(['results' => $results]);
}
public function costs()
{
    $destination = $this->request->getGet('destination');

    $client = \Config\Services::curlrequest();

    $response = $client->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
        'headers' => [
            'key'          => getenv('RAJAONGKIR_API_KEY'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'form_params' => [
            'origin'      => 64999, // asal toko, sesuai costs.rest — sesuaikan kalau beda
            'destination' => $destination,
            'weight'      => 1000,
            'courier'     => 'jne',
        ],
        'http_errors' => false,
    ]);

    $data = json_decode($response->getBody(), true);

    return $this->response->setJSON($data['data'] ?? []);
}
public function buy()
{
    $cart  = \Config\Services::cart();
    $items = $cart->contents();

    if (empty($items)) {
        session()->setFlashdata('failed', 'Keranjang kosong');
        return redirect()->to('keranjang');
    }

    $username    = $this->request->getPost('username');
    $alamat      = $this->request->getPost('alamat');
    $ongkir      = (int) $this->request->getPost('ongkir');
    $voucherCode = $this->request->getPost('voucher_code');

    // Total harga pembelian = subtotal produk saja, TIDAK termasuk ongkir
    $totalHarga    = $cart->total();
    $ppn           = hitung_ppn($totalHarga);
    $biayaAdmin    = hitung_biaya_admin($totalHarga);
    $diskonVoucher = hitung_diskon_voucher($totalHarga, $voucherCode);

    $transactionId = $this->transactionModel->insert([
        'username'       => $username,
        'total_harga'    => $totalHarga,
        'alamat'         => $alamat,
        'ongkir'         => $ongkir,
        'ppn'            => $ppn,
        'biaya_admin'    => $biayaAdmin,
        'voucher_code'   => !empty($voucherCode) ? strtoupper($voucherCode) : null,
        'diskon_voucher' => $diskonVoucher,
        'status'         => 0,
    ]);

    foreach ($items as $item) {
        $this->transactionDetailModel->insert([
            'transaction_id' => $transactionId,
            'product_id'     => $item['id'],
            'jumlah'         => $item['qty'],
            'diskon'         => 0,
            'subtotal_harga' => $item['price'] * $item['qty'],
        ]);
    }

    $cart->destroy();

    session()->setFlashdata('success', 'Pesanan berhasil dibuat');
    return redirect()->to('history');
}
public function history()
{
    $username = session()->get('username');

    $transactions = $this->transactionModel
                        ->where('username', $username)
                        ->orderBy('created_at', 'DESC')
                        ->findAll();

    $transactionIds = array_column($transactions, 'id');
    $products = !empty($transactionIds)
        ? $this->transactionDetailModel->getProductsByTransactionIds($transactionIds)
        : [];

    return view('v_history', [
        'username'     => $username,
        'transactions' => $transactions,
        'products'     => $products,
    ]);
}

public function apiIndex()
{
    // Khusus untuk /api/transactions — pakai Bearer token, bukan session
    if (! $this->authenticate()) {
        return $this->unauthorized();
    }

    $start = $this->request->getGet('start');
    $end   = $this->request->getGet('end');

    $page    = (int) ($this->request->getGet('page') ?? 1);
    $perPage = (int) ($this->request->getGet('per_page') ?? 10);

    $query = $this->transactionModel->orderBy('created_at', 'DESC');

    if ($start && $end) {
        $query->where('created_at >=', $start)->where('created_at <=', $end);
    }

    $transactions = $query->paginate($perPage, 'default', $page);

    $transactionIds = [];
    if (!empty($transactions)) {
        $transactionIds = array_column($transactions, 'id');
    }

    $products = [];
    if (!empty($transactionIds)) {
        $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);
    }

    foreach ($transactions as $key => $trx) {
        $transactions[$key]['details'] = $products[$trx['id']] ?? [];
    }

    $pager = $this->transactionModel->pager;

    return $this->response->setJSON([
        'filter' => ['start' => $start, 'end' => $end],
        'data' => $transactions,
        'pagination' => [
            'current_page' => $page,
            'per_page'     => $perPage,
            'last_page'    => $pager->getPageCount(),
            'total_data'   => $pager->getTotal(),
            'has_next'     => $page < $pager->getPageCount(),
            'has_prev'     => $page > 1,
        ]
    ]);
}
}