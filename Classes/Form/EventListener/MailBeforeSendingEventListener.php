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

        $remoteHosts = explode(',',GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('form_end2end', 'remoteHosts'));

        if (($e2eKey !== '' && $e2eKey === $key) || $this->checkHostAgainstPatterns($request->getServerParams()['REMOTE_ADDR'], $remoteHosts)) {

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

            $tos = $mail->getTo();
            foreach ($tos as $to) {
                $mail->getHeaders()->addTextHeader('X-E2E-Orig-To', $to->getAddress());
            }
            $mail->getHeaders()->addTextHeader('X-E2E-Form', $event->getFinisherContext()->getFormRuntime()->getFormDefinition()->getPersistenceIdentifier());
            $mail->getHeaders()->addTextHeader('X-E2E-Formtype', $type);
            $mail->getHeaders()->addTextHeader('X-E2E-Language', $siteLanguage->getLocale()->getName());

            $address = new Address($email);
            $mail->to($address);

        }
    }


    private function checkHostAgainstPatterns($host, $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern === '') {
                continue; // Leeres Muster überspringen
            }
            // Ersetzen von Wildcards (*) durch reguläre Ausdrucks-Syntax (.*)
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';

            if (preg_match($regex, $host)) {
                return true; // Übereinstimmung gefunden
            }
        }
        return false; // Keine Übereinstimmung gefunden
    }

}
