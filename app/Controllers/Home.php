<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
    protected $productModel;

public function __construct()
{
    helper(['number', 'form']);
    $this->productModel = new ProductModel();
}
    public function index(): string
    {
        $data = [
            'products' => $this->productModel->findAll(),
        ];
        return view('v_home', $data);
    }
}