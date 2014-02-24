<?  
    use RescueMe\Roles;
    use RescueMe\Permissions;
    
    if(isset($_ROUTER['message'])) { 
        insert_error($_ROUTER['message']);
    }
    
    $all_perms = Permissions::getAll();
    $active_perms = Roles::getPermissionsForRole($id);
        
    $fields = array();
    
    foreach ($all_perms as $resource=>$permission) {
        foreach ($permission as $access) {
            $name = "$resource.$access";
            $fields[] = array(
                'id' => "role[$name]",
                'type' => 'checkbox',
                'value' => (isset($active_perms[$name]) ? 'checked': ''),
                'label' => $name
            );
        }
    }
    
    $fields[] = array(
        'id' => 'role_id',
        'type' => 'hidden', 
        'value' => $id
    );    
    
    $role = Roles::getAll();
    
    insert_form("roles", _('Edit role'). ': '.$role[$id], $fields, ADMIN_URI."role/edit/$id");
    
?>