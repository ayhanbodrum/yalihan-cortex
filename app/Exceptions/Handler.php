<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Standart raporlama

            // Belgeleme için hatayı kaydet
            if (config('app.exception_docs', false)) {
                $this->documentException($e);
            }
        });
    }

    /**
     * Otomatik dokümantasyon için hata bilgilerini kaydet
     */
    protected function documentException(Throwable $exception): void
    {
        try {
            $exceptionData = [
                'date' => now()->format('Y-m-d H:i:s'),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'url' => request()->url(),
                'user_id' => auth()->id() ?? 'guest',
                'method' => request()->method(),
                'input' => request()->except(['password', 'password_confirmation', 'current_password']),
                'headers' => collect(request()->headers->all())
                    ->except(['cookie', 'authorization'])
                    ->toArray(),
            ];

            // Hataları JSON olarak kaydet
            $fileName = 'exceptions/'.date('Y-m-d').'/'.md5($exception->getMessage().$exception->getFile().$exception->getLine()).'.json';
            Storage::put($fileName, json_encode($exceptionData, JSON_PRETTY_PRINT));

            // Günlük raporuna ekle
            $this->addToExceptionReport($exceptionData);
        } catch (\Exception $e) {
            Log::error('Hata belgeleme sırasında sorun oluştu: '.$e->getMessage());
        }
    }

    /**
     * Günlük hata raporuna ekle
     */
    protected function addToExceptionReport(array $exceptionData): void
    {
        $reportPath = 'reports/exceptions/'.date('Y-m-d').'.md';

        $entryContent = '## Hata: '.date('H:i:s')."\n".
            '- **Mesaj:** '.$exceptionData['message']."\n".
            '- **Dosya:** '.$exceptionData['file'].':'.$exceptionData['line']."\n".
            '- **URL:** '.$exceptionData['url']."\n".
            '- **Metot:** '.$exceptionData['method']."\n\n";

        if (Storage::exists($reportPath)) {
            Storage::append($reportPath, $entryContent);
        } else {
            $header = '# Hata Raporu: '.date('Y-m-d')."\n\n";
            Storage::put($reportPath, $header.$entryContent);
        }
    }
}
