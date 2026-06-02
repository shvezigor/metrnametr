<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use App\Models\Setting;

class VacanciesController extends Controller
{
    public function index(Request $request) {
        $list = Vacancy::published()->orderBy('created_at', 'DESC')->paginate(8);

        if (Setting::existValue('phones')) {
            $lastPhone = explode(',', Setting::getValue('phones'));
            $phone = end($lastPhone);
        } else {
            $phone = '';
        }

        return view('client.vacancies.index')
            ->with('list', $list)
            ->with('title', 'Вакансії')
            ->with('phone', $phone);
    }

    public function show($alias) {
        $record = Vacancy::published()->where('alias', $alias)->first();

        if ($record === null) {
            abort(404);
        }

        return view('client.vacancies.show')
            ->with('record', $record)
            ->with('title', 'Вакансії');
    }
}
