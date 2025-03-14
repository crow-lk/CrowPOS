<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = new Service();
        if ($request->search) {
            $services = $services->where('name', 'LIKE', "%{$request->search}%");
        }
        $services = $services->latest()->paginate(10);
        if (request()->wantsJson()) {
            return ServiceResource::collection($services);
        }
        return view('services.index')->with('services', $services);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('services', 'public');
        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$service) {
            return redirect()->back()->with('error', __('service.error_creating'));
        }
        return redirect()->route('services.index')->with('success', __('service.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(service $service)
    {
        return view('services.edit')->with('service', $service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceUpdateRequest $request, service $service)
    {
        $service->name = $request->name;
        $service->description = $request->description;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($service->image) {
                Storage::delete($service->image);
            }
            // Store image
            $image_path = $request->file('image')->store('services', 'public');
            // Save to Database
            $service->image = $image_path;
        }

        if (!$service->save()) {
            return redirect()->back()->with('error', __('service.error_updating'));
        }
        return redirect()->route('services.index')->with('success', __('service.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::delete($service->image);
        }
        $service->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
