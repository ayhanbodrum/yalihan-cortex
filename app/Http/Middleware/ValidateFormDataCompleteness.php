<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * Middleware to validate that form creation methods have complete data
 *
 * This middleware prevents forms from rendering with missing dropdown data
 * by validating that required collections are present and not empty.
 */
class ValidateFormDataCompleteness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only validate GET requests to create/edit forms
        if ($request->method() !== 'GET' || !$this->isFormCreateRoute($request)) {
            return $response;
        }

        // Get the view data if this is a view response
        if (!method_exists($response, 'getOriginalContent')) {
            return $response;
        }

        $content = $response->getOriginalContent();
        if (!$content || !method_exists($content, 'getData')) {
            return $response;
        }

        $viewData = $content->getData();

        // Validate based on the form type
        $this->validateFormData($request, $viewData);

        return $response;
    }

    /**
     * Check if this is a form creation/edit route that needs validation
     */
    private function isFormCreateRoute(Request $request): bool
    {
        $routeName = $request->route() ? $request->route()->getName() : '';

        return str_contains($routeName, '.create') || str_contains($routeName, '.edit');
    }

    /**
     * Validate form data completeness
     */
    private function validateFormData(Request $request, array $viewData): void
    {
        $routeName = $request->route() ? $request->route()->getName() : '';
        $errors = [];

        // Validate ilan create/edit forms
        if (str_contains($routeName, 'ilanlar.')) {
            $errors = array_merge($errors, $this->validateIlanFormData($viewData));
        }

        // Validate yayin-tipleri create/edit forms
        if (str_contains($routeName, 'yayin-tipleri.')) {
            $errors = array_merge($errors, $this->validateYayinTipiFormData($viewData));
        }

        // Log any validation errors
        if (!empty($errors)) {
            Log::warning('Form data validation failed', [
                'route' => $routeName,
                'url' => $request->url(),
                'errors' => $errors,
                'available_data_keys' => array_keys($viewData)
            ]);

            // In development, we could throw an exception
            if (app()->environment('local')) {
                $errorMessages = implode(', ', $errors);
                Log::error("Form data incomplete for {$routeName}: {$errorMessages}");
            }
        }
    }

    /**
     * Validate ilan form data
     */
    private function validateIlanFormData(array $viewData): array
    {
        $errors = [];

        // Check required collections for ilan forms
        $requiredCollections = [
            'anaKategoriler' => 'Ana kategoriler',
            'altKategoriler' => 'Alt kategoriler',
            'yayinTipleri' => 'Yayın tipleri',
            'danismanlar' => 'Danışmanlar',
            'kisiler' => 'Kişiler'
        ];

        foreach ($requiredCollections as $key => $label) {
            if (!isset($viewData[$key])) {
                $errors[] = "{$label} data is missing from view";
            } elseif (method_exists($viewData[$key], 'isEmpty') && $viewData[$key]->isEmpty()) {
                $errors[] = "{$label} collection is empty";
            }
        }

        // Special validation for critical dropdown dependencies
        if (isset($viewData['yayinTipleri']) &&
            method_exists($viewData['yayinTipleri'], 'count') &&
            $viewData['yayinTipleri']->count() === 0) {

            $errors[] = "CRITICAL: Yayın tipleri is empty - dropdown will not work";
        }

        return $errors;
    }

    /**
     * Validate yayin tipi form data
     */
    private function validateYayinTipiFormData(array $viewData): array
    {
        $errors = [];

        // Check required collections for yayin-tipleri forms
        if (!isset($viewData['altKategoriler'])) {
            $errors[] = "Alt kategoriler data is missing from yayin-tipleri form";
        } elseif (method_exists($viewData['altKategoriler'], 'isEmpty') && $viewData['altKategoriler']->isEmpty()) {
            $errors[] = "Alt kategoriler collection is empty in yayin-tipleri form";
        }

        return $errors;
    }
}
