<?php
# charset=utf-8
defined("INCLUDES_CHECKER") or exit("Direct Access to this location is not allowed.");

require_once($_SERVER['DOCUMENT_ROOT'] . "/library/classes/class.GD.php");

# Class Photo
class Photo {

 var $gd = false;

 var $max_size;

 var $root;

 var $file_name;

 var $ext;

 var $file_size;

 var $file_tmp;

 var $file_mime;

 var $folder = "image/";

 var $upload_folder = "image/tmp/";

 var $logo;
 
 var $type = array(
  array("image/jpeg",  "jpg"),
  array("image/jpeg",  "jpeg"),
  array("image/jpeg",  "jpe"),
  array("image/pjpeg", "jpg"),
  array("image/pjpeg", "jpe"),
  array("image/pjpeg", "jpeg"),
  array("image/gif",   "gif"),
  array("image/png",   "png"),
  array("image/x-png", "png")
 );

 function Photo($file=false){
  $this->gd        = new GD();
  $this->root      = $_SERVER['DOCUMENT_ROOT']."/";
  $this->max_size  = preg_replace("/(\d+).*/", "\\1", ini_get("upload_max_filesize"));
  if ($file) $this->set_image($file);
  $this->file_name = md5(time());
  $sql = "SELECT setting.kay, value, `default` "
       . "FROM setting, setting_group "
       . "WHERE setting.group_id = setting_group.group_id "
       . "AND setting_group.kay = 'gallery'";
  $conf = DB::select($sql);
  foreach ($conf as $c){
   switch ($c['kay']){
    case 'copy':
     $this->logo = ($c['value']) ? $c['value'] : $c['default'];
     break;
    case 'resize_medium':
     $this->resize_medium = ($c['value']) ? $c['value'] : $c['default'];
     break;
    case 'resize_small':
     $this->resize_small = ($c['value']) ? $c['value'] : $c['default'];
     break;
   }
  }
 }

 function set_image($file){
  $this->file_size = $file['size'];
  $this->file_tmp  = $file['tmp_name'];
  $this->file_mime = $file['type'];
  $this->file_name = $file['name'];
  $this->ext = array_pop(explode(".", strtolower($this->file_name)));
  if (($this->ext=="jpe") || ($this->ext=="jpeg")) $this->ext="jpg";
  unset($_FILES);
 }

 function set_file_name(){
  $this->file_name = md5(time());
 }

 function get_file_name($kay=""){
  return $this->file_name.$kay.".".$this->ext;
 }

 function set_folder($folder){
  $this->folder = $folder;
 }

 function set_upload_folder($folder){
  $this->upload_folder = $folder;
 }

 function set_logo($logo){
  $this->logo = $logo;
 }

 function check_max_size(){
  return (($this->file_size/(1024*1024)) > $this->max_size);
 }

 function check_type(){
  foreach ($this->type as $type){
   if (!strcasecmp($this->file_mime,$type[0]) && !strcasecmp($this->ext,$type[1])){
    return TRUE;
   }
  }
  return FALSE;
 }

 function uploaded(){
  if (@move_uploaded_file($this->file_tmp, $this->root.$this->upload_folder.$this->get_file_name("_l"))){
   @chmod($this->root.$this->upload_folder.$this->get_file_name("_l"), 0777);
   unset($this->file_size, $this->file_tmp, $this->file_mime);
   if ($this->ext=="jpg") $this->jpg_clean();
   return TRUE;
  } else {
   return FALSE;
  }
 }

 function create_medium(){
  $this->gd->size($this->upload_folder.$this->get_file_name("_l"));
  return $this->create();
 }
  
 function paste_medium(){
  $this->gd->resize($this->resize_medium, $this->resize_medium);
  $this->logo();
  return $this->paste($this->folder.$this->get_file_name("_m"));
 }

 function create_small(){
  $this->gd->size($this->upload_folder.$this->get_file_name("_l"));
  return $this->create();
 }

 function paste_small(){
  $this->gd->resize($this->resize_small, $this->resize_small);
  return $this->paste($this->folder.$this->get_file_name("_s"), 70);
 }

 function logo(){
  if ($this->gd->gd) $this->gd->copy($this->logo, "br");
 }

 function create(){
  if (!$this->gd->gd) return FALSE;
  switch($this->ext){
   case 'jpg':
   case 'jpe':
   case 'jpeg':
    return $this->gd->create_jpeg($this->upload_folder.$this->get_file_name("_l"));
    break;
   case 'gif':
    return $this->gd->create_gif($this->upload_folder.$this->get_file_name("_l"));
    break;
   case 'png':
    return $this->gd->create_png($this->upload_folder.$this->get_file_name("_l"));
    break;
  }
  return FALSE;
 }

 function paste($file, $quality=100){
  if (!$this->gd->gd) return FALSE;
  switch ($this->ext){
   case 'jpg':
   case 'jpe':
   case 'jpeg':
    return $this->gd->save_jpeg($file, $quality);
    break;
   case 'gif':
    return $this->gd->save_gif($file);
    break;
   case 'png':
    return $this->gd->save_png($file);
    break;
  }
  return FALSE;
 }

/*
 * @since   20-11-2008
 * 
 * @param   void
 * @uses    is_array()
 * @uses    _remove_select()
 * @uses    _remove_curren()
 * 
 * Функция для удаления фотографии(ий)
 * При отсутствии входных данных удаляется
 * текущая фотография загруженная в данный обьект.
 * На входе массив со списком ID номеров фотографий,
 * которые надо удалить или число/строка с ID номером.
 */
 function remove($file=false){
  if ($file){
   if (is_array($file)){
    foreach ($file as $id){
     $this->_remove_select($id);
	}
   } else {
    $this->_remove_select($file);
   }
  } else {
   $this->_remove_curren();
  }
 }

/*
 * @since   20-11-2008
 *
 * @param   integer
 * @uses    DB::select()
 * @uses    intval()
 * @uses    count()
 * @uses    _remove_db()
 * @uses    _remove_file()
 * 
 * Функция удаляющая выбранную фотографию с указанным ID
 */
 function _remove_select($file_id){
  $photo = DB::select("SELECT file FROM photo WHERE photo_id = ".intval($file_id));
  if (count($photo)){
   $this->_remove_db(intval($file_id));
   $this->_remove_file("l", false, $photo[0]['file']);
   $this->_remove_file("m", true,  $photo[0]['file']);
   $this->_remove_file("s", true,  $photo[0]['file']);
  }
 }

/*
 * @since   20-11-2008
 *
 * @uses    DB::select()
 * @uses    count()
 * @uses    _remove_db()
 * @uses    _remove_file()
 * 
 * Функция удаляющая текущую фотографию
 */
 function _remove_curren(){
  $photo = DB::select("SELECT photo_id FROM photo WHERE file = '{$this->file_name}'");
  if (count($photo)) $this->_remove_db($photo[0]['photo_id']);
  $this->_remove_file("l", false);
  $this->_remove_file("m", true );
  $this->_remove_file("s", true );
 }

/*
 * @since   20-11-2008
 *
 * @param   string
 * @param   boolen
 * @param   integer
 * @uses    file_exists()
 * @uses    unlink()
 * 
 * Функция удаляющая указанный файл
 */
 function _remove_file($kay, $ds, $fname=false){
  $dir = ($ds) ? $this->folder : $this->upload_folder;
  $fname = ($fname!==false) ? $fname."_".$kay.$this->ext : $this->get_file_name($kay);
  if (file_exists($this->root.$dir.$fname)){
   unlink($this->root.$dir.$fname);
  }
 }

/*
 * @since   20-11-2008
 *
 * @param   integer
 * @uses    DB::delete()
 * 
 * Функция удаляющая указанную запись из БД
 */
 function _remove_db($file_id){
  DB::delete('photo',        array('photo_id' => $file_id));
  DB::delete('gallery_join', array('photo_id' => $file_id));
 }

/*
 * @version 1.0
 * @since   26-07-2008
 * 
 * @param   string
 * @param   string
 * @uses    time()
 * @uses    md5()
 * @uses    rand()
 * @uses    substr()
 * @uses    is_dir()
 * @uses    mkdir()
 * @return  string
 * 
 * генерирует простые дериктории имя дерикторий состоит из 
 * 1 численно-буквенного симовола (a-z0-9)
 * 
 * для защиты от зацикливания во внутренний цикл
 * встроена проверка и по прошествию 15 итэраций
 * генерируется случайная дериктория
 * и функция переходит к генерации поддерикторий
 * 
 * Пример: a/g/4/p/7/
 */
 function rand_simple_dir($folder=""){
  $f = substr(md5(time()), rand(0, 31), 1)."/";
  $i = 0;
  while (is_dir($this->root.$folder.$f)){
   $f = substr(md5(time()), rand(0, 31), 1)."/";
   // защита от зацикливания
   if ($i==15){
    @mkdir($this->root.$folder.$f, 0700);
    // генерация sub дерикторий
    $folder = $this->rand_simple_dir($folder.$f);
    $f = "";
   }
   $i++;
  }
  @mkdir($this->root.$folder.$f, 0700);
  return $folder.$f;
 }

 function jpg_clean($destination=""){
  $destination = ($destination=="") ? $this->root.$this->upload_folder.$this->get_file_name("_l") : $destination;
  $handle = fopen($this->root.$this->upload_folder.$this->get_file_name("_l"), "rb");
  $segment[] = fread($handle, 2);
  if($segment[0] === "\xFF\xD8"){
   $segment[] = fread($handle, 1);
   if($segment[1] === "\xFF"){
    rewind ($handle);
	$newfile = "";
    while(!feof($handle)){
     $daten = fread($handle, 2);
     if((preg_match("/FFE[1-9a-zA-Z]{1,1}/i",bin2hex($daten))) || ($daten === "\xFF\xFE")){
      $position = ftell($handle);
      $size = fread($handle, 2);
      $newsize = 256 * ord($size{0}) + ord($size{1});
      $newpos = $position + $newsize;
      fseek($handle, $newpos);
     } else {
      $newfile .= $daten;
     }
    }
    fclose($handle);
//    $newfile = implode('',$newfile);
    $handle = fopen($destination, "wb");
    fwrite($handle, $newfile);
    fclose($handle);
    return TRUE;
   } else {
    return FALSE;
   }
  } else {
   return FALSE;
  }
 }

 function get_author_id($family, $name, $mail=""){
  $sql = "SELECT author_id FROM photo_author WHERE"
       . " family = '".DB::escape_string($family)."'"
       . " AND name = '".DB::escape_string($name)."'";
  if ($mail){
   $sql .= " AND mail = '".DB::escape_string($mail)."'";
  }
  $author = DB::select($sql);
  return(count($author) ? $author[0]['author_id'] : FALSE);
 }

 function set_author($family, $name, $mail){
  $family = substr(trim($family), 0, 128);
  $name   = substr(trim($name),   0, 128);
  $mail   = substr(trim($mail),   0, 128);
  $insert = array(
   'family' => DB::escape_string($family),
   'name'   => DB::escape_string($name),
   'mail'   => DB::escape_string($mail)
  );
  return DB::insert("photo_author", $insert);
 }

 function set_photo($author_id, $photname){
  $photname = substr(trim($photname), 0, 128);
  // переменная уже экранированна в классе FormCheck
//  $photname = str_replace("\'", "'", $photname);
//  $photname = htmlspecialchars($photname);
  $insert = array(
   'file'       => $this->file_name,
   'path'       => $this->folder,
   'ext'        => $this->ext,
   'author_id'  => $author_id,
   'photo_name' => $photname,//DB::escape_string($photname)
   'date'       => time()
  );
  return DB::insert("photo", $insert);
 }

 function get_gallery_id($year, $kay){
  $sql = "SELECT gallery_id FROM gallery WHERE "
       . "year = ".intval($year)." AND "
       . "kay = '".DB::escape_string($kay)."'";
  $gallery = DB::select($sql);
  return isset($gallery[0]['gallery_id']) ? $gallery[0]['gallery_id'] : FALSE;
 }

 function set_photo_join($gallery_id){
  if ($gallery_id == -1) return true;
  $insert = array(
   'gallery_id' => $gallery_id,
   'photo_id'   => DB::insert_id()
  );
  return DB::insert("gallery_join", $insert);
 }

} // end class

?>