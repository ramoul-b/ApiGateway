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

class LanguageController extends Controller
{

    /**
     * List all languages.
     * 
     * @OA\Get(
     *     path="/api/v1/languages",
     *     summary="List all languages",
     *     tags={"Languages"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of languages."
     *     )
     * )
     */
    public function index()
    {
        try {
            $languages = LanguageResource::collection(Language::all());
            return ApiService::response($languages);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Error retrieving languages'], 500);
        }
    }

    /**
 * @OA\Post(
 *     path="/api/v1/languages",
 *     summary="Create a new language",
 *     tags={"Languages"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="English"),
 *             @OA\Property(property="iso_639_code", type="string", example="en"),
 *             @OA\Property(property="flag", type="string", example="url_to_flag_image")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Language created successfully.",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid input data."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error occurred while creating the language."
 *     )
 * )
 */
public function store(Request $request)
{
    // Define your validation rules
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'iso_639_code' => 'required|string|max:10|unique:languages,iso_639_code',
        'flag' => 'nullable|string|max:255',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    // Handle the language creation process within a try-catch block
    try {
        // Create the new language using the validated data
        $language = Language::create($validator->validated());

        // Return a successful response with the created language data
        return ApiService::response(new LanguageResource($language), 201);
    } catch (\Exception $e) {
        // If an exception occurs, log it and return an error response
        return ApiService::response(['error' => 'Language creation failed'], 500);
    }
}


    /**
 * @OA\Put(
 *     path="/api/v1/languages/{id}",
 *     summary="Update an existing language",
 *     tags={"Languages"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The id of the language to update",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Object containing updated language information",
 *         @OA\JsonContent(
 *             required={"name","iso_639_code"},
 *             @OA\Property(property="name", type="string", example="French"),
 *             @OA\Property(property="iso_639_code", type="string", example="fr"),
 *             @OA\Property(property="flag", type="string", example="url_to_flag_image")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Language updated successfully.",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="name",
 *                     type="array",
 *                     @OA\Items(type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Language not found."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error occurred while updating the language."
 *     )
 * )
 */
public function update(Request $request, $id)
{
    $language = Language::find($id);

    if (!$language) {
        return ApiService::response(['error' => 'Language not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'iso_639_code' => 'required|string|max:10|unique:languages,iso_639_code,' . $id,
        'flag' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return ApiService::response($validator->errors(), 422);
    }

    try {
        $language->update($validator->validated());
        return ApiService::response(new LanguageResource($language), 200);
    } catch (\Exception $e) {
        return ApiService::response(['error' => 'Language update failed'], 500);
    }
}

/**
 * @OA\Delete(
 *     path="/api/v1/languages/{id}",
 *     summary="Delete a specific language",
 *     tags={"Languages"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the language to delete",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Language deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Language deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Language not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Language not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Language deletion failed.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Language deletion failed")
 *         )
 *     )
 * )
 */


    public function destroy($id)
    {
        try {
            $language = Language::find($id);
            if (!$language) {
                return ApiService::response(['error' => 'Language not found'], 404);
            }

            $language->delete();
            return ApiService::response(['message' => 'Language deleted successfully'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Language deletion failed'], 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/v1/languages/{iso_639_code}/{type}/content",
 *     summary="Retrieve file content by language code and type",
 *     tags={"Languages"},
 *     @OA\Parameter(
 *         name="iso_639_code",
 *         in="path",
 *         required=true,
 *         description="ISO 639 language code",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="path",
 *         required=true,
 *         description="Type of the content (e.g., 'frontend')",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful retrieval of file content.",
 *         @OA\MediaType(
 *             mediaType="text/plain"
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error retrieving file content."
 *     )
 * )
 */

 public function getContent(Request $request, $iso_639_code, $type)
 {
     try {
         $language = Language::where('iso_639_code', $iso_639_code)->first();
 
         if (!$language) {
             return ApiService::response(['error' => 'Language not found'], 404);
         }
 
         $file = $language->files()->where('type', $type)->first();
 
         if (!$file) {
             return ApiService::response(['error' => 'File not found'], 404);
         }
 
         $path = storage_path('app/public/' . $file->path_file); // Assurez-vous que le chemin est correct
         if (!file_exists($path)) {
             return ApiService::response(['error' => 'File does not exist'], 404);
         }
 
         $content = file_get_contents($path);
 
         return ApiService::response($content, 200);
     } catch (\Exception $e) {
         return ApiService::response(['error' => 'Error retrieving file content'], 500);
     }
 }
 
/**
 * @OA\Get(
 *     path="/api/v1/languages/{iso_639_code}/{type}/content/check/{md5}",
 *     summary="Check for new content based on MD5 hash",
 *     tags={"Languages"},
 *     @OA\Parameter(
 *         name="iso_639_code",
 *         in="path",
 *         required=true,
 *         description="ISO 639 language code",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="path",
 *         required=true,
 *         description="Type of the content to check (e.g., 'frontend')",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="md5",
 *         in="path",
 *         required=true,
 *         description="MD5 hash of the current file content",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New content available.",
 *         @OA\MediaType(
 *             mediaType="text/plain"
 *         )
 *     ),
 *     @OA\Response(
 *         response=202,
 *         description="No new content available."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error checking for new content."
 *     )
 * )
 */

    public function checkForNewContent(Request $request, $iso_639_code, $type, $md5)
    {
        try {
            // Check if a new file is available based on the MD5 hash
            // This is a placeholder - implement the actual logic for MD5 checking
            $fileIsNew = false; // Placeholder for actual check
            
            if ($fileIsNew) {
                // Return the new file's content
                return ApiService::response('New file content here', 200);
            } else {
                // Return a 202 Accepted status to indicate no new content is available
                return ApiService::response(null, 202);
            }
        } catch (\Exception $e) {
            return ApiService::response(['error' => 'Error checking for new content'], 500);
        }
    }
}
