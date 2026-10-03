<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('front/home', [
            'title' => setting('site_name', 'Cms-shop'),
            'categories' => Category::all(),
            'featured' => Product::list(['featured' => true], 8),
            'latest' => Product::list([], 8),
            'navPages' => $this->navPages(),
        ]);
    }
}
