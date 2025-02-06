<?php

declare(strict_types=1);

namespace WapplerSystems\MailEnd2End\Powermail\EventListener;

use In2code\Powermail\Events\ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\DebugUtility;

final class ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEventListener
{
    public function __invoke(ReceiverMailReceiverPropertiesServiceSetReceiverEmailsEvent $event): void
    {

        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $headers = $request->getHeaders();
        $zabbixKey = $headers['e2e-monitoring-key'][0] ?? '';
        if ($zabbixKey !== '') {

            $event->setEmailArray(['']);

        }

        DebugUtility::debug($event);
        exit();

    }
}
