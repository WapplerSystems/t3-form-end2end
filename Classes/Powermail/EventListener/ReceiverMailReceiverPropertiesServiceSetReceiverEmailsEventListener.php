<?php

declare(strict_types=1);

namespace WapplerSystems\FormEnd2End\Powermail\EventListener;

use In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEventListener
{
    public function __invoke(ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent $event): void
    {

        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $headers = $request->getHeaders();
        $zabbixKey = $headers['e2e-key'][0] ?? '';

        $key = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('form_end2end', 'key');

        if ($zabbixKey !== '' && $zabbixKey === $key) {

            $email = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('form_end2end', 'email');

            $event->setEmailArray([$email]);
        }

    }
}
