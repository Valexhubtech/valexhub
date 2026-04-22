<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Wave\DemoRequest;
use Wave\Product;

class DemoRequestController extends Controller
{
    /**
     * Store a new demo request
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'referral_code' => 'nullable|string|max:50',
                'product_id' => 'required|exists:products,id',
                'request_type' => 'required|in:quote,demo',
            ]);

            // Find the product
            $product = Product::findOrFail($validated['product_id']);

            // Create the demo request
            $demoRequest = DemoRequest::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'referral_code' => $validated['referral_code'],
                'product_id' => $product->id,
                'request_type' => $validated['request_type'],
                'status' => 'pending',
            ]);

            // Generate WhatsApp URL
            $whatsappUrl = $demoRequest->generateWhatsAppUrl();

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully!',
                'whatsapp_url' => $whatsappUrl,
                'request_id' => $demoRequest->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Demo request failed:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    /**
     * Get all demo requests for admin (optional)
     */
    public function index()
    {
        $requests = DemoRequest::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.demo-requests.index', compact('requests'));
    }
}