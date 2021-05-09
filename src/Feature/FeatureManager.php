<?php


namespace Freezemage\Config\Feature;


final class FeatureManager {
    protected $keyChaining;
    
    public function getKeyChaining(): KeyChaining {
        if (!isset($this->keyChaining)) {
            $this->keyChaining = new KeyChaining();
        }
        return $this->keyChaining;
    }
}