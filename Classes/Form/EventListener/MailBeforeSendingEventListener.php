<?php


declare(strict_types=1);

namespace WapplerSystems\FormEnd2End\Form\EventListener;


use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\FormExtended\Event\MailBeforeSendingEvent;

final class MailBeforeSendingEventListener
{

    public function __invoke(MailBeforeSendingEvent $event): void
    {
        /** @var ServerRequestInterface $request */
        $request = $event->getFinisherContext()->getFormRuntime()->getRequest();
        $headers = $request->getHeaders();
        $e2eKey = $headers['e2e-key'][0] ?? '';

        $key = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('form_end2end', 'key');

        if ($e2eKey !== '' && $e2eKey === $key) {

            $email = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('form_end2end', 'email');

            $mail = $event->getMail();
            $identifier = $event->getFinisher()->getFinisherIdentifier();

            $type = '';
            if ($identifier === 'EmailToSender') {
                $type = 'sender';
            } else if ($identifier === 'EmailToReceiver') {
                $type = 'receiver';
            }

            /** @var SiteLanguage $siteLanguage */
            $siteLanguage = $request->getAttribute('language');

            $subject = $mail->getSubject();
            $subject .= ' [form:' . $event->getFinisherContext()->getFormRuntime()->getFormDefinition()->getPersistenceIdentifier() . ',type:' . $type . ',lang:' . $siteLanguage->getLocale()->getName() . ']';
            $mail->subject($subject);

            $address = new Address($email);
            $mail->to($address);

        }
    }


}
