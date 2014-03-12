<?  
    use RescueMe\User;
    use \RescueMe\Locale;
    
    $approve = isset($_GET['approve']);
 
    
    
    $id = input_get_int('id', User::currentId());
    $user = User::get($id);
    
    $fields = array();
    
    $fields[] = array(
        'id' => 'name',
        'type' => 'text', 
        'value' => $user->name, 
        'label' => _('Full name'),
        'attributes' => 'required autofocus'
    );
    
    $group = array(
        'type' => 'group',
        'class' => 'row-fluid'
    );
    $group['value'][] = array(
        'id' => 'country',
        'type' => 'select', 
        'value' => insert_options(Locale::getCountryNames(), $user->mobile_country, false), 
        'label' => _('Mobile country'),
        'class' => 'span2',
        'attributes' => 'required'
    );    
    $group['value'][] = array(
        'id' => 'mobile',
        'type' => 'tel', 
        'value' => $user->mobile, 
        'label' => _('Mobile'),
        'class' => 'span2',
        'attributes' => 'required pattern="[0-9]*"'
    );
    $group['value'][] = array(
        'id' => 'email',
        'type' => 'email', 
        'value' => $user->email, 
        'label' => _('E-mail'),
        'class' => 'span3',
        'attributes' => 'required'
    );    
    $fields[] = $group;
    if (User::current()->allow('write', 'roles')) {
        $fields[] = array(
            'id' => 'role',
            'type' => 'select',
            'value' => insert_options(\RescueMe\Roles::getAll(), $user->role_id, false), 
            'label' => _('Role'),
            'attributes' => 'required'
        );
    }
    
    if($approve) {
        $_ROUTER['submit'] = ('Godkjenn');
        insert_form("user", _('Godkjenn bruker'), $fields,  ADMIN_URI."user/approve/$id", $_ROUTER);
    } else {
        insert_form("user",_(EDIT_USER), $fields, ADMIN_URI."user/edit/$id", $_ROUTER);
    }
    
?>