<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoriesImport;
use App\Exports\CategoriesExport;
use Spatie\Permission\Middleware\PermissionMiddleware;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(PermissionMiddleware::class . ':category-manage')->only([
            'store',
            'update',
            'destroy',
            'import',
        ]);
    }

    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return new CategoryResource($category);
    }

   
    public function show(Category $category)
{
    return new CategoryResource($category);
}


public function update(StoreCategoryRequest $request, Category $category)
{
    $category->update($request->validated());
    return new CategoryResource($category);
}

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category because it has associated posts.'
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.'
        ], 200);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            Excel::import(new CategoriesImport, $request->file('file'));
            return response()->json(['message' => 'Categories imported']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Import failed', 'details' => $e->getMessage()], 500);
        }
    }

    public function export()
{
    return Excel::download(new CategoriesExport, 'categories.xlsx');
}
}
