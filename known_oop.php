<?php
$action = P::action('m1 m2 action');

$REG_USERS = new reg_users(REG::I()->PARAM, $action);
$REG_USERS->getPage();
return;


class reg_users{

  // ****  ГЛОБАЛЬНЫЕ (НАСЛЕДУЕМЫЕ) ПЕРЕМЕННЫЕ МОДУЛЯ
  protected $tableSlovar = 'shop_items';

  protected $action = '';

  protected $tplDir = '';
  protected $tplAbsDir = '';

  function __construct($PARAM, $action){
    $this->action = $action;
    $this->tplDir = dirname(__FILE__).'/';
    $this->tplAbsDir = F::absDIR($this->tplDir);
    // Автоматически подгружаем темплейт по имени фйла модуля
    $tpl = preg_replace('~\.php$~','.htm',basename(__FILE__));
    TPL::LoadTplFile($tpl,'module_edit', $this->tplDir, $this->tplAbsDir);
  }

  public function getPage(){

    if(P::get("sendmail")){
      $this->addMailLoginUser();
    } else if(P::get("search")){
      $this->searchList();
    }else{
      $this->printList();
    }

  }
  
  private function addMailLoginUser(){
    unset($_POST["sendmail"]);
    if(!empty($_POST['grant']) && !empty($_POST['textadmin']) && !empty($_POST['user_filial_id'])){
      $grant = $_POST['grant'];
      $textadmin = nl2br($_POST['textadmin']);
      $user_filial_id = $_POST['user_filial_id'];
      if($user_filial_id != "all"){
      $LOGIN = DB::select_array("SELECT user_login FROM users_grant,users WHERE users_grant.grant_user_id=users.user_id AND grant_id=$grant AND user_filial_id=$user_filial_id");
      }else{
      $LOGIN = DB::select_array("SELECT user_login FROM users_grant,users WHERE users_grant.grant_user_id=users.user_id AND grant_id=$grant");
      }
      $MAIL_ARR = array(  'from' => 'Панель администрирования корпоративного сайта', 
              'subject' => 'Рассылка сообщений администратором корпоративного сайта'  );
      $MAIL = new SiteMailLotus();
      foreach ($LOGIN as $value) {
        $MAIL->setTo($value['user_login'].'@lotus.asb.by');
      }
      $MAIL->setFrom('',$MAIL_ARR["from"]);
      $MAIL->setSubject($MAIL_ARR["subject"]);
      $MAIL->setMessage($textadmin);
        if($MAIL->send()) {
          $_SESSION["INFO_MESSAGE"] = 'Сообщения успешно отправлены';
          unset($_POST);
        }else{
          $_SESSION["INFO_MESSAGE"] = 'Произошла ошибка при оправки данных';
        }

    }else{
      $_SESSION["INFO_MESSAGE"] = 'Заполните текст сообщения администраторам сайта';
    }
    header("Location: ".$this->action);
  }

  private function searchList(){
      if(!empty($_POST['grant']) && !empty($_POST['user_filial_id'])){
  $grant = $_POST['grant'];
  $user_filial_id = $_POST['user_filial_id'];
  if($user_filial_id != "all"){
  $SEARCHLIST=DB::select_array("SELECT user_login,user_name,user_surname,user_familyname FROM users_grant,users WHERE users_grant.grant_user_id=users.user_id AND grant_id=$grant AND user_filial_id=$user_filial_id");
  $GRANTLIST = DB::select_array("SELECT grantlist_id,grantlist_info FROM users_grantlist");
    $FILIALS=DB::select_array("SELECT f.id,CONCAT(ft.type_name, ' №', f.num) as fil_name FROM  filials f 
			LEFT JOIN filials_type as ft ON ft.type_id=f.`type`
			WHERE id IN (SELECT DISTINCT u.user_filial_id FROM users u)
			ORDER BY f.num ASC");
  }else{
  $SEARCHLIST=DB::select_array("SELECT user_login,user_name,user_surname,user_familyname FROM users_grant,users WHERE users_grant.grant_user_id=users.user_id AND grant_id=$grant");
  $GRANTLIST = DB::select_array("SELECT grantlist_id,grantlist_info FROM users_grantlist");
    $FILIALS=DB::select_array("SELECT f.id,CONCAT(ft.type_name, ' №', f.num) as fil_name FROM  filials f 
			LEFT JOIN filials_type as ft ON ft.type_id=f.`type`
			WHERE id IN (SELECT DISTINCT u.user_filial_id FROM users u)
			ORDER BY f.num ASC");
  }

      $DATA = array(
        'SEARCHLIST'=> $SEARCHLIST,
        'GRANTLIST'=> $GRANTLIST,
      	'FILIALS'=> $FILIALS,
        'action'=> $this->action,
      );
      TPL::PrnTpl('admMessagesTPL',$DATA);
    }else{
      $_SESSION["INFO_MESSAGE"] = 'ВЫБЕРИТЕ ФИЛИАЛ(ПРАВО)!';
      header("Location: ".$this->action);
    }
  }

  private function printList(){
    $LIST = DB::select_array("SELECT user_login,user_name,user_surname,user_familyname FROM users ORDER BY user_surname");
    $GRANTLIST = DB::select_array("SELECT grantlist_id,grantlist_info FROM users_grantlist");
    $FILIALS=DB::select_array("SELECT f.id,CONCAT(ft.type_name, ' №', f.num) as fil_name FROM  filials f 
			LEFT JOIN filials_type as ft ON ft.type_id=f.`type`
			WHERE id IN (SELECT DISTINCT u.user_filial_id FROM users u)
			ORDER BY f.num ASC");
    $DATA = array(
      'LIST'=> $LIST,
      'GRANTLIST'=> $GRANTLIST,
      'FILIALS'=> $FILIALS,
      'action'=> $this->action,
    );
    TPL::PrnTpl('admMessagesTPL',$DATA);
  }

  private function getParam($name){ return P::get($name); }

  private function redirect($redirectURL, $debug = false){
    //$debug = true;
    echo '<br><br><a href="'.$redirectURL.'">REDIRECT LINK</a>';
    if(!$debug) header("Location: ".$redirectURL);
    exit();
  }

  private function utf2win($text){
    return iconv('utf-8','cp1251',$text);
  }

  private function win2utf($text){
    return iconv('cp1251','utf-8',$text);
  }
}

?>
