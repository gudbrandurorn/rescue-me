<?
use RescueMe\Module;

$id = $_GET['id'];

$module = Module::get($id);

if($module === false)
{
?><h3><?=_("Module")?></h3><?
    
    insert_alert(_('No module found. Run install script.'));
    
}
else
{        
    $impl = isset($_GET['type']) ? new $_GET['type'] : $module->newInstance();
    
    $fields = array();
    
    $fields[] = array(
        'id' => "type",
        'type' => 'hidden', 
        'value' => $module->type
    );
    
    $fields[] = array(
        'id' => "class",
        'type' => 'hidden', 
        'value' => isset($_GET['type']) ? $_GET['type'] : $module->impl
    );
    
    $config = $impl->config();
    
    foreach($config->params() as $property => $default) {
        
        $readonly = ($property === \RescueMe\SMS\Callback::PROPERTY) && ($impl instanceof \RescueMe\SMS\Callback);
        $attributes = array($config->required($property) ? "required" : '', ($readonly ? "readonly" : ''));
        
        $fields[] = array(
            'id' => "$property",
            'type' => 'text', 
            'value' => $default, 
            'label' => _($config->label($property)),
            'attributes' => trim(implode(" ", $attributes))
        );
    }
    
    $label = _($module->type).": "._(isset($_GET['type']) ? $_GET['type'] : $module->impl);
    
    insert_form("module", $label, $fields, ADMIN_URI."setup/module/$id");
}             
