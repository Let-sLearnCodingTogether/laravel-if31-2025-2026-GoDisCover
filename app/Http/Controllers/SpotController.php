<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Models\Category;
use App\Models\Spot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\call;
use function Pest\Laravel\get;

class SpotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $spot = Spot::with([
                'user:id,name',
                'categories:category,spot_id'
                ])
                ->withCount('reviews')
                ->withSum('reviews','rating')
                ->orderBy('created_at','desc')
                ->paginate(request('size',10));
                return Response::json([
                    'message' => 'List Berhasil',
                    'data' => null
                ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpotRequest $request)
    {
        try {
            $validated = $request->safe()->all();
            $picture_path = Storage::disk('public')->putFile('spots', $request->file('picture'));
            $validated['user_id'] = Auth::user()->id;
            $validated['picture'] = $picture_path;
            $spot = Spot::create($validated);
            if ($spot) {
                $categories = [];
                foreach ($validated['category'] as $category) {
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }
                Category::fillAndInsert($categories);
                return Response::json([
                    'message' => 'Spot Berhasil',
                    'data' => null
                ], 200);
            }
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Spot $spot)
    {
        try {
        return Response::json([
                    'message' => 'List Berhasil',
                    'data' => $spot->load([
                        'user:id,name',
                        'categories:category,spot_id'
                    ])
                    ->loadCount(['reviews'])
                    ->loadSum('reviews','rating')
                ], 200);
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spot $spot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpotRequest $request, Spot $spot)
    {
        try {
            $request = $request->safe()->all();
            //cek request ada picture
            if(isset($validated['picture'])){
                $picture_path = Storage::disk('public')->putFile('spots',$request->file('picture'));
            }
            //cek request ada category
            if(isset($validated['category'])){
                Category::where('spot_id',$spot->id)->delete();
                $categories = [];
                foreach ($validated['category'] as $category) {
                    $categories[] = [
                        'spot_id' => $spot->id,
                        'category' => $category
                    ];
                }
                Category::fillAndInsert($categories);
                
                $spot->update([
                    'name'=> $validated['name'],
                    'picture' => $picture_path ?? $spot->picture,
                    'address' => $validated['address']
                ]);
                return Response::json([
                    'message' => 'Spot Berhasil',
                    'data' => null
                ], 200);
            }
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spot $spot)
    {
        try{
            $user = Auth::user();
            if($spot->user_id == $user->id || $user->role == 'admin'){
                if($spot->delete()){
                    return Response::json([
                            'message' => 'Spot Berhasil dihapus',
                            'data' => null
                        ], 200);
                }
             } else {
                    return Response::json([
                            'message' => 'Spot Gagal Dihapus',
                            'data' => null
                        ], 200);
            }
        } catch (Exception $e) {
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}