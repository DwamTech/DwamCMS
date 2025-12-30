<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BookSeries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookSeriesController extends Controller
{
    public function index()
    {
        // For admin dropdowns
        return response()->json(BookSeries::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $series = BookSeries::create($validator->validated());

        return response()->json(['message' => 'تم إنشاء السلسلة بنجاح', 'data' => $series], 201);
    }
}
