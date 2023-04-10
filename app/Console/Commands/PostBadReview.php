<?php

namespace App\Console\Commands;

use App\Service\AdsService;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Facebook\WebDriver\Remote\RemoteWebDriver;

define('SELENIUM_URL','127.0.0.1:9515');

class PostBadReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PostBadReview {post_url} {times=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发布差评';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $times = $this->argument('times');
        $post_url = $this->argument('post_url');
        for ($i = 1; $i <= $times; $i++) {
            //配置浏览器信息
            $ads = new AdsService();
            $return = $ads->createBrowser(['cookie'=>'[{"domain":".facebook.com","expiry":1710309997103,"httpOnly":true,"name":"datr","path":"/","priority":"Medium","sameParty":false,"secure":true,"session":false,"size":24,"sourcePort":443,"sourceScheme":"Secure","value":"3LwOZMiBJAKvpDS2jXJXXV3v"},{"domain":".facebook.com","expiry":1710309997103,"httpOnly":false,"name":"dpr","path":"/","priority":"Medium","sameParty":false,"secure":false,"session":false,"size":1,"sourcePort":443,"sourceScheme":"Secure","value":"2"},{"domain":".facebook.com","expiry":1710309997103,"httpOnly":true,"name":"sb","path":"/","priority":"Medium","sameParty":false,"secure":true,"session":false,"size":24,"sourcePort":443,"sourceScheme":"Secure","value":"87wOZD_cR4cDxBTFSqInixnW"},{"domain":".facebook.com","expiry":1710309997103,"httpOnly":false,"name":"c_user","path":"/","priority":"Medium","sameParty":false,"secure":true,"session":false,"size":15,"sourcePort":443,"sourceScheme":"Secure","value":"100090421148530"},{"domain":".facebook.com","expiry":1710309997103,"httpOnly":true,"name":"xs","path":"/","priority":"Medium","sameParty":false,"secure":true,"session":false,"size":45,"sourcePort":443,"sourceScheme":"Secure","value":"3%3Aa8OWlUC9oGABiA%3A2%3A1678687577%3A-1%3A-1"},{"domain":".facebook.com","expiry":1710309997103,"httpOnly":false,"name":"wd","path":"/","priority":"Medium","sameParty":false,"secure":true,"session":false,"size":7,"sourcePort":443,"sourceScheme":"Secure","value":"360x748"}]']);
            //成功创建浏览器
            if ($return) {
                $ret = $ads->startBrowser();
                if (!$ret) {
                    echo $i.'error';
                    continue;
                }
                //exec('nohup '.$ads->webdriver);
                $chrome_options = new ChromeOptions();
                $chrome_options->setExperimentalOption("debuggerAddress", $ads->selenium);
                $capabilities = DesiredCapabilities::chrome();
                $capabilities->setCapability(ChromeOptions::CAPABILITY, $chrome_options);
                $driver = RemoteWebDriver::create(SELENIUM_URL, $capabilities);
                try {
                    //修改语言
                    $driver->get('https://m.facebook.com');
                    //尝试加好友
                    try {
                        $driver->findElement(WebDriverBy::cssSelector('._5s61._52z8'))->click();
                    }catch (\Exception $exception){

                    }

                    $driver->findElements(WebDriverBy::cssSelector('._4g34'))[8]->click();
                    sleep(3);
                    $driver->executeScript("window.scrollBy(0, 500);");
                    $driver->findElements(WebDriverBy::cssSelector('._6rvl'))[2]->click();
                    $driver->executeScript("window.scrollBy(0, 500);");
                    $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/div[4]/div/div/div/div[4]/ul/li/div/ul/li/a[2]'))->click();
                    sleep(3);
                    $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'English (US)')]"))->click();
                    //打开指定链接
                    $driver->get($post_url);
                    sleep(3);
                    //举报
                    $driver->findElement(WebDriverBy::cssSelector('.xqcrz7y.x78zum5.x1qx5ct2.x1y1aw1k.x1sxyh0.xwib8y2.xurb0ha.xw4jnvo')) // find search input element
                    ->click();
                    sleep(3);
                    $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'Report post')]"))->click();
                    sleep(3);
                    $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'False information')]"))->click();
                    sleep(3);
                    $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'Health')]"))->click();
                    sleep(3);
                    $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'Submit')]"))->click();
                }
                catch (\Exception $exception){
                    echo $exception->getMessage();
                    continue;
                }
            }
        }
    }
}
