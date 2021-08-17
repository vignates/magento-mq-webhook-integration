<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Klevu\ProductIntegration\Consumer;

use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Product Integration consumer class
 */
class ProductIntegration
{
    const SYSTEM_A_ENDPOINT = "https://e926e5577c0b5a5e0fcdb5701cf72efb.m.pipedream.net";
    const SYSTEM_B_ENDPOINT = "https://ad8adc92bc6c2dec6639ebcb2228a492.m.pipedream.net";
    private $productAction;
    private $storeManager;
    private $productRepository;
    private $clientFactory;
    private $logger;

    public function __construct(
        ProductAction $action,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ClientFactory $clientFactory,
        LoggerInterface $logger
    )
    {
        $this->productAction = $action;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    /**
     * Consumer operation
     * @param UpdateProductDataManagementInterface $operation
     * @return void
     */
    public function consumerProcess($operation)
    {
        try{
            $products = json_decode($operation,true);
            $storeId = $this->storeManager->getStore()->getId();
            [$changedIds,$changedProducts] = $this->saveProduct($products,$storeId);
            
            if(!empty($changedProducts)){
                $this->sendToSystemA($changedProducts);
            }
    
            if(!empty($changedIds)){
                $this->sendToSystemB($changedIds);
            }
    
            print_r($changedIds);
            print_r($changedProducts);
    
        } catch(\Exception $e){
            $this->logger->critical('Error found during processing: ', ['exception' => $e]);
        }
    }

    /**
     * Data to be saved in Magento
     * @param array $products
     * @param int $storeId
     * @return array
     */
    protected function saveProduct($products,$storeId)
    {
        if(!empty($products)){
           $changedIds = $changedProducts = [];
           foreach($products as $product){
                try {
                    //Just to make sure product exists. Else it'll throw exception
                    $this->productRepository->getById($product["id"]);

                    $this->productAction->updateAttributes([$product["id"]], 
                            array(
                                'name' => $product["name"], 
                                'price' => $product["prices"]["was"],
                                'special_price' => $product["prices"]["now"]
                            ),
                            $storeId);
                    $changedIds[] = $product["id"];
                    $changedProducts[] = ["id"=>$product["id"],"price"=>$product["prices"]["now"],"images"=>$product["images"][0]];
                    $this->logger->info('Product updated for ID: '.$product["id"]);
                    
                } catch (\Exception $e) {
                    $this->logger->critical('Requested product is not found: ', ['exception' => $e]);
                    continue;
                }
            }

            return [$changedIds,$changedProducts];
        }

    }

    /**
     * Outgoing request to Remote System A
     * @param array $data
     * @return void
     */
    protected function sendToSystemA(array $data)
    {
        try{
            $client = $this->clientFactory->create(['config' => [
                'base_uri' => self::SYSTEM_A_ENDPOINT
            ]]);

            $client->post(self::SYSTEM_A_ENDPOINT, [
                'headers'         => ['Content-Type' => 'application/json'],
                'json'            => json_encode($data)
            ]);
        } catch(GuzzleException $e){
            $this->logger->critical('Error sending data to System A: ', ['exception' => $e]);
        }
    }

    /**
     * Outgoing request to Remote System B
     * @param array $data
     * @return void
     */
    protected function sendToSystemB(array $data)
    {
        try{
            $client = $this->clientFactory->create(['config' => [
                'base_uri' => self::SYSTEM_B_ENDPOINT
            ]]);

            $client->get('?ids='.implode(",",$data));
        } catch(GuzzleException $e){
            $this->logger->critical('Error sending data to System A: ', ['exception' => $e]);
        }
    }
}