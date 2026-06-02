<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticlesController extends Controller
{
    public function index(Request $request) {

        $articles = Article::published()->orderBy('created_at', 'DESC')->paginate(9);

        return view('client.articles.index')
            ->with('articles', $articles)
            ->with('title', 'Новини');
//            ->with('breadcrumbs', [
//                route('articles') => 'Новини',
//            ]);
    }

    public function show($alias) {
        $article = Article::published()->where('alias', $alias)->first();

        if ($article === null) {
            abort(404);
        }

        $list = Article::published()->orderBy('created_at', 'DESC')->limit(3)->get();

        return view('client.articles.show')
            ->with('article', $article)
            ->with('list', $list)
            ->with('title', $article->title)
            ->with('description', $article->description)
            ->with('keywords', $article->keywords)
            ->with('breadcrumbs', [
                route('articles') => 'Новини',
                $article->location => $article->title,
            ]);
    }
}
