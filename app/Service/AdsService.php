<?php

namespace App\Service;

use Illuminate\Support\Facades\URL;

define('ADS_URL', 'http://localhost:50325');
define('GROUP_ID', '2328592');
define('PROXY_HOST', '23.106.33.181');
define('PROXY_PORT', '50000');
define('PROXY_USER', 'user8000');
define('PROXY_PASSWORD', 'user');
define('PROXY_SOFT', 'other');

class AdsService
{
    public $id = '';
    public $serial_number = '';
    public $puppeteer = '';
    public $selenium = '';
    public $webdriver = '';
    public $debug_port = '';

    public function deleteBrowser(){
        $return = CurlService::post(ADS_URL . '/api/v1/user/delete', ['user_ids' => [$this->id]]);
        if (isset($return['code'])&&$return['code']==0){
            return true;
        }
        return false;
    }

    public function startBrowser($user_id = '')
    {
        $return = CurlService::get(ADS_URL . '/api/v1/browser/start', ['user_id' => $this->id,'launch_args'=>json_encode(['--disable-notifications'])]);
        if (isset($return['code'])&&$return['code']==0){
            $this->puppeteer = $return['data']['ws']['puppeteer'];
            $this->selenium = $return['data']['ws']['selenium'];
            $this->webdriver = str_replace(' ', '\ ', $return['data']['webdriver']);
            $this->debug_port = $return['data']['debug_port'];
            return true;
        }
        return false;
    }

    public function createBrowser($data=[])
    {
        $proxy = [
            'proxy_type' => 'http',
            'proxy_host' => PROXY_HOST,
            'proxy_port' => PROXY_PORT,
            'proxy_user' => PROXY_USER,
            'proxy_password' => PROXY_PASSWORD,
            'proxy_soft' => PROXY_SOFT
        ];
        $return = CurlService::post(ADS_URL . '/api/v1/user/create', [
            'group_id' => GROUP_ID,
            //'user_proxy_config' => ['proxy_soft'=>'no_proxy'],
            'user_proxy_config' => $proxy,
            'fingerprint_config' => [
                'automatic_timezone' => 1
            ]]);

        if (isset($return['code']) && $return['code'] == 0) {
            $this->id = $return['data']['id'];
            $this->serial_number = $return['data']['serial_number'];
        }else{
            return false;
        }
        if ($data){
            $this->updateBrowser($data);
        }
        return true;
    }

    public function updateBrowser($data=[]){
        $data = array_merge($data,['user_id'=>$this->id]);
        $return = CurlService::post(ADS_URL . '/api/v1/user/update',$data);
        if (isset($return['code']) && $return['code'] == 0) {
            return true;
        }
        throw new \Exception('更新浏览器配置失败');
    }

    public function getGroup()
    {
        return CurlService::get(ADS_URL . '/api/v1/group/list');
    }
}
