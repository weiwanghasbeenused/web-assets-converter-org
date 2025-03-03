<?php
class WebpManagerBackend {
    public $action = '';
    public $media_root = '';
    public $names = array(
        'convert' => 'mediaToConvert',
        'upload' => 'mediaToUpload',
        'delete' => 'mediaToDelete'
    );
    public $name = '';
    private $db = null;
    function __construct($action, $media_root='/media/'){
        $this->action = $action; 
        $this->name = $this->names[$this->action];
        $this->media_root = $media_root;
        $this->db = db_connect('admin');
    }
    function sliceFilename($fn) {
        $p = strrpos($fn, '.');
        $output = array(
            'ext'  => substr($fn, $p + 1),
            'name' => substr($fn, 0, $p)
        );
        return $output;
    }
    function delete($files){
        $result = array();
        foreach($files as $f) {
            if(unlink($this->media_root . $f)) {
                $fn = $this->sliceFilename($f);
                $id = intval($fn['name']);
                $result[] =  '[O] ' . $f . ' >> deleted';
                $this->updateRecord($id, 'NULL');
            } else {
                $result[] =  '[X] ' . $f . ' >> not deleted';
            }
        }
        // return implode('<br>', $result);
        $output = $this->formatOutput(implode('<br>', $result));
        return $output;
    }
    function upload($files){
        $result = array();
        foreach($files["error"] as $key => $error) {
            $name = $files["name"][$key];
            if($error == UPLOAD_ERR_OK) {
                $tmp_name = $files["tmp_name"][$key];
                $m_dest = $this->media_root . $name;
                $result[] = move_uploaded_file($tmp_name, $m_dest) ? '[O] ' . $name . ' >>> uploaded.' : '[X] ' . $name . ' >>> upload failed.';
                $fn = $this->sliceFilename($name);
                $id = intval($fn['name']);
                $this->updateRecord($id, 1.0);
            } else {
                $result[] = '[X] ' . $name . ' >>> upload failed.';
            }
        }
        $output = $this->formatOutput(implode('<br>', $result));
        return $output;
    }
    function convert($files){
        $output = '';
        $result = array();
        $files_arr = array();
        foreach($files as $fn) 
            $files_arr[] = $this->media_root . $fn;
        $files_arr = implode(' ', $files_arr);
        $log = __DIR__ . '/../bash/logs/log';
        exec(__DIR__ . '/../bash/media-converter.sh ' . $files_arr);

        foreach($files as $f) {
            $fn = $this->sliceFilename($f);
            if(file_exists($this->media_root . $fn['name'] . '.' . 'webp') || file_exists($this->media_root . $fn['name'] . '.' . 'webm')){
                $id = intval($fn['name']);
                $updated = $this->updateRecord($id, 1.0);
                $result[] = $updated ? '[O] ' . $f . ' >>> converted. record updated' : '[O] ' . $f . ' >>> converted. record not updated';
            } else  {
                $result[] = '[X] ' . $f . ' >>> conversion failed.';
            }
        }
        // $log = implode('<br>', $log);
        $output = $this->formatOutput(implode('<br>', $result));
        return $output;
    }
    function updateRecord($id, $w){
        if(!$this->db) return false;
        $sql = "UPDATE `media` SET `weight` = $w WHERE `id` = '$id'";
        return $this->db->query($sql);
    }
    function formatOutput($r){
        $output = '<div id="output-container"><div>result:</div><br><div id="result-container">' . $r . '</div></div>';
        return $output;
    }
}
