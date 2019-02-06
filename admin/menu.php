<?php
require_once 'PaackApi.php';

add_action("admin_menu", "create_menu");
add_action("admin_init", "register_data");
function create_menu() {
  // Aquí va el código para crear opciones del menú
  add_menu_page('Configuración Paack', 'Paack', 'manage_options', 'paack_slug', 'output_menu','dashicons-share-alt2');
}
function output_menu() {
    $idStore = get_option('store_id');
    $is_store_valid = get_option('is_store_valid');
    ?>
    <h1>Configuración Paack Plugin</h1>
    <p>Plugin para consultar y generar envios.</p>
    <?php
        if($idStore!=null && $idStore!= ''){
            if($is_store_valid==1){
                echo messages('updated notice','Plugin configurado exitosamente.');
            }else{
                echo messages('error notice','Tu id de tienda Paack no es válido.');
            }
        }else{
            echo messages('error notice','Debe ingresar un id de Tienda registrado en Paack.');
        }

    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <?php
                settings_fields('paack_setting_group');
                do_settings_sections('paack_setting_group');
            ?>
            <input type="hidden" name="is_store_valid" value="<?=$is_store_valid?>">
            <table class="form-table">
                <tbody>
                    <tr valing="top">
                        <th><label for="api_token">Api Token</label></th>
                        <td>
                            <input type="text" name="api_token" id="api_token" value="<?=get_option('api_token')?>" maxlength="100" style="width:600px" required> *
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="store_id">Id de store</label></th>
                        <td>
                            <input type="number" name="store_id" id="store_id" value="<?=$idStore?>" required=""> *
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="text_popup">Texto para popup</label></th>
                        <td>
                            <textarea name="text_popup" id="text_popup" style="width:600px; height: 100px;"><?=get_option('text_popup')?></textarea>
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="zip_codes">Códigos postales</label></th>
                        <td>
                            <textarea name="zip_codes" id="zip_codes" style="width:600px; height: 100px;"><?=get_option('zip_codes')?></textarea><br>
                            <span class="description">Agregar los códigos postales permitidos para envíos de 2 horas separados por coma (,)</span>
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="paack_message_zip_code_success">Mensaje Exito código postal</label></th>
                        <td>
                            <input type="text" name="paack_message_zip_code_success" id="paack_message_zip_code_success" value="<?=get_option('paack_message_zip_code_success')?>" maxlength="100" style="width:600px">
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="paack_message_zip_code_error">Mensaje Error código postal</label></th>
                        <td>
                            <input type="text" name="paack_message_zip_code_error" id="paack_message_zip_code_error" value="<?=get_option('paack_message_zip_code_error')?>" maxlength="100" style="width:600px" >
                        </td>
                    </tr>
                    <tr valing="top">
                        <th><label for="paack_testing">Modo prueba</label></th>
                        <td>
                            <input type="checkbox" name="paack_testing" value="1" <?php checked( 1 == get_option( 'paack_testing' )); ?> />
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button();?>
        </form>
    </div>
    <?php
  }

  function register_data(){

      $idStore = get_option('store_id');
      update_option( 'is_store_valid', validate_stores_id($idStore));
      if(get_option('text_popup')==''){update_option('text_popup','¿Desea envíos a su destino en 2 horas? Ingrese aquí su código postal y verifique si tenemos disponible envíos a su ubicación.');}
      if(get_option('paack_message_zip_code_success')==''){update_option( 'paack_message_zip_code_success', 'Su código postal es válido.');}
      if(get_option('paack_message_zip_code_error')==''){update_option( 'paack_message_zip_code_error', 'Lo sentimos, su código postal no es válido.');}


      register_setting('paack_setting_group','text_popup');
      register_setting('paack_setting_group','store_id');
      register_setting('paack_setting_group','api_token');
      register_setting('paack_setting_group','is_store_valid');
      register_setting('paack_setting_group','zip_codes');

      register_setting('paack_setting_group','paack_message_zip_code_success');
      register_setting('paack_setting_group','paack_message_zip_code_error');
      register_setting('paack_setting_group','paack_testing');
  }

  function validate_stores_id($idStore){
    $isValid=0;
       if($idStore!=null && $idStore!=''){
            $res = PaackApi::check_store($idStore);
            if($res["error"]==0){
                $isValid = 1;
            }
       }
    return $isValid;
  }

  function messages($class, $message){
    return '<div class="'.$class.'"><p>'.$message.'</p></div>';
  }
?>
