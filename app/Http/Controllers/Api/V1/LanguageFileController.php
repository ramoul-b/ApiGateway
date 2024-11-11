<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\LanguageFile;
use App\Services\ApiService;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\LanguageFileResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LanguageFileController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/v1/languages-files/{languageId}/files",
     *     summary="List all files for a specific language",
     *     tags={"LanguageFiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="languageId",
     *         in="path",
     *         required=true,
     *         description="Language ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of files for the specified language."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving language files."
     *     )
     * )
     */
    public function index($languageId)
    {
        try {
            $languageFiles = LanguageFile::where('language_id', $languageId)->get();
            return ApiService::response(LanguageFileResource::collection($languageFiles));
        } catch (\Exception $e) {
            Log::error('Error in LanguageFile creation', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString() 
            ]);
            return ApiService::response(['error' => 'File creation failed'], 500);
        }        
    }

    /**
     * @OA\Post(
     *     path="/api/v1/languages-files/{languageId}/files",
     *     summary="Store a new file for a specific language",
     *     tags={"LanguageFiles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="languageId",
     *         in="path",
     *         required=true,
     *         description="Language ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="Type of the file (e.g., 'frontend')"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="File created successfully."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while creating the file."
     *     )
     * )
     */
    public function store(Request $request, $languageId)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|file',
        'type' => 'required|string',
    ]);

    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    try {
        // Vérifie si un fichier avec le même language_id et type existe déjà
        $existingFile = LanguageFile::where('language_id', $languageId)->where('type', $request->type)->first();
        if ($existingFile) {
            return ApiService::response(['error' => 'A file for the specified language and type already exists.'], 409); // 409 Conflict
        }

        $file = $request->file('file');
        $path = Storage::disk('public')->putFile('language_files', $file);
        $md5 = md5_file($file->getRealPath());

        $languageFile = new LanguageFile([
            'language_id' => $languageId,
            'path_file' => $path,
            'type' => $request->type,
            'md5_path_file' => $md5,
        ]);
        $languageFile->save();

        return ApiService::response(new LanguageFileResource($languageFile), 201);
    } catch (\Exception $e) {
        Log::error('Error while creating file', [
            'language_id' => $languageId,
            'error' => $e->getMessage(),
            'stackTrace' => $e->getTraceAsString(),
            'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file',
            'type' => $request->input('type', 'No type provided')
        ]);
        return ApiService::response(['error' => 'File creation failed'], 500);
    }
}


/**
 * @OA\Get(
 *     path="/api/v1/languages-files/{languageId}/files/{fileId}",
 *     summary="Show details of a specific file for a language",
 *     tags={"LanguageFiles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="languageId",
 *         in="path",
 *         required=true,
 *         description="Language ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         required=true,
 *         description="File ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful retrieval of the language file details.",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Language file not found."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error retrieving the language file details."
 *     )
 * )
 */
public function show($languageId, $fileId)
{
    try {
        $languageFile = LanguageFile::where('language_id', $languageId)->findOrFail($fileId);
        return ApiService::response(new LanguageFileResource($languageFile));
    } catch (\Exception $e) {
        return ApiService::response(['error' => 'Language file not found'], 404);
    }
}


/**
 * @OA\Post(
 *     path="/api/v1/languages-files/{languageId}/files/{fileId}",
 *     summary="Update a specific file for a language",
 *     tags={"LanguageFiles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="languageId",
 *         in="path",
 *         required=true,
 *         description="Language ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         required=true,
 *         description="File ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Object containing the file to update and its type",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"file", "type"},
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="New file to upload"
 *                 ),
 *                 @OA\Property(
 *                     property="type",
 *                     type="string",
 *                     example="frontend",
 *                     description="Type of the file (e.g., 'frontend')"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File updated successfully."
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid input data."
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Language file not found."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error occurred while updating the file."
 *     )
 * )
 */
public function update(Request $request, $languageId, $fileId)
{
    Log::info("Updating language file", ['languageId' => $languageId, 'fileId' => $fileId]);

    try {
        $languageFile = LanguageFile::where('language_id', $languageId)->findOrFail($fileId);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed", $validator->errors()->toArray());
            return ApiService::response($validator->errors(), 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::info("Received file for update", ['fileName' => $file->getClientOriginalName()]);

            // Supprimer l'ancien fichier si nécessaire
            $oldFilePath = $languageFile->path_file;
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info("Deleted old file", ['oldFilePath' => $oldFilePath]);
            } else {
                Log::warning("Old file does not exist or could not be deleted", ['oldFilePath' => $oldFilePath]);
            }

            // Télécharger le nouveau fichier
            $path = Storage::disk('public')->putFile('language_files', $file);
            if ($path) {
                Log::info("Uploaded new file", ['path' => $path]);
            } else {
                Log::error("Failed to upload new file");
                return ApiService::response(['error' => 'File upload failed'], 500);
            }
            
            $md5 = md5_file($file->getRealPath());

            // Mise à jour des informations dans la base de données
            $languageFile->update([
                'path_file' => $path,
                'type' => $request->type,
                'md5_path_file' => $md5,
            ]);
            Log::info("Updated language file in database", ['languageFileId' => $languageFile->id]);
        }

        return ApiService::response(new LanguageFileResource($languageFile), 200);
    } catch (\Exception $e) {
        Log::error('Error while updating file', [
            'language_id' => $languageId,
            'file_id' => $fileId,
            'error' => $e->getMessage(),
            'stackTrace' => $e->getTraceAsString(),
        ]);
        return ApiService::response(['error' => 'File update failed'], 500);
    }
}




/**
 * @OA\Delete(
 *     path="/api/v1/languages-files/{languageId}/files/{fileId}",
 *     summary="Delete a specific file for a language",
 *     tags={"LanguageFiles"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="languageId",
 *         in="path",
 *         required=true,
 *         description="Language ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="fileId",
 *         in="path",
 *         required=true,
 *         description="File ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="File deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Language file not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Language file not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error occurred while deleting the file.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="File deletion failed")
 *         )
 *     )
 * )
 */
public function destroy($languageId, $fileId)
{
    try {
        $languageFile = LanguageFile::where('language_id', $languageId)->findOrFail($fileId);

        Storage::delete($languageFile->path_file);

        $languageFile->delete();
        return ApiService::response(['message' => 'File deleted successfully'], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return ApiService::response(['error' => 'Language file not found'], 404);
    } catch (\Exception $e) {
        Log::error("Failed to delete language file: {$e->getMessage()}");
        return ApiService::response(['error' => 'File deletion failed'], 500);
    }
}


}
