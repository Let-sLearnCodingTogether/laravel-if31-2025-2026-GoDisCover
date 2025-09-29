<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreReviewRequest $request)
    {
        try{
            $validated = $request->safe()->all();
            $validated['user_id'] = Auth::user()->id;
            $response = Review::create($validated);

            if($response){
                return Response::json([
                    'message' => 'review berhasil dibuat',
                    'data' => null
                ],200);
            }
            return Response::json([
                    'message' => 'review gagal dibuat',
                    'data' => null
                ],500);
        } catch (Exception $e){
            return Response::json([
                    'message' => $e->getMessage(),
                    'data' => null
                ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        try{
            if($review ->delete()){
                return Response::json([
                    'message' => "Review Berhasil Dihapus",
                    'data' => null
                ],200);
            }
            return Response::json([
                'message' => 'Review Gagal Dihapus',
                'data' => null
                ],500);
        } catch (Exception $e){
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
