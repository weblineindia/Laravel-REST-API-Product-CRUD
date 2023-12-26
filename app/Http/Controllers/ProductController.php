<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use Validator;

/**
 * Author Name  :  WeblineIndia  |  https://www.weblineindia.com/
 * 
 * For more such software development components and code libraries, visit us at
 * https://www.weblineindia.com/communities.html
 * 
 * Our Github URL : https://github.com/weblineindia
 **/

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get a list of products",
     *     tags={"Products"},
     *     @OA\Response(response="200", description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))),
     *     @OA\Response(response="404", description="No products found"),
     *     @OA\Response(response="500", description="Error retrieving products")
     * )
     */
    public function index()
    {
        // Define empty array variable
        $emptyData = (object)[];
        try {
            // Retrieve all products from the database
            $products = Product::all();

            // Check if products exist
            if ($products->isEmpty()) {
                // Return a JSON response with a 404 status code and message
                return response()->json(['message' => 'No products found', 'data' => $emptyData], 404);
            }

            // Return a JSON response with a 200 status code, success message, and data
            return response()->json(['message' => 'Products retrieved successfully', 'data' => $products], 200);

        } catch (\Exception $e) {
            // Return a JSON response with a 500 status code and error message
            return response()->json(['message' => $e->getMessage(), 'data' => $emptyData], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product retrieved successfully", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="404", description="Product not found"),
     *     @OA\Response(response="500", description="Error retrieving product")
     * )
     */
    public function show($id)
    {
        // Define empty array variable
        $emptyData = (object)[];
        try {
            // Find the product by ID
            $product = Product::find($id);

            // Check if product exists
            if (!$product) {
                // Return a JSON response with a 404 status code and message
                return response()->json(['message' => 'Product not found', 'data' => $emptyData], 404);
            }

            // Return a JSON response with a 200 status code, success message, and data
            return response()->json(['message' => 'Product retrieved successfully', 'data' => $product], 200);

        } catch (\Exception $e) {
            // Return a JSON response with a 500 status code and error message
            return response()->json(['message' => $e->getMessage(), 'data' => $emptyData], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product data",
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(response="201", description="Product created successfully", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="errors", type="object"))),
     *     @OA\Response(response="500", description="Error creating product", @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="error", type="string")))
     * )
     */
    public function store(Request $request)
    {
        // Define empty array variable
        $emptyData = (object)[];
        try {

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'desc' => 'required|string',
                'status' => 'required|in:Publish,Draft',
                'date' => 'required|date',
                'category' => 'required|string|max:255',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->messages()->first(),
                    'data' => []
                ], 422); // 422 Unprocessable Entity
            }

            // Start a database transaction
            DB::beginTransaction();

            // Create a new product with the validated data
            $product = Product::create($request->all());

            // Commit the database transaction
            DB::commit();

            // Return a JSON response with a 201 status code, success message, and data
            return response()->json(['message' => 'Product created successfully', 'data' => $product], 201);

        } catch (\Exception $e) {
            // Rollback the database transaction in case of an error
            DB::rollBack();

            // Return a JSON response with a 500 status code and error message
            return response()->json(['message' => $e->getMessage(),'data' => $emptyData], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated product data",
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(response="200", description="Product updated successfully", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response="404", description="Product not found"),
     *     @OA\Response(response="422", description="Validation error", @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="errors", type="object"))),
     *     @OA\Response(response="500", description="Error updating product", @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="error", type="string")))
     * )
     */
    public function update(Request $request, $id)
    {
        // Define empty array variable
        $emptyData = (object)[];
        try {
            // Find the product by ID
            $product = Product::find($id);

            // Check if product exists
            if (!$product) {
                // Return a JSON response with a 404 status code and message
                return response()->json(['message' => 'Product not found','data' => $emptyData], 404);
            }

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'desc' => 'required|string',
                'status' => 'required|in:Publish,Draft',
                'date' => 'required|date',
                'category' => 'required|string|max:255',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->messages()->first(),
                    'data' => $emptyData
                ], 422); // 422 Unprocessable Entity
            }

            // Start a database transaction
            DB::beginTransaction();

            // Update the product with the validated data
            $product->update($request->all());

            // Commit the database transaction
            DB::commit();

            // Return a JSON response with a 200 status code, success message, and data
            return response()->json(['message' => 'Product updated successfully', 'data' => $product], 200);

        } catch (\Exception $e) {
            // Rollback the database transaction in case of an error
            DB::rollBack();

            // Return a JSON response with a 500 status code and error message
            return response()->json(['message' => $e->getMessage(),'data' => $emptyData], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product deleted successfully"),
     *     @OA\Response(response="404", description="Product not found"),
     *     @OA\Response(response="500", description="Error deleting product")
     * )
     */
    public function destroy($id)
    {
        // Define empty array variable
        $emptyData = (object)[];
        try {
            // Find the product by ID
            $product = Product::find($id);

            // Check if product exists
            if (!$product) {
                // Return a JSON response with a 404 status code and message
                return response()->json(['message' => 'Product not found','data' => $emptyData], 404);
            }

            // Start a database transaction
            DB::beginTransaction();

            // Delete the product
            $product->delete();

            // Commit the database transaction
            DB::commit();

            // Return a JSON response with a 200 status code and success message
            return response()->json(['message' => 'Product deleted successfully','data' => $emptyData], 200);

        } catch (\Exception $e) {
            // Rollback the database transaction in case of an error
            DB::rollBack();

            // Return a JSON response with a 500 status code and error message
            return response()->json(['message' => $e->getMessage(),'data' => $emptyData], 500);
        }
    }
}
