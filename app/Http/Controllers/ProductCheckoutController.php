<?php

namespace App\Http\Controllers;

use App\Services\Paystack\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Wave\Deployment;
use Wave\Product;
use Wave\UserProduct;

class ProductCheckoutController extends Controller
{
    public function initialize(Request $request, Product $product, PaystackService $paystack): RedirectResponse
    {
        abort_unless($product->isPrebuilt(), 404);

        $validated = $request->validate([
            'deploy_for' => 'required|in:self,client',
            'client_name' => 'required_if:deploy_for,client|nullable|string|max:255',
            'client_email' => 'required_if:deploy_for,client|nullable|email|max:255',
        ]);

        $amount = (float) ($product->low_price ?? $product->high_price ?? 0);
        abort_if($amount <= 0, 422, 'This product does not have a price configured.');

        $user = $request->user();

        // If this user already has an active deployment for this product, send
        // them straight to their deployments page — no duplicate purchase needed.
        $existingActive = UserProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', 'active')
            ->first();

        if ($existingActive) {
            return redirect()->route('dashboard.deployments')
                ->with('status', 'You already have an active deployment for this product.');
        }

        // Reuse an existing inactive record (abandoned previous checkout) or
        // create a fresh one — avoids hitting the unique (user_id, product_id) constraint.
        $userProduct = UserProduct::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['amount_paid' => $amount, 'purchase_date' => now(), 'status' => 'inactive'],
        );

        // Always create a fresh deployment record for this checkout attempt.
        $deployment = Deployment::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'user_product_id' => $userProduct->id,
            'deploy_for' => $validated['deploy_for'],
            'client_name' => $validated['client_name'] ?? null,
            'client_email' => $validated['client_email'] ?? null,
            'status' => 'pending',
        ]);

        $response = $paystack->initializeTransaction(
            email: $user->email,
            amountKobo: (int) round($amount * 100),
            metadata: [
                'type' => 'product_purchase',
                'deployment_id' => $deployment->id,
                'user_product_id' => $userProduct->id,
            ],
            callbackUrl: route('products.checkout.callback'),
        );

        if (! ($response['status'] ?? false)) {
            $deployment->delete();
            $userProduct->delete();

            return redirect()->route('dashboard.products')
                ->with('error', 'Unable to start checkout with Paystack. Please try again.');
        }

        return redirect()->to($response['data']['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack): RedirectResponse
    {
        $reference = $request->query('reference');
        $deploymentId = null;

        if ($reference) {
            $result = $paystack->verifyTransaction($reference);
            $deploymentId = $result['data']['metadata']['deployment_id'] ?? null;
        }

        if ($deploymentId) {
            $deployment = Deployment::where('id', $deploymentId)
                ->where('user_id', auth()->id())
                ->first();

            if ($deployment) {
                return redirect()->route('dashboard.deployments.show', ['deployment' => $deployment->id])
                    ->with('status', 'Payment received — your deployment is being set up.');
            }
        }

        return redirect()->route('dashboard.deployments')
            ->with('status', 'Payment received — your deployment is being provisioned.');
    }
}
