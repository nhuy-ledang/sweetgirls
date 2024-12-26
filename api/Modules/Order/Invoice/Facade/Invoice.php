<?php namespace Modules\Order\Invoice\Facade;

use Illuminate\Support\Facades\Facade;

class Invoice extends Facade {
    protected static function getFacadeAccessor() {
        return 'invoice';
    }
}
