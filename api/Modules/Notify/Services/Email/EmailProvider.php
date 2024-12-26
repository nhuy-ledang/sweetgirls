<?php
namespace Modules\Notify\Services\Email;

use Modules\Notify\Services\NotifyProvider;

class EmailProvider extends NotifyProvider {

    /**
     * @return EmailDefault
     */
    function getProvider() {
        return app()->make('Modules\Notify\Services\Email\EmailDefault');
    }
}