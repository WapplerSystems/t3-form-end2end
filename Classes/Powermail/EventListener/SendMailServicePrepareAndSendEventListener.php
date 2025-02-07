<?php

declare(strict_types=1);

namespace WapplerSystems\FormEnd2End\Powermail\EventListener;


use In2code\Powermail\Events\SendMailServicePrepareAndSendEvent;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class SendMailServicePrepareAndSendEventListener
{

    public function __invoke(SendMailServicePrepareAndSendEvent $event): void
    {

        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $headers = $request->getHeaders();
        $e2eKey = $headers['e2e-key'][0] ?? '';

        $key = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('form_end2end', 'key');

        if ($e2eKey !== '' && $e2eKey === $key) {

            $email = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('form_end2end', 'email');

            $mail = $event->getSendMailService()->getMail();
            $type = $event->getSendMailService()->getType();

            $subject = $event->getMailMessage()->getSubject();
            $subject .= ' [form:' . $mail->getUid() . ',type:' . $type . ']';
            $event->getMailMessage()->setSubject($subject);

            $address = new Address($email);
            $event->getMailMessage()->setTo([$address]);

        }



    }

}
