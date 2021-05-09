<?php


namespace Freezemage\Config\Feature;


interface FeatureInterface {
    public function isEnabled(): bool;
    
    public function enable(): void;
    
    public function disable(): void;
}