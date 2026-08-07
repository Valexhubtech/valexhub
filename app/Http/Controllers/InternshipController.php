<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Wave\InternshipApplication;
use Wave\InternshipSession;

class InternshipController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $session = InternshipSession::getActive();

        if (! $session || ! $session->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Applications are not currently open. Please check back later.',
            ], 422);
        }

        try {
            $validated = $request->validate([
                'first_name'      => 'required|string|max:100',
                'last_name'       => 'required|string|max:100',
                'email'           => 'required|email|max:255',
                'phone'           => 'required|string|max:20',
                'institution'     => 'required|string|max:255',
                'course'          => 'required|string|max:255',
                'graduation_year' => 'required|digits:4|integer|min:2020|max:2035',
                'role'            => 'required|string|in:'.implode(',', $session->roles),
                'cover_letter'    => 'nullable|string|max:5000',
                'cv'              => 'required|file|mimes:pdf,doc,docx|max:5120',
                'portfolio_url'   => 'nullable|url|max:500',
                'linkedin_url'    => 'nullable|url|max:500',
            ]);

            $cvPath = $request->file('cv')->store(
                'internship-cvs/'.$session->slug,
                'local'
            );

            InternshipApplication::create([
                'internship_session_id' => $session->id,
                'first_name'            => $validated['first_name'],
                'last_name'             => $validated['last_name'],
                'email'                 => $validated['email'],
                'phone'                 => $validated['phone'],
                'institution'           => $validated['institution'],
                'course'                => $validated['course'],
                'graduation_year'       => $validated['graduation_year'],
                'role'                  => $validated['role'],
                'cover_letter'          => $validated['cover_letter'] ?? null,
                'cv_path'               => $cvPath,
                'portfolio_url'         => $validated['portfolio_url'] ?? null,
                'linkedin_url'          => $validated['linkedin_url'] ?? null,
                'status'                => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully! We\'ll be in touch.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Internship application failed:', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function downloadCv(InternshipApplication $application): mixed
    {
        abort_unless(Storage::disk('local')->exists($application->cv_path), 404);

        return Storage::disk('local')->download(
            $application->cv_path,
            $application->full_name.'_CV.'.pathinfo($application->cv_path, PATHINFO_EXTENSION)
        );
    }
}
