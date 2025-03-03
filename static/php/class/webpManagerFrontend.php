<?php
class WebpManagerFrontend {
    public $action = '';
    public $media_root = '/media/';
    public $acceptables = array(
        'img' => '',
        'vid' => ''
    );
    public $names = array(
        'convert' => 'mediaToConvert',
        'upload' => 'mediaToUpload',
        'delete' => 'mediaToDelete'
    );
    public $name = '';
    function __construct($action, $acceptables){
        $this->action = $action; 
        $this->name = isset($this->names[$this->action]) ? $this->names[$this->action] : '';
        $this->acceptables = $acceptables;
    }
    function sliceFilename($fn) {
        $p = strrpos($fn, '.');
        $output = array(
            'ext'  => substr($fn, $p + 1),
            'name' => substr($fn, 0, $p)
        );
        return $output;
    }
    function renderListItem($fn, $id, $mediaType='img'){
        $output = $mediaType == 'img' ? $this->renderImg($fn) : $this->renderVid($fn);
        $name = $this->name . '[]';
        $e_id = $mediaType . '-'.$id;
        if(!$this->name)
            $output = '<li class="list-item"><label class="checkbox-label" for="'. $e_id .'"><div class="item-media">' . $output . '</div><div class="item-name">' . $fn . '</div></label></li>';
        else
            $output = '<li class="list-item"><input id="'.$e_id.'" class="converter-item-checkbox" name="'.$name.'" type="checkbox" value="'.$fn.'"><label class="checkbox-label" for="'. $e_id .'"><div class="item-media">' . $output . '</div><div class="item-name">' . $fn . '</div></label></li>';
        return $output;
    }
    function renderImg($fn){
        $src = $this->media_root . $fn;
        return '<img src="'.$src.'">';
    }
    function renderVid($fn){
        $src = $this->media_root . $fn;
        return '<video muted playsinline controls src="'.$src.'">The browser doesnt support this type of video.</video>';
    }
    function renderImgList($media, $view='rows'){
        $output = '<ul id="'.$this->action.'-img-list" mm-list-view="'.$view.'">';
        if($media && count($media) !== 0)
            foreach($media as $id => $fn)
                $output .= $this->renderListItem($fn, $id);
        else $output .= 'No files found.';
        $output .= '</ul>';
        return $output;
    }
    function renderVidList($media, $view='rows'){
        $output = '<ul id="'.$this->action.'-vid-list" mm-list-view="'.$view.'">';
        foreach($media as $id => $fn)
            $output .= $this->renderListItem($fn, $id, 'vid');
        $output .= '</ul>';
        return $output;
    }
}