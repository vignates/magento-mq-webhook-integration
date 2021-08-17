<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Klevu\ProductIntegration\Model;

use Magento\Framework\Webapi\Rest\Request;
use Klevu\ProductIntegration\Publisher\ProductDataPublisher;

class UpdateProductDataManagement implements \Klevu\ProductIntegration\Api\UpdateProductDataManagementInterface
{

    const SUCCESS_MESSAGE = "Data is received and will be processed Asynchronously";
    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;
    /**
     * @var ProductDataPublisher
     */
    private $publisher;
    /**
     * constructor
     *
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Klevu\ProductIntegration\Publisher\ProductDataPublisher $publisher
     */
    public function __construct(
        Request $request,
        ProductDataPublisher $publisher
    ) {
        $this->request = $request;
        $this->publisher = $publisher;
    }
 
    /**
     * {@inheritdoc}
     */
    public function postUpdateProductData()
    {

        try{
            $param = ($this->request->getBodyParams());
            $this->publisher->publish($param);
            return self::SUCCESS_MESSAGE;
        }catch(\Exception $e){
            return $e->getMessage();
        }

    }
}

