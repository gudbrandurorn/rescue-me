<?    
    $inline = (isset($inline) && $inline ? true : false);
    
    use RescueMe\User;
    use RescueMe\Properties;
    
    $include = (isset($context) ? $context : ".*");
    
    $id = input_get_int('id', User::currentId());

    $pattern = '#'.$include.'#';
    
    ob_start();
    
    if($inline === false) { ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th width="25%"><?=T_("Settings")?></th>
            <th width="25%"></th>
            <th width="50%">
                <input type="search" class="input-medium search-query pull-right" placeholder="Search">
            </th>            
        </tr>
    </thead>        
    <tbody class="searchable">        

<? } 


    foreach(Properties::rows($id) as $name => $cells) {
        if(preg_match($pattern, $name)) {
            insert_row($name, $cells);
        }
    }
    
 if($inline === false) { ?>
        
    </tbody>
</table>    
        
 <? } 
 
    return $inline ? ob_get_clean() : create_ajax_response(ob_get_clean()); 
 
 ?>