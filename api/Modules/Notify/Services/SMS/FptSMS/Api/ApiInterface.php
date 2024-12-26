<?php
namespace Modules\Notify\Services\SMS\FptSMS\Api;

/**
 * Api interface
 * 
 * @author ISC--DAIDP
 * @since 22/09/2015
 */
interface ApiInterface
{
    public function toArray();
    
    public function getAction();
}
