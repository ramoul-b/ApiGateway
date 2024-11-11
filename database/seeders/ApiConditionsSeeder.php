<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api;
use App\Models\Permission;
use App\Models\ApiCondition;
use Illuminate\Support\Facades\Log;

class ApiConditionsSeeder extends Seeder
{
    public function run()
    {
        $ApiConditionsMap = [
            // Format: 'METHODE route' => 'code_permission'
            'GET api/v1/roles' => 'AG_get_role',
            'POST api/v1/roles' => 'AG_create_role',
            'GET api/v1/roles/{id}' => 'AG_get_role',
            'PUT api/v1/roles/{id}' => 'AG_edit_role',
            'DELETE api/v1/roles/{id}' => 'AG_delete_role',
            'GET api/v1/roles/{roleId}/permissions' => 'AG_get_role',
            'POST api/v1/roles/{roleId}/permissions' => 'AG_create_role',
            'DELETE api/v1/roles/{roleId}/permissions' => 'AG_delete_role',
            'POST api/v1/role-requests' => 'AG_create_role_request',
            'GET api/v1/role-requests' => 'AG_get_role_request',
            'GET api/v1/role-requests/{id}' => 'AG_get_role_request',
            'PUT api/v1/role-requests/{id}' => 'AG_edit_role_request',
            'DELETE api/v1/role-requests/{id}' => 'AG_delete_role_request',
            'GET api/v1/permissions' => 'AG_get_permission',
            'POST api/v1/permissions' => 'AG_create_permission',
            'GET api/v1/permissions/{id}' => 'AG_get_permission',
            'PUT api/v1/permissions/{id}' => 'AG_edit_permission',
            'DELETE api/v1/permissions/{id}' => 'AG_delete_permission',
            'GET api/v1/users' => 'AG_get_user',
            'POST api/v1/users' => 'AG_create_user',
            'GET api/v1/users/{id}' => 'AG_get_user',
            'PUT api/v1/users/{id}' => 'AG_edit_user',
            'DELETE api/v1/users/{id}' => 'AG_delete_user',
            'GET api/v1/microservices' => 'AG_get_microservice',
            'POST api/v1/microservices' => 'AG_create_microservice',
            'GET api/v1/microservices/{id}' => 'AG_get_microservice',
            'PUT api/v1/microservices/{id}' => 'AG_edit_microservice',
            'DELETE api/v1/microservices/{id}' => 'AG_delete_microservice',
            'GET api/v1/permission-categories' => 'AG_get_permission_category',
            'POST api/v1/permission-categories' => 'AG_create_permission_category',
            'GET api/v1/permission-categories/{categoryId}' => 'AG_get_permission_category',
            'PUT api/v1/permission-categories/{categoryId}' => 'AG_edit_permission_category',
            'DELETE api/v1/permission-categories/{categoryId}' => 'AG_delete_permission_category',
            'POST api/v1/roles/{roleId}/permissions/{permissionId}' => 'AG_attach_permission_to_role',
            'DELETE api/v1/roles/{roleId}/permissions/{permissionId}' => 'AG_detach_permission_from_role',
            'POST api/v1/apis/{apiId}/permissions/{permissionId}' => 'AG_attach_permission_to_api',
            'DELETE api/v1/apis/{apiId}/permissions/{permissionId}' => 'AG_detach_permission_from_api',
            'POST api/v1/accounts/{accountId}' => 'AG_create_account',
            'PUT api/v1/accounts/{accountId}' => 'AG_edit_account',
            'DELETE api/v1/accounts/{accountId}' => 'AG_delete_account',
            'GET api/v1/me' => 'AG_view_self',
            'PUT api/v1/accounts/{accountId}/switch' => 'AG_switch_account',
            'POST api/v1/register' => 'AG_register',
            'POST api/v1/login' => 'AG_login',
            'POST api/v1/forgot-password' => 'AG_forgot_password',
            'POST api/v1/reset-password' => 'AG_reset_password',
            'POST api/v1/refresh-token' => 'AG_refresh_token',
            'GET api/v1/email/verify/{id}/{hash}' => 'AG_verify_email',
            'POST api/v1/email/resend' => 'AG_resend_verification_email',
            'GET api/v1/auth/token' => 'AG_view_auth_token',
            'GET api/v1/languages' => 'AG_view_languages',
            'POST api/v1/languages' => 'AG_create_language',
            'PUT api/v1/languages/{id}' => 'AG_edit_language',
            'DELETE api/v1/languages/{id}' => 'AG_delete_language',
            'GET api/v1/languages/{iso_639_code}/{type}/content' => 'AG_view_language_content',
            'GET api/v1/languages/{iso_639_code}/{type}/content/check/{md5}' => 'AG_check_language_content',
            'GET api/v1/languages/{languageId}/files' => 'AG_view_language_files',
            'POST api/v1/languages/{languageId}/files' => 'AG_create_language_file',
            'GET api/v1/languages/{languageId}/files/{fileId}' => 'AG_view_language_file',
            'PUT api/v1/languages/{languageId}/files/{fileId}' => 'AG_edit_language_file',
            'DELETE api/v1/languages/{languageId}/files/{fileId}' => 'AG_delete_language_file',
        ];
        

        foreach ($ApiConditionsMap as $apiPattern => $permissionCode) {
            // Trouver l'API et la Permission basées sur le mappage
            list($method, $route) = explode(' ', $apiPattern, 2);

            //Log::info("Recherche de l'API avec la route: $route et la méthode: $method.");

            $routePattern = str_replace(['{', '}'], '%', $route);
            $api = Api::where('route_in', 'like', $routePattern)
                      ->where('method', $method)
                      ->first();
        
            $api = Api::where('route_in', $route)->where('method', $method)->first();
            if (!$api) {
               // Log::warning("API non trouvée pour la route: $route et la méthode: $method.");
            }
            //Log::info("Recherche de la permission avec le code: $permissionCode.");

            $permission = Permission::where('code', $permissionCode)->first();
            if (!$permission) {
                //Log::warning("Permission non trouvée avec le code: $permissionCode.");
            }

            
            if ($api && $permission) {
                // Créer ou mettre à jour l'entrée dans api_conditions
                ApiCondition::updateOrCreate([
                    'api_id' => $api->id,
                    'permission_id' => $permission->id,
                ]);
                $ApiCondition = ApiCondition::updateOrCreate([
                    'api_id' => $api->id,
                    'permission_id' => $permission->id,

                    'level' => 0, 
                    'position_level' => 0,
                
                ]);
                // Log pour confirmer la création ou la mise à jour
                if ($ApiCondition->wasRecentlyCreated) {
                    //Log::info("Création réussie de la permission d'API pour $apiPattern.");
                } else {
                   // Log::info("Mise à jour réussie de la permission d'API pour $apiPattern.");
                }
            } else {
                // Loguer les cas où une correspondance n'est pas trouvée
                //Log::warning("Aucune correspondance trouvée pour $apiPattern avec le code de permission $permissionCode.");
            }
        }
    }
    protected function findApiByRouteAndMethod($route, $method)
    {
        // Ici, vous pouvez implémenter une logique pour matcher les routes avec des placeholders
        // Cette fonction est juste un placeholder pour l'implémentation réelle
        // Vous devriez adapter cette méthode en fonction de la structure de vos routes et de vos besoins
        return Api::where('route_in', 'like', $this->convertRouteToPattern($route))
                   ->where('method', $method)
                   ->first();
    }

    protected function convertRouteToPattern($route)
    {
        // Convertissez les placeholders en quelque chose de comparable avec 'like'
        // Par exemple, transformer 'api/v1/users/{id}' en 'api/v1/users/%'
        return str_replace(['{', '}'], '%', $route);
    }
}
