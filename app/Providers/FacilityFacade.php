<?php
use Illuminate\Support\Facades\Facade;

class FacilityFacade extends Facade {

    protected static function getFacadeAccessor() { return 'Facility'; }
}