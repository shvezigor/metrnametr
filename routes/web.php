<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/logout', function() {
    Auth::logout();
    Session::flush();
    return Redirect::to('/');
});

// BEGIN Admin
Route::get('/admin/{all?}', 'Admin\MainController@index')
    ->where('all', '(.*)')
    ->middleware(['auth:web']);

Route::get('/admin', 'Admin\MainController@index')
    ->middleware(['auth:web']);
// END Admin

// SEO and AI discovery
Route::get('/llms.txt', ['as' => 'llms', 'uses' => 'SeoController@llms']);
Route::get('/llms-full.txt', ['as' => 'llms-full', 'uses' => 'SeoController@llmsFull']);
Route::get('/ai-policy.txt', ['as' => 'ai-policy', 'uses' => 'SeoController@aiPolicy']);
Route::get('/robots.txt', ['as' => 'robots', 'uses' => 'SeoController@robots']);
Route::get('/sitemap.xml', ['as' => 'sitemap', 'uses' => 'SeoController@sitemap']);
Route::get('/nashi-roboty', ['as' => 'real-works.index', 'uses' => 'RealWorksController@index']);
Route::get('/vkhidni-dveri-lutsk', ['as' => 'seo.vkhidni-dveri-lutsk', 'uses' => 'SeoController@landing'])->defaults('slug', 'vkhidni-dveri-lutsk');
Route::get('/mizhkimnatni-dveri-lutsk', ['as' => 'seo.mizhkimnatni-dveri-lutsk', 'uses' => 'SeoController@landing'])->defaults('slug', 'mizhkimnatni-dveri-lutsk');
Route::get('/dveri-z-montazhem-lutsk', ['as' => 'seo.dveri-z-montazhem-lutsk', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-z-montazhem-lutsk');
Route::get('/dveri-dlya-kvartyry', ['as' => 'seo.dveri-dlya-kvartyry', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-dlya-kvartyry');
Route::get('/dveri-dlya-budynku', ['as' => 'seo.dveri-dlya-budynku', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-dlya-budynku');
Route::get('/dveri-z-termorozryvom', ['as' => 'seo.dveri-z-termorozryvom', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-z-termorozryvom');
Route::get('/protypozhezhni-dveri', ['as' => 'seo.protypozhezhni-dveri', 'uses' => 'SeoController@landing'])->defaults('slug', 'protypozhezhni-dveri');
Route::get('/dveri-volyn', ['as' => 'seo.dveri-volyn', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-volyn');
Route::get('/dveri-rivne', ['as' => 'seo.dveri-rivne', 'uses' => 'SeoController@landing'])->defaults('slug', 'dveri-rivne');
Route::get('/vkhidni-dveri-rivne', ['as' => 'seo.vkhidni-dveri-rivne', 'uses' => 'SeoController@landing'])->defaults('slug', 'vkhidni-dveri-rivne');
Route::get('/mizhkimnatni-dveri-rivne', ['as' => 'seo.mizhkimnatni-dveri-rivne', 'uses' => 'SeoController@landing'])->defaults('slug', 'mizhkimnatni-dveri-rivne');

// Knowledge Base
Route::get('/knowledge', ['as' => 'knowledge.index', 'uses' => 'KnowledgeController@index']);
Route::get('/knowledge/plan', ['as' => 'knowledge.plan', 'uses' => 'KnowledgeController@plan']);
Route::get('/knowledge/{slug}/image.svg', ['as' => 'knowledge.image', 'uses' => 'KnowledgeController@image']);
Route::get('/knowledge/{slug}', ['as' => 'knowledge.show', 'uses' => 'KnowledgeController@show']);
Route::get('/for-ai-agents', ['as' => 'for-ai-agents', 'uses' => 'KnowledgeController@forAiAgents']);

// Products
Route::get('/catalog', ['as' => 'catalog', 'uses' => 'ProductsController@index']);
Route::get('/product/{alias}', ['as' => 'product.show', 'uses' => 'ProductsController@show']);

//Articles
Route::get('/news', ['as' => 'articles', 'uses' => 'ArticlesController@index']);
Route::get('/news/{alias}', ['as' => 'article.show', 'uses' => 'ArticlesController@show']);

// Vacancies
Route::get('/vacancies', ['as' => 'vacancies', 'uses' => 'VacanciesController@index']);
Route::get('/vacancy/{alias}', ['as' => 'vacancy.show', 'uses' => 'VacanciesController@show']);

// Static
Route::get('/guarantee', ['as' => 'guarantee', 'uses' => 'MainController@guarantee']);
Route::get('/payment', ['as' => 'payment', 'uses' => 'MainController@payment']);
Route::get('/wholesale', ['as' => 'wholesale', 'uses' => 'MainController@wholesale']);
Route::get('/about', ['as' => 'about', 'uses' => 'MainController@about']);
Route::get('/contacts', ['as' => 'contacts', 'uses' => 'MainController@contacts']);

// Mail
Route::post('/subscribe', ['as' => 'subscribe', 'uses' => 'MainController@subscribe']);
Route::post('/message', ['as' => 'message', 'uses' => 'MainController@message']);
Route::post('/orders', ['as' => 'orders', 'uses' => 'MainController@order']);

Route::get('/', ['as' => 'home', 'uses' => 'MainController@index']);
