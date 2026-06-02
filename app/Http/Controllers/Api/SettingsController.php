<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    private function getSettings() {

        $list = Setting::get();

        $list = $list->map(function($m) {

            $value = $m->value;

            if ($m->type === Setting::TYPE_ARRAY) {
                $value = json_decode($value, true);
            }

            return [
                'key' => $m->key,
                'value' => $value,
            ];
        });

        return $list->pluck('value', 'key');
    }

    public function index()
    {
        $list = $this->getSettings();

        return response()->json($list);
    }

    public function show($key)
    {
        return Setting::getValue($key);
    }


    public function update(Request $request, $key = null)
    {
        if ($key === null) {

            $settings = $request->get('settings');

            foreach ($settings as $key => $value) {
                Setting::setValue($key, $value);
            }

            $list = $this->getSettings();
            return response()->json($list);

        } else {
            return Setting::setValue($key, $request->get('value'));
        }
    }

    public function slider(Request $request) {

        $slider = $request->get('slider');

        $prepareSlider = [];

        if ($slider && count($slider)) {

            foreach($slider as $key => $slide) {

                $image = $slide['image'];

                $fileIndex = sprintf('slider.%d.file', $key);

                if ($request->hasFile($fileIndex)) {
                    $image = $this->image($request->file($fileIndex));
                }

                $prepareSlider[] = [
                    'title' => $slide['title'],
                    'label' => $slide['label'],
                    'text' => $slide['text'],
                    'button' => $slide['button'],
                    'link' => $slide['link'],
                    'image' => $image,
                ];
            }

        } else {
            $prepareSlider = [];
        }

        Setting::setValue('slider', $prepareSlider);

        $list = $this->getSettings();
        return response()->json($list);
    }

    private function image($image) {

        $storage = Storage::disk('slider');
        $extension = $image->getClientOriginalExtension();

        $filename = sprintf('%s.%s', Str::random('12'), $extension);

        $storage->put($filename, file_get_contents($image->getRealPath()), 'public');
        return $storage->url($filename);
    }
}
