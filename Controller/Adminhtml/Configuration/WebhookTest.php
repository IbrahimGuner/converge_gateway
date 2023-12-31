<?php
/**
 * Adyen Payment Module
 *
 * Copyright (c) 2022 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Controller\Adminhtml\Configuration;

use Adyen\AdyenException;
use Adyen\Payment\Helper\Config;
use Adyen\Payment\Helper\ManagementHelper;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;

class WebhookTest extends Action
{
    /**
     * @var ManagementHelper
     */
    private $managementApiHelper;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param ManagementHelper $managementApiHelper
     * @param JsonFactory $resultJsonFactory
     * @param StoreManager $storeManager
     * @param Config $configHelper
     */
    public function __construct(
        Context $context,
        ManagementHelper $managementApiHelper,
        JsonFactory $resultJsonFactory,
        StoreManager $storeManager,
        Config $configHelper
    ) {
        parent::__construct($context);
        $this->managementApiHelper = $managementApiHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws AdyenException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $storeId = $this->storeManager->getStore()->getId();

        $merchantAccount = $this->configHelper->getMerchantAccount($storeId);
        $webhookId = $this->configHelper->getWebhookId($storeId);
        $isDemoMode = $this->configHelper->isDemoMode($storeId);
        $environment = $isDemoMode ? 'test' : 'live';
        $apiKey = $this->configHelper->getApiKey($environment, $storeId);

        $managementApiService = $this->managementApiHelper->getManagementApiService($apiKey, $isDemoMode);
        $response = $this->managementApiHelper->webhookTest($merchantAccount, $webhookId, $managementApiService);

        $success = isset($response['data']) &&
            in_array('success', array_column($response['data'], 'status'), true);

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData([
            'messages' => $response,
            'statusCode' => $success ? 'Success' : 'Failed'
        ]);

        return $resultJson;
    }
}
