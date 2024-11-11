<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ApiCondition;

class CheckApiPermission
{
    public function handle(Request $request, Closure $next)
    {
        // Log the incoming API request path and method for debugging purposes
        $path = $request->path();
        $method = $request->method();
        Log::info('API Request', ['path' => $path, 'method' => $method]);

        // Retrieve the API route information from the database
        $api = Api::where('route_in', $path)->where('method', $method)->first();

        // If API route is not found, log a warning and return a 404 response
        if (!$api) {
            Log::warning('API not found', ['path' => $path, 'method' => $method]);
            return response()->json(['message' => 'API not found'], 404);
        }

        // Retrieve the API conditions associated with the route
        // Delete This <--------!!!!!!
        $apiConditions = $api->conditions;

        // Check if the user has the required permissions for accessing the API
        $permissionGranted = $this->checkPermissionsForApi($api->id);

        // If user does not have the required permissions, log a warning and return a 403 response
        if (!$permissionGranted) {
            Log::warning('Unauthorized access attempt', ['api' => $path]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // If permissions are granted, proceed with the request
        return $next($request);
    }

    function checkPermissionsForApi($apiId)
    {
        // Fetch all conditions for the given api_id
        $conditions = ApiCondition::where('api_id', $apiId)->get();
        
        // Initialize the result
        $checkLevels = true;
    
        // Group conditions by level
        $groupedConditions = $conditions->groupBy('level');
    
        // Iterate through each level and check permissions
        foreach ($groupedConditions as $level => $conditionsForLevel) {
            // Use condition_level to determine the logical operator between levels
            if ($level > 1) {
                $logicalOperator = $conditionsForLevel->first()->condition_level;
                if ($logicalOperator === 'OR') {
                    $checkLevels = $checkLevels || $this->checkPermissionsForLevel($conditionsForLevel);
                } else {
                    $checkLevels = $checkLevels && $this->checkPermissionsForLevel($conditionsForLevel);
                }
            } else {
                $checkLevels = $checkLevels && $this->checkPermissionsForLevel($conditionsForLevel);
            }
        }
    
        return $checkLevels;
    }
    
    
    function checkPermissionsForLevel($conditionsForLevel)
    {
        $result = true;
    
        // Iterate through conditions
        foreach ($conditionsForLevel as $condition) {
            $permissionId = $condition->permission_id;
    
            if ($condition->condition_object === 'hasPermission') {
                $permissionGranted = $this->hasPermission($permissionId);
            } elseif ($condition->condition_object === 'hasNotPermission') {
                $permissionGranted = $this->hasNotPermission($permissionId);
            } else {
                $permissionGranted = false;
            }
    
            // Apply the condition operator
            if ($condition->condition === 'AND') {
                $result = $result && $permissionGranted;
            } elseif ($condition->condition === 'OR') {
                $result = $result || $permissionGranted;
            } else { // No explicit condition, default to permission granted
                $result = $permissionGranted;
            }
        }
    
        return $result;
    }
 
    
    public function hasPermission($permissionId)
    {
        // Récupère l'utilisateur actuel avec le compte utilisé
        $user = Auth::user();

        // Vérifie si l'utilisateur a la permission avec l'ID donné
        // en examinant les rôles associés à l'utilisateur via le compte utilisé
        foreach ($user->accountUsing()->first()->role->permissions as $permission) {
            if ($permission->id == $permissionId) {
                return true;
            }
        }

        return false;
    }

    public function hasNotPermission($permissionId)
    {
        // Récupère l'utilisateur actuel avec le compte utilisé
        $user = Auth::user();

        // Vérifie si l'utilisateur n'a pas la permission avec l'ID donné
        // en examinant les rôles associés à l'utilisateur via le compte utilisé
        foreach ($user->accountUsing()->first()->role->permissions as $permission) {
            if ($permission->id == $permissionId) {
                return false;
            }
        }

        return true;
    }

}