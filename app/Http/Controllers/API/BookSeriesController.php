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
            'name' => 'required|string|max:1048576',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $series = BookSeries::create($validator->validated());

        return response()->json(['message' => 'تم إنشاء السلسلة بنجاح', 'data' => $series], 201);
    }

    public function show($id)
    {
        $series = BookSeries::find($id);
        if (! $series) {
            return response()->json(['message' => 'السلسلة غير موجودة'], 404);
        }

        return response()->json($series);
    }

    public function update(Request $request, $id)
    {
        $series = BookSeries::find($id);
        if (! $series) {
            return response()->json(['message' => 'السلسلة غير موجودة'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:1048576',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $series->update($validator->validated());

        return response()->json(['message' => 'تم تحديث السلسلة بنجاح', 'data' => $series]);
    }

    public function destroy($id)
    {
        $series = BookSeries::find($id);
        if (! $series) {
            return response()->json(['message' => 'السلسلة غير موجودة'], 404);
        }

        // Ideally check if books exist before delete, or set null on books.
        // For now, strict delete.
        $series->delete();

        return response()->json(['message' => 'تم حذف السلسلة بنجاح']);
    }
}
