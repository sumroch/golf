<?php

namespace App\Http\Controllers;

use App\Http\Factories\SettingFactory;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.grandmaster.setting.index', [
            'settings' => SettingFactory::call(Setting::select('id', 'name', 'date_start', 'date_end')
                ->orderBy('id', 'asc')
                ->paginate(10)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.grandmaster.setting.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SettingRequest $request)
    {
        Setting::create($request->validated());

        return redirect()->route('setting.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($setting)
    {
        return view('admin.grandmaster.setting.edit', [
            'data' => Setting::findOrFail($setting),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request, $setting)
    {
        $setting = Setting::findOrFail($setting);
        $setting->update($request->validated());

        return redirect()->route('setting.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($setting)
    {
        $setting = Setting::findOrFail($setting);
        $setting->delete();

        return redirect()->route('setting.index');
    }
}
