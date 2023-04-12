<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\AccountLog;
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
            //获取可用cookie
            $account = Account::where('can_use','1')->first();
            if (!$account||!isset($account->cookie)){
                exit('出错了');
            }
            $account->can_use = 0;
            $account->save();
            $return = $ads->createBrowser(['cookie'=>$account->cookie]);
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
                    sleep(3);
                    //尝试语言选择
                    try {
                        $driver->findElement(WebDriverBy::xpath("//*[contains(text(), 'Language for buttons, titles and other text from Facebook for this account on www.facebook.com')]"))->click();
                    }catch (\Exception $exception){

                    }
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
                    $driver->close();
                    sleep(5);
                    $ads->deleteBrowser();
                }
                catch (\Exception $exception){
                    $driver->close();
                    sleep(5);
                    $ads->deleteBrowser();
                    continue;
                }
            }
        }
    }
}
