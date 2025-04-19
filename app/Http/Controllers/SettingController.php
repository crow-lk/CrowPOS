<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.edit');
    }

    public function store(Request $request)
{
    $data = $request->except('_token');

    // Handle the image upload
    if ($request->hasFile('app_logo')) {
        $image_path = $request->file('app_logo')->store('products', 'public');
        // Save the image path in the data array
        $data['app_logo'] = $image_path; // Assuming 'app_logo' is the key for the image
    }

    foreach ($data as $key => $value) {
        $setting = Setting::firstOrCreate(['key' => $key]);
        $setting->value = $value;
        $setting->save();
    }

    return redirect()->route('settings.index');
}
}
