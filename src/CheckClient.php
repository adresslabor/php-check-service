<?php

namespace Adresslabor\CheckService;

use Adresslabor\CheckService\Exception\InvalidProductException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;

/**
 * The Adresslabor CheckClient is a utility class for the Adresslabor address validation service.
 * For more information visit <a href="https://adresslabor.de" target="_blank">adresslabor.de</a>
 */
class CheckClient
{
    const BASE_URL = 'https://api.adresslabor.de/';
    /** @var string */
    const PRODUCT_KEYS = 'sc,scx,sc_int,fk,nc,em,emx,pb,pbt,vatid,vatidx';
    
    /**
     * @var string
     * @deprecated for new implementations use the latest version instead
     */
    const PATH_V1 = '/v1/de/check';
    
    /**
     * @var string
     * @deprecated for new implementations use the latest version instead
     */
    const PATH_V2 = '/v2/de/check';
    
    /** @var string */
    const PATH_V3 = '/v3/de/check';
    
    /** @var Client */
    private $client;
    
    /** @var bool */
    private $assoc;
    
    /** @var string */
    private $cid;
    
    /** @var string */
    private $apiKey;
    
    public $credits;
    
    /**
     * @param string $apiCid Can be found on the <a href="https://addresslabor.de/en/my-account.html">my account page
     *                       </a>
     * @param string $apiKey Can be found on the <a href="https://addresslabor.de/en/my-account.html">my account page
     *                       </a>
     * @param bool   $assoc  When <b>TRUE</b>, returned objects will be converted into associative arrays.
     *
     * @throws GuzzleException|InvalidProductException
     */
    public function __construct(string $apiCid, string $apiKey, bool $assoc = true)
    {
        $this->cid = $apiCid;
        $this->apiKey = $apiKey;
        
        $this->assoc = $assoc;
        
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 0,
            'allow_redirects' => true,
        ]);
        
        // make a vatNumberCheck to test credentials and get your credits
        $this->vatNumberCheck("");
    }
    
    /**
     * @param string      $street
     * @param string      $hno
     * @param string      $zip
     * @param string      $city
     * @param string|null $country  optional, default null
     * @param bool        $extended optional, default false
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function addressCheckDACH(
        string $street,
        string $hno,
        string $zip,
        string $city,
        string $country = null,
        bool $extended = false
    ) {
        $product = $extended ? 'scx' : 'sc';
        
        $data = [
            'product' => $product,
            'street' => $street,
            'hno' => $hno,
            'zip' => $zip,
            'city' => $city,
            'country' => $country
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists($product, $result) ? $result[$product] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, $product) ? $result->$product : null;
    }
    
    /**
     * @param string      $street
     * @param string      $zip
     * @param string      $city
     * @param string      $country
     * @param string      $state
     * @param string      $zipAddOn
     * @param string      $location
     * @param string      $houseEstate
     * @param string      $subBuildingName
     * @param string      $organisation
     * @param string|null $hno optional, default null
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function addressCheckWorld(
        string $street,
        string $zip,
        string $city,
        string $country,
        string $state,
        string $zipAddOn,
        string $location,
        string $houseEstate,
        string $subBuildingName,
        string $organisation,
        string $hno = null
    ) {
        $data = [
            'product' => 'sc_int',
            'street' => $street,
            'hno' => $hno,
            'zip' => $zip,
            'city' => $city,
            'country' => $country,
            'state' => $state,
            'zip_add_on' => $zipAddOn,
            'location' => $location,
            'house_estate' => $houseEstate,
            'sub_building_name' => $subBuildingName,
            'organisation' => $organisation
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists('sc_int', $result) ? $result['sc_int'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'sc_int') ? $result->sc_int : null;
    }
    
    /**
     * @param string      $firstname
     * @param string      $lastname
     * @param string|null $street  optional, default null
     * @param string|null $hno     optional, default null
     * @param string|null $zip     optional, default null
     * @param string|null $city    optional, default null
     * @param string|null $country optional, default null
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function fakeCheck(
        string $firstname,
        string $lastname,
        string $street = null,
        string $hno = null,
        string $zip = null,
        string $city = null,
        string $country = null
    ) {
        $data = [
            'product' => 'fk',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'street' => $street,
            'hno' => $hno,
            'zip' => $zip,
            'city' => $city,
            'country' => $country
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists('fk', $result) ? $result['fk'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'fk') ? $result->fk : null;
    }
    
    /**
     * @param string      $firstname
     * @param string      $lastname
     * @param string|null $salutation optional, default null
     * @param string|null $title      optional, default null
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function nameCheckB2C(
        string $firstname,
        string $lastname,
        string $salutation = null,
        string $title = null
    ) {
        $data = [
            'product' => 'nc',
            'salutation' => $salutation,
            'title' => $title,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        
        if ($this->assoc) {
            return array_key_exists('nc', $result) ? $result['nc'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'nc') ? $result->nc : null;
    }
    
    /**
     * @param string $email
     * @param bool   $extended optional, default false
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function emailCheck(
        string $email,
        bool $extended = false
    ) {
        $data = [
            'product' => $extended ? 'emx' : 'em',
            'email' => $email,
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists('em', $result) ? $result['em'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'em') ? $result->em : null;
    }
    
    /**
     * @param string      $firstname
     * @param string      $lastname
     * @param string      $street
     * @param string      $zip
     * @param string      $city
     * @param string      $country
     * @param string      $phone
     * @param string|null $title     optional, default null
     * @param string|null $hno       optional, default null
     * @param bool        $payPerHit optional, default false
     *
     * @return mixed
     * @throws GuzzleException
     * @throws InvalidProductException
     */
    public function telephoneDirectory(
        string $firstname,
        string $lastname,
        string $street,
        string $zip,
        string $city,
        string $country,
        string $phone,
        string $title = null,
        string $hno = null,
        bool $payPerHit = false
    ) {
        $data = [
            'product' => $payPerHit ? 'pbt' : 'pb',
            'title' => $title,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'street' => $street,
            'hno' => $hno,
            'zip' => $zip,
            'city' => $city,
            'country' => $country,
            'phone' => $phone
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists('pb', $result) ? $result['pb'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'pb') ? $result->pb : null;
    }
    
    /**
     * @param string $vatId
     * @param bool   $extended optional, default false
     *
     * @return mixed
     * @throws GuzzleException|InvalidProductException
     */
    public function vatNumberCheck(
        string $vatId,
        bool $extended = false
    ) {
        $data = [
            'product' => $extended ? 'vatidx' : 'vatid',
            'vatin' => $vatId,
        ];
        
        $result = $this->check(self::PATH_V3, $data);
        
        if ($this->assoc) {
            return array_key_exists('vatid', $result) ? $result['vatid'] : null;
        }
        
        /** @var stdClass $result */
        return is_object($result) && property_exists($result, 'vatid') ? $result->vatid : null;
    }
    
    
    /**
     * @param string $path
     * @param array  $data
     *
     * @return mixed
     * @throws GuzzleException
     * @throws InvalidProductException
     */
    public function check(
        string $path,
        array $data
    ) {
        
        $this->validateProductKeys($data['product']);
        
        $url = (new Uri())->withPath($path);
        
        $data = array_merge($data, [
            'apicid' => $this->cid,
            'apikey' => $this->apiKey
        ]);
        
        $params = [
            'form_params' => $data,
        ];
        
        $result = $this->client
            ->post($url, $params)
            ->getBody()
            ->getContents();
        
        $result = json_decode($result, $this->assoc);
        
        $this->credits = $this->assoc
            ? $result['credits']
            : $result->credits;
        
        return $result;
    }
    
    /**
     * @param string $products comma separated product keys
     *
     * @return void
     * @throws InvalidProductException
     */
    private function validateProductKeys(string $products): void
    {
        $allowedKeys = explode(',', self::PRODUCT_KEYS);
        $productKeys = explode(',', $products);
        
        foreach ($productKeys as $productKey) {
            if (!in_array(strtolower($productKey), $allowedKeys)) {
                throw new InvalidProductException(sprintf(
                    'invalid product key "%s"! allowed product keys are %s.',
                    $productKey,
                    self::PRODUCT_KEYS
                ));
            }
        }
    }
    
}
