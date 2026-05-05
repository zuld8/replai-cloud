<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket\TicketCategory;
use App\Observers\Ticket\TicketCategoryObserver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TicketCategoryController extends Controller
{
    protected $categoryObserver;

    public function __construct(TicketCategoryObserver $categoryObserver)
    {
        $this->categoryObserver = $categoryObserver;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = $this->categoryObserver->getData($request);


            $perPage = $request->get('per_page', 20);
            $page = $request->get('page', 1);
            
            $categories = $query->orderBy('name', 'asc')
                              ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories->items(),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                        'has_more' => $categories->hasMorePages()
                    ]
                ],
                'message' => 'Categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getOptions(Request $request): JsonResponse
    {
        try {
            $categories = $this->categoryObserver->getOptions($request);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'ok' => true,
                'message' => 'Category options retrieved successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category options',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:ticket_categories,name',
                'slug' => 'nullable|string|max:255|unique:ticket_categories,slug',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create category using observer
            $category = $this->categoryObserver->createData($request);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category created successfully'
            ], 201);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $category = TicketCategory::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function edit(Request $request, $id): JsonResponse
    {
        try {
            $category = TicketCategory::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:ticket_categories,name,' . $id,
                'slug' => 'nullable|string|max:255|unique:ticket_categories,slug,' . $id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update category using observer
            $this->categoryObserver->updateData($request, $category);

            // Refresh the model to get updated data
            $category->refresh();

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category updated successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $category = TicketCategory::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|integer|exists:ticket_categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ids = $request->get('ids');
            

            $deletedCount = TicketCategory::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} categories deleted successfully",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete categories',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            // $validator = Validator::make($request->all(), [
            //     'query' => 'required|string|min:1|max:255'
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Validation failed',
            //         'errors' => $validator->errors()
            //     ], 422);
            // }

            $categories = $this->categoryObserver->search($request);

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories->items(),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                        'has_more' => $categories->hasMorePages()
                    ]
                ],
                'message' => 'Search completed successfully'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search categories',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
