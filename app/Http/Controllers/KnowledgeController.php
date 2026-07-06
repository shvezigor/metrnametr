<?php

namespace App\Http\Controllers;

use App\Support\SeoContent;

class KnowledgeController extends Controller
{
    public function index()
    {
        return view('client.knowledge.index')
            ->with('articles', SeoContent::articles())
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
