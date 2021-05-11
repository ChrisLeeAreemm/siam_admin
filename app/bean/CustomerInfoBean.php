<?php


namespace app\bean;


use EasySwoole\Spl\SplBean;

class CustomerInfoBean extends SplBean
{
    protected $customer_name;
    protected $customer_phone;
    protected $customer_address;

    /**
     * @return mixed
     */
    public function getCustomerName()
    {
        return $this->customer_name;
    }

    /**
     * @param mixed $customer_name
     */
    public function setCustomerName($customer_name): void
    {
        $this->customer_name = $customer_name;
    }

    /**
     * @return mixed
     */
    public function getCustomerPhone()
    {
        return $this->customer_phone;
    }

    /**
     * @param mixed $customer_phone
     */
    public function setCustomerPhone($customer_phone): void
    {
        $this->customer_phone = $customer_phone;
    }

    /**
     * @return mixed
     */
    public function getCustomerAddress()
    {
        return $this->customer_address;
    }

    /**
     * @param mixed $customer_address
     */
    public function setCustomerAddress($customer_address): void
    {
        $this->customer_address = $customer_address;
    }


}