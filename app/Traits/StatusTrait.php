<?php

namespace App\Traits;

trait StatusTrait
{
    // activate model
    public function active()
    {
        $this->status = 1;
        $this->save();
    }

    // deactive model
    public function deactive()
    {
        $this->status = 0;
        $this->save();
    }
}
