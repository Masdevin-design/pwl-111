<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        $model = new ProductModel();
        $data = [
            'products' => $model->findAll(),
        ];
        return view('v_home', $data);
    }
}