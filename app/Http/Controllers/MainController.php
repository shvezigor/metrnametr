<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Events\OrderCreated;
use App\Events\Subscribe;
use App\Models\Article;
use App\Models\Catalog;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\Order;
use App\Models\Message;
use App\Models\Setting;
use App\Support\SeoContent;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index() {

        $products = Product::published()->orderBy('created_at', 'DESC')->limit(8)->get();
        $articles = Article::published()->orderBy('created_at', 'DESC')->limit(9)->get();

        $slides = Product::published()->slider()->whereHas('categories', function($q) {
            $q->where('published', 1)->whereHas('catalog', function($q) {
                $q->where('published', 1);
            });
        })->get();

        $title = Setting::getValue('meta_title');
        $keywords = Setting::getValue('meta_keywords');
        $description = Setting::getValue('meta_description');

        return view('client.main.index')
            ->with('products', $products)
            ->with('articles', $articles)
            ->with('title', $title)
            ->with('keywords', $keywords)
            ->with('description', $description)
            ->with('faq', SeoContent::faq('home_faq'))
            ->with('schema', [
                SeoContent::organizationSchema(),
                SeoContent::websiteSchema(),
                SeoContent::localBusinessSchema(),
                SeoContent::faqSchema(SeoContent::faq('home_faq')),
            ])
            ->with('slides', $slides);
    }

    public function contacts() {
        return view('client.main.contacts')
            ->with('title', 'Контакти')
            ->with('description', 'Контакти компанії Метр на Метр: адреса, телефони, email і форма для звернення щодо підбору та монтажу дверей.')
            ->with('schema', [
                SeoContent::localBusinessSchema(),
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    '/contacts' => 'Контакти',
                ]),
            ])
            ->with('breadcrumbs', [route('contacts') => 'Контакти']);
    }

    public function guarantee() {
        return view('client.main.guarantee')
            ->with('title', 'Гарантія')
            ->with('breadcrumbs', [route('guarantee') => 'Гарантія']);
    }

    public function payment() {
        return view('client.main.payment')
            ->with('title', 'Оплата і доставка')
            ->with('breadcrumbs', [route('payment') => 'Оплата і доставка']);
    }

    public function about() {
        return view('client.main.about')
            ->with('title', 'Про нас')
            ->with('breadcrumbs', [route('about') => 'Про нас']);
    }

    public function wholesale() {
        return view('client.main.wholesale')
            ->with('title', 'Оптовий продаж')
            ->with('breadcrumbs', [route('wholesale') => 'Оптовий продаж']);
    }

    public function subscribe(Request $request) {

        $this->validate($request, [
            'email' => 'required|email|unique:subscribers',
        ]);

        $subscriber = Subscriber::create([
            'email' => $request->get('email'),
        ]);

        // Send mail
        event(new Subscribe($subscriber));

        return redirect()->back()->with('success', 'Ви успішно підписались !');
    }

    public function order(Request $request) {

        $this->validate($request, [
            'phone' => 'required',
        ]);

        $order = Order::create([
            'name' => $request->get('name'),
            'phone' => $request->get('phone'),
        ]);

        if ($request->has('product')) {
            $order->update([
                'product_id' => $request->get('product'),
            ]);
        }

        // Send mail
        event(new OrderCreated($order));

        return redirect()->back()->with('success', 'Ваше повідомлення відправлено успішно !');
    }

    public function message(Request $request) {

        $this->validate($request, [
            'email' => 'required|email',
            'text' => 'required',
        ]);

        $email = Message::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'title' => $request->get('title'),
            'text' => $request->get('text')
        ]);

        $email->save();

        // Send mail
        event(new MessageCreated($email));

        return redirect()->back()->with('success', 'Ваше повідомлення відправлено успішно!');
    }

}
