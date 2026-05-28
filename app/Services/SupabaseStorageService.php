<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SupabaseStorageService
 *
 * Handles file uploads to Supabase Storage buckets.
 * Used primarily for trade screenshot uploads.
 */
class SupabaseStorageService
{
    protected string $baseUrl;
    protected string $serviceRoleKey;
    protected string $bucket;

    public function __construct()
    {
        $this->baseUrl = config('supabase.url');
        $this->serviceRoleKey = config('supabase.service_role_key');
        $this->bucket = config('supabase.storage_bucket');
    }

    /**
     * Upload a file to Supabase Storage.
     *
     * @param UploadedFile $file     The uploaded file
     * @param string       $folder   Subfolder path (e.g. "user-uuid/2024-01-15")
     * @return array{success: bool, url?: string, error?: string}
     */
    public function upload(UploadedFile $file, string $folder = ''): array
    {
        try {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = trim("{$folder}/{$filename}", '/');

            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->serviceRoleKey,
                'Authorization' => "Bearer {$this->serviceRoleKey}",
                'Content-Type' => $file->getMimeType(),
            ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post("{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$path}");

            if ($response->successful()) {
                $publicUrl = "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$path}";

                return [
                    'success' => true,
                    'url' => $publicUrl,
                ];
            }

            $error = $response->json('message') ?? 'Upload failed.';
            Log::warning('Supabase storage upload failed', ['error' => $error, 'path' => $path]);

            return ['success' => false, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('Supabase storage upload exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Upload error. Please try again.'];
        }
    }

    /**
     * Delete a file from Supabase Storage.
     *
     * @param string $filePath  The full path within the bucket
     * @return bool
     */
    public function delete(string $filePath): bool
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'apikey' => $this->serviceRoleKey,
                'Authorization' => "Bearer {$this->serviceRoleKey}",
            ])->delete("{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$filePath}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Supabase storage delete exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
