<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Klevu\ProductIntegration\Publisher;
use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Add Product data To Queue
 */
class ProductDataPublisher{

    const TOPIC_NAME= "product_integration";
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * CustomerPublisher constructor.
     * @param PublisherInterface $publisher
     */
    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    /**
     * Publisher
     * @param UpdateProductDataManagementInterface $data
     */
    public function publish(array $data)
    {
        return $this->publisher->publish(self::TOPIC_NAME, json_encode($data));
    }
}