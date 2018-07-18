<?php
namespace dungang\webuploader\actions;

use dungang\storage\StorageAction;

class InitAction extends StorageAction
{
    public $accept;
    
    public function run()
    {
        
        $this->driver->accept = $this->accept;
        $rsp = $this->driver->initChunkUpload();
        return json_encode($rsp);
    }
}

