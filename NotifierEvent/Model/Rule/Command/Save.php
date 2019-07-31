<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use MSP\NotifierEvent\Model\ResourceModel\Rule;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Save implements SaveInterface
{
    /**
     * @var Rule
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ValidateRuleInterface
     */
    private $validateRule;

    /**
     * @param Rule $resource
     * @param ValidateRuleInterface $validateRule
     * @param LoggerInterface $logger
     */
    public function __construct(
        Rule $resource,
        ValidateRuleInterface $validateRule,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->validateRule = $validateRule;
    }

    /**
     * @inheritdoc
     */
    public function execute(RuleInterface $rule): int
    {
        $this->validateRule->execute($rule);

        try {
            $this->resource->save($rule);
            return (int) $rule->getId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Rule'), $e);
        }
    }
}
