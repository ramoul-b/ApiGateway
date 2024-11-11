<?php

return [
    'success' => 'Operazione completata con successo',
    'error' => 'Si è verificato un errore',
    'server_error' => 'Errore del server',
    'unauthorized' => 'Non autorizzato.',

    // Messages for UserController
    'user_created_success' => 'Utente creato con successo',
    'user_deleted_success' => 'Utente eliminato con successo',
    'user_not_found' => 'Utente non trovato',
    'validation_required' => 'Il campo :attribute è obbligatorio.',
    'validation_unique' => 'Il campo :attribute deve essere unico.',
    'user_not_found' => 'Utente non trovato.',
    'error_retrieving_users' => 'Si è verificato un errore durante il recupero degli utenti.',


    // Messages pour les rôles
    'role_not_found' => 'Ruolo non trovato.',
    'role_creation_failed' => 'Impossibile creare il ruolo.',
    'role_update_failed' => 'Impossibile aggiornare il ruolo.',
    'role_deletion_failed' => 'Impossibile eliminare il ruolo.',
    'role_created_success' => 'Ruolo creato con successo.',
    'role_updated_success' => 'Ruolo aggiornato con successo.',
    'role_deleted_success' => 'Ruolo eliminato con successo.',
    'permission_attached_success' => 'Permesso allegato con successo.',
    'permission_detached_success' => 'Permesso staccato con successo.',
    'permission_attachment_failed' => 'Impossibile allegare il permesso.',
    'permission_detachment_failed' => 'Impossibile staccare il permesso.',

    // Messages pour les permissions
    'permission_not_found' => 'Permesso non trovato.',
    'permission_creation_failed' => 'Impossibile creare il permesso.',
    'permission_update_failed' => 'Impossibile aggiornare il permesso.',
    'permission_deletion_failed' => 'Impossibile eliminare il permesso.',
    'permission_created_success' => 'Permesso creato con successo.',
    'permission_updated_success' => 'Permesso aggiornato con successo.',
    'permission_deleted_success' => 'Permesso eliminato con successo.',    

        // Messages pour les catégories de permissions
    'error_retrieving_permission_categories' => 'Si è verificato un errore durante il recupero delle categorie di permessi.',
    'permission_category_not_found' => 'Categoria di permesso non trovata.',
    'permission_category_creation_failed' => 'Impossibile creare la categoria di permessi.',
    'permission_category_update_failed' => 'Impossibile aggiornare la categoria di permessi.',
    'permission_category_deletion_failed' => 'Impossibile eliminare la categoria di permessi.',
    'permission_category_created_success' => 'Categoria di permessi creata con successo.',
    'permission_category_updated_success' => 'Categoria di permessi aggiornata con successo.',
    'permission_category_deleted_success' => 'Categoria di permessi eliminata con successo.',


    // Messages pour les microservices
    'error_retrieving_microservices' => 'Si è verificato un errore durante il recupero dei microservizi.',
    'microservice_not_found' => 'Microservizio non trovato.',
    'microservice_creation_failed' => 'Impossibile creare il microservizio.',
    'microservice_update_failed' => 'Impossibile aggiornare il microservizio.',
    'microservice_deletion_failed' => 'Impossibile eliminare il microservizio.',
    'microservice_created_success' => 'Microservizio creato con successo.',
    'microservice_updated_success' => 'Microservizio aggiornato con successo.',
    'microservice_deleted_success' => 'Microservizio eliminato con successo.',
    
        // Messages for APIs
    'error_retrieving_apis' => 'Si è verificato un errore durante il recupero delle API.',
    'api_not_found' => 'API non trovata.',
    'api_creation_failed' => 'Impossibile creare l\'API.',
    'api_update_failed' => 'Impossibile aggiornare l\'API.',
    'api_deletion_failed' => 'Impossibile eliminare l\'API.',
    'api_created_success' => 'API creata con successo.',
    'api_updated_success' => 'API aggiornata con successo.',
    'api_deleted_success' => 'API eliminata con successo.',
    'permission_attached_success' => 'Permesso allegato con successo.',
    'permission_detached_success' => 'Permesso staccato con successo.',
    'permission_attachment_failed' => 'Impossibile allegare il permesso.',
    'permission_detachment_failed' => 'Impossibile staccare il permesso.',

        // Messages for AccountController
    'account_not_found' => 'Account non trovato.',
    'account_update_failed' => 'Aggiornamento dell\'account non riuscito.',
    'account_deleted_success' => 'Account eliminato con successo.',
    'account_deletion_failed' => 'Eliminazione dell\'account non riuscita.',
    'role_assigned_successfully' => 'Ruolo assegnato con successo all\'account.',
    'operation_failed' => 'Operazione non riuscita.',
    'role_removed_successfully' => 'Ruolo rimosso con successo dall\'account.',
    'default_role_set_successfully' => 'Ruolo predefinito impostato con successo.',
    'account_associated_with_organization_successfully' => 'Account associato con successo all\'organizzazione.',
    'default_organization_changed_successfully' => 'Organizzazione predefinita modificata con successo.',
    'account_details_retrieved_successfully' => 'Dettagli dell\'account recuperati con successo.',
    'current_password_not_matched' => 'La password attuale non corrisponde.',
    'password_changed_successfully' => 'Password cambiata con successo.',

        // Messages for AuthController
    'logout_successfully' => 'Disconnesso con successo.',
    'operation_failed' => 'Operazione non riuscita.',
    'reset_link_sent_successfully' => 'Link per il ripristino della password inviato con successo.',
    'password_reset_successfully' => 'Password ripristinata con successo.',
    'email_verified_successfully' => 'Indirizzo email verificato con successo.',
    'email_already_verified' => 'Indirizzo email già verificato.',
    'verification_email_resent' => 'Email di verifica inviata nuovamente con successo.',
];
