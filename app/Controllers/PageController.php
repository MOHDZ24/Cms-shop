<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Page;

final class PageController extends Controller
{
    public function show(string $slug): void
    {
        $page = Page::findBySlug($slug);
        if ($page === null) {
            abort(404);
        }

        $this->view('front/page', [
            'title' => (string) $page['title'],
            'page' => $page,
            'navPages' => $this->navPages(),
        ]);
    }
}
