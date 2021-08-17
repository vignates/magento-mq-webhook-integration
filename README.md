# Magento 2 Module Klevu ProductIntegration

    `klevu/module-productintegration`

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)



## Main Functionalities
Module to update product using webhooks and send updates to remote systems

## Installation
\* = in production please use the `--keep-generated` option

### Zip file

 - Unzip the zip file in `app/code/Klevu`
 - Enable the module by running `php bin/magento module:enable Klevu_ProductIntegration`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Flush the cache by running `php bin/magento cache:flush`

## Configuration

 - Configure Message queues
 - Run `php bin/magento setup:upgrade` to create new topic in queue for Product integration
 - Run `php bin/magento queue:consume:list` to list all the available queues and you'll find `productIntegrationConsumer` if MQ is set properly
 - Run `php bin/magento queue:consume:start productIntegrationConsumer` to start the queue


## Specifications

 - API Endpoint
	- POST - /rest/V2/klevu-productintegration/updateproductdata
    - Authentication - Anonymous(No tokens needed to test)
 - Remote system Endpoint
    - System A(POST) - https://e926e5577c0b5a5e0fcdb5701cf72efb.m.pipedream.net
    - System B(GET) - https://ad8adc92bc6c2dec6639ebcb2228a492.m.pipedream.net




