<?php
    
    ob_start();
    
    use RescueMe\User;
    use RescueMe\Missing;
    use RescueMe\Properties;
    
    if(isset($_ROUTER['error'])) {
        insert_error($_ROUTER['error']);
    }
    
    $user = User::current();
    $user_id = $user->id;
    $admin = User::current()->allow("read", 'operations.all');
    
    $filter = '(op_closed IS NOT NULL)';
    if(isset($_GET['filter'])) {
        $filter .= ' AND ' . Missing::filter(isset_get($_GET, 'filter', ''), 'OR');
    }
    
    $list = Missing::countAll($filter, $admin);
    
    $page = input_get_int('page', 1);
    $max = Properties::get(Properties::SYSTEM_PAGE_SIZE, $user_id);
    $start = $max * ($page - 1);
    
    if($list === false || $list <= $start) { ?>

        <tr><td colspan="<?=$admin ? 4 : 3?>"><?=_('Ingen registrert')?></td></tr>

<? } else { 
        
    // Create pagination options
    $total = ceil($list/$max);
    $options = create_paginator(1, $total, $user_id);
    
    // Get missing
    $list = Missing::getAll($filter, $admin, $start, $max);
    
    foreach($list as $id => $this_missing) {
        $owner = ($this_missing->user_id === $user_id);
?>
            <tr id="<?= $this_missing->id ?>">
                <? if($admin) { ?>
                <td class="missing name"><?= $this_missing->name ?></td>
                <td class="missing name"><?=($owner ? '<b class="icon icon-ok"></b>' : '')?></td>
                <? } else { ?>
                <td class="missing name" colspan="2"> <?= $this_missing->name ?> </td>
                <? } ?>
                <td class="missing date"><?= format_dt($this_missing->op_closed) ?></td>
                <td class="missing editor">
                    <div class="btn-group pull-right">
                        <a class="btn btn-small" href="<?=ADMIN_URI."operation/reopen/$id"?>">
                            <b class="icon icon-edit"></b><?= _('Gjenåpne') ?>
                        </a>
                    </div>
                </td>
            </tr>
            
<? }} 

    
    if(isset($options) === false) {
        $options = create_paginator(1, 1, $user_id);         
    }
    
    return create_ajax_response(ob_get_clean(), $options);
    
?>