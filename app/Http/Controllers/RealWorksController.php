<?php

namespace App\Http\Controllers;

use App\Support\RealWorks;
use App\Support\SeoContent;

class RealWorksController extends Controller
{
    public function index()
    {
        $page = RealWorks::page();
        $cases = RealWorks::cases();

        return view('client.works.index', [
            'page' => $page,
            'cases' => $cases,
            'filters' => RealWorks::filters(),
            'videos' => RealWorks::videos(),
            'title' => $page['title'],
            'description' => $page['description'],
            'canonical' => SeoContent::canonical($page['path']),
            'ogImage' => SeoContent::canonical($page['og_image']),
            'breadcrumbs' => [$page['path'] => $page['h1']],
            'schema' => [
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    $page['path'] => $page['h1'],
                ]),
                SeoContent::realWorksGallerySchema($cases),
            ],
        ]);
    }
}
