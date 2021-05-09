<?php


namespace Freezemage\Config\Feature;


class KeyChaining implements FeatureInterface {
    protected $state;
    
    public function isEnabled(): bool {
        return $this->state ?? true;
    }
    
    public function enable(): void {
        $this->state = true;
    }
    
    public function disable(): void {
        $this->state = false;
    }
}