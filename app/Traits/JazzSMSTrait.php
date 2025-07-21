<?php

namespace App\Traits;

use NsTechNs\JazzCMS\JazzCMS;
use Illuminate\Support\Facades\Http;

trait JazzSMSTrait
{

    public function sendSMSToSuplier($detail)
    {
        $prepareMsg = '';
        $prepareMsg .= "Assalaam-O-Alaikum " . '(' . $detail['business_name'] . ')' . "\n";
        $prepareMsg .= 'Thanku For The Milk Supply' . "\n";
        $prepareMsg .= 'Date : ' . date('Y-m-d h:i', time()) . "\n";
        $prepareMsg .= 'Collection Point : ' . $detail['collection_point'] . "\n";
        $prepareMsg .= 'Gross Volume : ' . $detail['gross_volume'] . "\n";
        $prepareMsg .= 'Fat : ' . $detail['fat'] . "\n";
        $prepareMsg .= 'LR : ' . $detail['lr'] . "\n";
        $prepareMsg .= 'SNF : ' . $detail['snf'] . "\n";
        $prepareMsg .= 'Adj.Volume 13 TS :' . $detail['ts_volume'] . "\n\n";
        $prepareMsg .= 'In case of any query please call to Fauji Foods Limited Representative' . "\n";
        $contactNum = str_replace('-', '', $detail['number']);
        $result = (new JazzCMS)->sendSMS($contactNum, $prepareMsg);
        return $result;
    }
    public function sendSMSToSuplierVoid($detail)
    {
        $prepareMsg = '';
        $prepareMsg .= "Assalaam-O-Alaikum " . '(' . $detail['business_name'] . ')' . "\n";
        $prepareMsg .= 'Hope you\'re well.'. "\n";
        $prepareMsg .= 'We need to void a recent transaction due to an error.'. "\n";
        $prepareMsg .= 'Date : ' . date('Y-m-d h:i', time()) . "\n";
        $prepareMsg .= 'Collection Point : ' . $detail['collection_point'] . "\n";
        $prepareMsg .= 'Gross Volume : ' . $detail['gross_volume'] . "\n";
        $prepareMsg .= 'Fat : ' . $detail['fat'] . "\n";
        $prepareMsg .= 'LR : ' . $detail['lr'] . "\n";
        $prepareMsg .= 'SNF : ' . $detail['snf'] . "\n";
        $prepareMsg .= 'Adj.Volume 13 TS :' . $detail['ts_volume'] . "\n\n";
        $prepareMsg .= 'Date & Time Of Previous Message :' . $detail['message_time'] . "\n\n";
        $prepareMsg .= 'In case of any query please call to Fauji Foods Limited Representative' . "\n";
        $contactNum = str_replace('-', '', $detail['number']);
        $result = (new JazzCMS)->sendSMS($contactNum, $prepareMsg);
        return $result;
    }
}