<?php

namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
class MailServiceContainer {
    private MailerInterface $mailerInterface;

    public function __construct(MailerInterface $mailerInterface) {
        $this->mailerInterface = $mailerInterface;
    }


    public function send(string $recipient, string $sender, string $subject, string $template, array $variables) {
        $email = (new TemplatedEmail())
            ->from($sender)
            ->to(Address::create($recipient))
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($variables)
        ;
        $this->mailerInterface->send($email);
    }
}