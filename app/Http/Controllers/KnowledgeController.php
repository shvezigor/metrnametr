<?php

namespace App\Http\Controllers;

use App\Support\KnowledgePlan;
use App\Support\SeoContent;
use Illuminate\Pagination\LengthAwarePaginator;

class KnowledgeController extends Controller
{
    public function index()
    {
        $allArticles = SeoContent::articles();
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $articles = new LengthAwarePaginator(
            $allArticles->forPage($page, $perPage)->values(),
            $allArticles->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('client.knowledge.index')
            ->with('articles', $articles)
            ->with('title', 'База знань про двері | Метр на Метр')
            ->with('description', 'Практичні поради Метр на Метр щодо вибору, монтажу, утеплення, замків і догляду за вхідними дверима.')
            ->with('breadcrumbs', [
                route('knowledge.index') => 'База знань',
            ])
            ->with('schema', [
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/knowledge' => 'База знань',
                ]),
            ]);
    }

    public function image($slug)
    {
        $article = SeoContent::article($slug);

        if (!$article) {
            abort(404);
        }

        return response(SeoContent::articleImageSvg($article), 200)
            ->header('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function show($slug)
    {
        $article = SeoContent::article($slug);

        if (!$article) {
            abort(404);
        }

        return view('client.knowledge.show')
            ->with('article', $article)
            ->with('related', SeoContent::articles()->where('slug', '!=', $slug)->take(3))
            ->with('title', $article['title'] . ' — поради виробника | Метр на Метр')
            ->with('description', $article['description'])
            ->with('breadcrumbs', [
                route('knowledge.index') => 'База знань',
                route('knowledge.show', ['slug' => $article['slug']]) => $article['title'],
            ])
            ->with('schema', [
                SeoContent::articleSchema($article),
                SeoContent::faqSchema($article['faq']),
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/knowledge' => 'База знань',
                    '/knowledge/' . $article['slug'] => $article['title'],
                ]),
            ]);
    }

    public function plan()
    {
        $clusters = KnowledgePlan::clusters();
        $articles = KnowledgePlan::articles();

        return view('client.knowledge.plan')
            ->with('clusters', $clusters)
            ->with('articlesByCluster', $articles->groupBy('cluster'))
            ->with('totalArticles', $articles->count())
            ->with('title', 'План бази знань про двері | Метр на Метр')
            ->with('description', 'Редакційний план AI-friendly бази знань Метр на Метр: 100 експертних статей про вибір, виробництво, монтаж, безпеку, дизайн і догляд за дверима.')
            ->with('breadcrumbs', [
                route('knowledge.index') => 'База знань',
                route('knowledge.plan') => 'План бази знань',
            ])
            ->with('schema', [
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/knowledge' => 'База знань',
                    '/knowledge/plan' => 'План бази знань',
                ]),
            ]);
    }

    public function forAiAgents()
    {
        return view('client.knowledge.for-ai-agents')
            ->with('site', SeoContent::site())
            ->with('articles', SeoContent::articles())
            ->with('title', 'Сторінка для AI-агентів | Метр на Метр')
            ->with('description', 'Короткий опис сайту Метр на Метр, основних товарів, джерел інформації та контактних сторінок для AI-агентів.')
            ->with('breadcrumbs', [
                route('for-ai-agents') => 'Сторінка для AI-агентів',
            ])
            ->with('schema', [
                SeoContent::organizationSchema(),
                SeoContent::websiteSchema(),
            ]);
    }
}
