<?php


namespace App\Traits;

use App\Library\CustomORM\AppCollection;
use Illuminate\Support\Facades\Storage;
use \ZipArchive;
use File;
use App\Models\User;
use Mail;
use Carbon\Carbon;


trait UtilityTrait
{
    /**
     * Method get value by given key from array.
     *
     * @param string     $key     key name
     * @param array      $arr     array
     * @param mixed|null $default default flag
     *
     * @return mixed
     */
    public function arrayGet(string $key, array $arr, $default = null)
    {
        if (is_array($arr) && array_key_exists($key, $arr) && !empty($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    /**
     * This will send mail
     *
     * @param string $toEmail  ToEmail
     * @param string $mailFrom MailFrom
     * @param string $mailName MailName
     * @param string $body     Body
     * @param string $subject  Subject
     *
     * @return void
     */
    public function sendMail(
        string $toEmail,
        string $mailFrom,
        string $mailName,
        array $body,
        string $subject,
        string $fileName
    ) {
        Mail::send(
            $fileName,
            ['body' => $body],
            function ($message) use ($toEmail, $body, $mailFrom, $mailName, $subject) {
                $message->to($toEmail)->subject($subject);
                $message->from($mailFrom, $mailName);
            }
        );
       
    }
// $this->sendMail($input['email'], $fromEmail, $fromName, $body, $subject, 'resetPassword');
    /**
     * This will retrive table name
     *
     * @return string Table Name
     */
    public function getTableName() :string
    {
        return $this->table;
    }

    /**
     * Converts an object to an array
     *
     * @param object $d object to be converted
     *
     * @return array Array convertido
     */
    public function objectToArray($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        return is_array($d) ? array_map(array($this, 'objectToArray'), $d) : $d;
    }

    /**
     * Passing model name for the pagination and sorting.
     *
     */
    public function newCollection(array $models = [])
    {
        return new AppCollection($models);
    }

    /**
     * This check if user has role or not
     *
     * @param int $roleId RoleId
     *
     * @return boolean  boolean
     */
    public function isUserHasRole(int $roleId)
    {
        $roles = UserMappingRole::where(
            [
                ['user_id' , $this->currentUser->id],
                ['role_id' , $roleId]
            ]
        )->get();
        if (count($roles) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This check card Type
     *
     * @param string $cardNumber cardNumber
     *
     * @return string  $cardType
     */
    public function cardType($cardNumber)
    {
        $prefix= substr($cardNumber, 0, 1);
        if ($prefix == "4" || $prefix == "1") {
            $cardType="visa";
        } elseif ($prefix =="5") {
            $cardType="master";
        } elseif ($prefix == "6") {
            $cardType="discover";
        } else {
            $cardType = "";
        }
        return $cardType;
    }

    /**
     * Verify if exit subarray
     *
     * @param array   $arr      -> array to verify if exits subarray
     * @param boolean $checkAll -> To check whether all the elements are array or not
     *
     * @return boolean     true if exist and false if not
     */
    public function existSubArray($arr, bool $checkAll = false): bool
    {
        $noOfArray =  0;
        foreach ($arr as $value) {
            if (is_array($value)) {
                if (false === $checkAll) {
                    return true;
                } else {
                    $noOfArray++;
                }
            }
        }

        if ($noOfArray === count($arr)) {
            return true;
        }

        return false;
    }

    /**
     * This check domain Name
     *
     * @param string $domainName domainName
     *
     * @return object $company
     */
    public function verifyDomainName($domainName = null)
    {
        $host = explode('.', $domainName);
        $subdomains = array_slice($host, 0, count($host) - 2);
        $domain = implode(".", $subdomains);
        $company = Company::where(
            [
                ['domain_name', $domain],
                ['is_deleted', 0],
                ['status', 0]
            ]
        )->first(
            ['id', 'company_name', 'is_deleted', 'status']
        );
        return $company;
    }

    /**
     * Create files to storage
     *
     * @param int    $companyId company Id
     * @param string $filePath  file path
     *
     * @return bool
     */
    public function extractFile(int $companyId, string $filePath) : bool
    {
        $zip =  new ZipArchive();
        $isCheck = $zip->open($filePath);
        if ($isCheck === true) {
            if (!is_dir(storage_path('json/'.$companyId))) {
                mkdir(storage_path('json/'.$companyId), 0777, true);
            }
            $zip->extractTo(storage_path('json/'.$companyId));
            $zip->close();
            return true;
        } else {
            $this->line('File Crashed! Please Chechk ZIP Files');
            return false;
        }
    }

    /**
     * Returns file name array
     *
     * @param int $companyId company Id
     *
     * @return array
     */
    public function getFilesname(int $companyId) : array
    {
        $exists = File::allFiles(storage_path().'/json/'.$companyId);
        foreach ($exists as $e) {
            $fname[] = pathinfo($e)['basename'];
        }
        return $fname;
    }


    /**
     * Read CSV file
     *
     * @param string $file file name
     *
     * @return array
     */
    public function readCSV(string $file) : array
    {
        $array = $fields = array();
        $i = 0;
        $handle = @fopen($file, "r");
        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                }
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        return $array;
    }

    /**
     * Read CSV file
     *
     * @param string $file file name
     *
     * @return array
     */
    public function readFirstRecord(string $file) : array
    {
        $array = $fields = array();
        $i = 0;
        $handle = @fopen($file, "r");
        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                }
                $i++;
                if ($i == 1) {
                    break;
                }
            }
            fclose($handle);
        }
        return $array;
    }

    /**
     * This will store primary user.
     *
     * @param array $data      Data
     * @param int   $companyId CompanyId
     *
     * @return void            description
     */
    private function savePrimaryUser(array $data, int $companyId)
    {
        $user = new User;
        $userId = $user->importRecord($data, $companyId);
        Company::where('id', $companyId)->update(
            ['admin_id' => $userId]
        );
    }

    /*
     * Generate string message from array
     *
     * @param array $message Message
     *
     * @return string          output
     */
    public function generateMessage(array $message)
    {
        $output = implode(
            ', ',
            array_map(
                function ($v, $k) {
                    return sprintf("%s %s ", $k, $v);
                },
                $message,
                array_keys($message)
            )
        );
        return $output;
    }

    /*
     * Generate SignedURL
     *
     * @param string $path Path
     * @param boolean $forceDownload Download
     *
     * @return string      output
     */
    public function generateSignedURL($path, $forceDownload = false)
    {
        if (env('APP_ENV') == 'testing') {
            return '';
        }
        if (!Storage::disk('s3')->exists($path)) {
            return false;
        }
        $s3 = Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = "+20 minutes";
        $params = array(
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $path,
        );
        if ($forceDownload) {
            $params['ResponseContentType'] = 'application/octet-stream';
            $params['ResponseContentDisposition'] = 'attachment; filename="' . basename($path) . '"';
        }
        $command = $client->getCommand('GetObject', $params);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string)$request->getUri();
    }

    /*
     * Generate SignedURL
     *
     * @param string $folderName folder Name
     * @param string $fileName file Name
     * @param string $path Path
     *
     * @return string filePath
     */
    public function awsBucketStorage($folderName = null, $fileName = null, $path = null)
    {
        if (env('APP_ENV') == 'testing') {
            return '';
        }
        Storage::disk('s3')->put($folderName . '/' . $fileName, file_get_contents($path));
        $filePath = $this->generateSignedURL(
            $folderName . '/' . $fileName,
            true
        );
        return $filePath;
    }

    /**
     * [apiObject description]
     *
     * @param int $companyId [description]
     *
     * @return [type]            [description]
     */
    public function getApiObject(int $companyId)
    {
        $apiCredentials = Company::find($companyId);
        $jsonArray = json_decode($apiCredentials->api_credentials, true);
        $jsonArray['timeZone'] = config('constants.timezone.' . $apiCredentials->timezone);
        $jsonArray['recurringStartTime'] = $apiCredentials->recurring_starttime;
        $jsonArray['recurringEndTime'] = $apiCredentials->recurring_endtime;
        $apiName = config('constants.apiLists.'.$apiCredentials->api_type);
        $apiObject = new $apiName($jsonArray);

        return $apiObject;
    }

    /*
     * CreateLogFile
     *
     * @param string $logFile logFile
     * @param string $message Message
     * @param array $context context
     *
     * @return void
     */
    public function createLogFile($logFile = 'log-lm.log', $message = null, $context = array())
    {
        $path='/tmp/';
        if (!file_exists($path.$logFile)) {
            $file = fopen($path.$logFile, 'a+');
            fclose($file);
            chmod($path.$logFile, 0777);
        }
        file_put_contents($path.$logFile, "\n".'message::'.$message."\n", FILE_APPEND);
        file_put_contents($path.$logFile, json_encode($context), FILE_APPEND);
    }


    public function searchLogFile($filename)
    {
        $path = '/tmp/'.$filename;

        return file_exists($path);
    }

    public function removeLogFile($filename)
    {
        try {
            $path = '/tmp/'.$filename;

            if (!file_exists($path)) {
                return false;
            }
            exec('sudo chown ubuntu:ubuntu '.$path);
            unlink($path);

            return true;
        } catch (\Exception $ex) {
        }
    }

    /**
     * This will show hide campaign and products
     *
     * @param int $userId    UserId
     * @param int $companyId CompanyId
     *
     * @return array
     */
    public function hiddenPermission(int $userId, int $companyId)
    {
        $data = HidePermission::where(
            [
                ['user_id', $userId],
                ['company_id', $companyId]
            ]
        )->get();

        $responseData = [];
        $campaignId = [];
        $productId = [];
        $apiCampaign = [];
        $apiProduct = [];

        if (count($data) > 0) {
            foreach ($data as $id) {
                if ($id['campaign_id'] != 0) {
                    $campaignId[] = $id['campaign_id'];
                    $apiCampaign[] = $id['campaigns']['campaign_id'];
                }
                if ($id['product_id'] != 0) {
                    $productId[] = $id['product_id'];
                    $apiProduct[] = $id['products']['product_id'];
                }
            }
            $responseData['product_id'] = implode(',', $productId);
            $responseData['campaign_id'] = implode(',', $campaignId);
            $responseData['api_product_id'] = implode(',', $apiProduct);
            $responseData['api_campaign_id'] = implode(',', $apiCampaign);
        } else {
            $responseData['product_id'] = implode(',', $productId);
            $responseData['campaign_id'] = implode(',', $campaignId);
            $responseData['api_product_id'] = implode(',', $apiProduct);
            $responseData['api_campaign_id'] = implode(',', $apiCampaign);
        }

        return $responseData;
    }

    /**
     * This will validate domain
     *
     * @param string $domainName DomainName
     *
     * @return boolean
     */
    public function validateDomain(string $domainName)
    {
        $domain = array(
            'mysql',
            'rds',
            'admin',
            'api',
            'www',
            'static',
            'administrator',
            'client',
            'database'
        );

        if (in_array(strtolower($domainName), $domain)) {
            return true;
        }
        return false;
    }

    /**
     * This will prepare Data for send email
     *
     * @param array $input input
     * @param string $password password
     *
     * @return boolean
     */
    public function prepareSendMailData(array $input, string $password)
    {
        $body = array(
            'username' => $input['username'],
            'password' => $password,
            'firstName' => $input['first_name'],
            'lastName' => $input['last_name'],
            'email' => $input['email']
        );
        $fromEmail = env('MAIL_FROM', 'archana.adsum@gmail.com');
        $fromName = env('MAIL_NAME', 'Regen-Real-Estate');
        $subject = "your credentials";
        $this->sendMail($input['email'], $fromEmail, $fromName, $body, $subject, 'verifyAccount');
    }

    /**
     * This will get campaign and product not in order
     *
     * @param int $companyId CompanyId
     *
     * @return void
     */
    public function deleteUnmappedOrders(int $companyId)
    {
        Order::whereDoesntHave(
            'productcampaign',
            function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            }
        )->where('company_id', $companyId)->update(['is_deleted' => 1]);
    }

    /**
     * This will get current site protocol as per url defined in env
     *
     * @param int $companyId CompanyId
     *
     * @return void
     */
    public function getProtocol()
    {
        $url = explode("://", env('APP_URL'));

        return $url[0];
    }

    /**
     * This will encrypt data
     *
     * @param int $data data
     *
     * @return void
     */
    public function encrypt($data)
    {
        $id = (double)$data * 253525.24;
        return base64_encode($id);
    }

    /**
     * This will decrypt data
     *
     * @param string $data data
     *
     * @return void
     */
    public function decrypt($data)
    {
        $urlId = base64_decode($data);
        $id = (double)$urlId / 253525.24;
        return $id;
    }


    /**
     * This will get Time Zone
     *
     * @param string $time time
     * @param int $flag flag 1 means date format "Y-m-d H:i:s"
     * @param string $timezone timezone
     * @param string $isCron flag is called from cron
     *
     * @return void
     */
    public function getTimeZone($time, $flag, $timezone = null, $isCron = 0)
    {
        date_default_timezone_set($timezone);
        $returnTime = Carbon::parse($time)->format("Y-m-d");
        if ($flag == 1) {
            $returnTime =Carbon::parse($time)->format("Y-m-d H:i:s");
        }
        if (!$isCron) {
            date_default_timezone_set('UTC');
        }

        return $returnTime;
    }

    /**
     * This will get Time(mostly used in main sale recurring/attempt time)
     *
     * @param string $startDate date
     * @param string $endDate date
     * @param string $startTime date
     * @param string $endTime date
     *
     * @return string
     */
    public function getMainTime($startDate, $endDate, $startTime, $endTime)
    {
        if ($startDate != $endDate) {
            $randomDate = Carbon::parse(
                rand(strtotime($startDate), strtotime($endDate))
            )->format("Y-m-d");
            $randomTime = Carbon::parse(
                rand(strtotime($startTime), strtotime($endTime))
            )->format("H:i:s");
            return $randomDate.' '.$randomTime;
        }
        $startDateTime = strtotime($startDate .' '. $startTime);
        $endDateTime   = strtotime($endDate .' '. $endTime);
        $newRecurringDate = Carbon::parse(
            rand($startDateTime, $endDateTime)
        )->format("Y-m-d H:i:s");
        return $newRecurringDate;
    }

    /**
     * This will get Time(mostly used in upsell time)
     *
     * @param string $time time
     *
     * @return string
     */
    public function getUpsellTime($time)
    {
        return  Carbon::parse(strtotime("+59 minutes", strtotime($time)))->format("Y-m-d H:i:s");
    }

    public function getRoundedNumber($number, $afterDigit = 2)
    {
        return number_format(
            (float)$number,
            $afterDigit,
            '.',
            ''
        );
    }
    
    /**
     * This will get local product id
     *
     * @param int $productId    User Id
     * @param int $campaignId  campaign Id
     * @param int $companyId Company Id
     *
     * @return string
     */
    public function getLocalProductId(int $productId, int $campaignId, int $companyId)
    {
        $localProductID = 0;
        if (!empty($productId) && !empty($campaignId)) {
            $locallProductData = Product::select('id', 'product_id')->where(
                [
                    ['product_id', $productId],
                    ['company_id', $companyId],
                    ['is_deleted', 0],
                    ['campaign_id', $campaignId]
                ]
            )->first();
            $localProductID = $locallProductData['id'] ?? 0;
        }
        return $localProductID;
    }
}
