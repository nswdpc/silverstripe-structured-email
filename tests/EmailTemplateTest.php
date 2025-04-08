<?php

namespace NSWDPC\StructuredEmail\Tests;

use NSWDPC\StructuredEmail\StructuredEmailProcessor;
use SilverStripe\Control\Email\Email;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class EmailTemplateTest extends SapphireTest
{
    protected static $fixture_file = 'EmailTemplateTest.yml';

    protected static $illegal_extensions = [
        Member::class => '*',
    ];

    protected $usesDatabase = false;

    private function saveOutput($html, string $template, string $ext = ".html")
    {
        $full = __DIR__ . '/__output/' . $template;
        $path = dirname($full);
        @mkdir($path, 0700, true);
        file_put_contents($full . $ext, $html);
    }

    public function testStructuredEmailProcessor()
    {
        $email = Email::create();
        $expected = 'test pre header';
        $processor = StructuredEmailProcessor::create($email);
        $processor->setPreHeader($expected);
        $email->addData('StructuredEmailProcessor', $processor);
        $this->assertEquals($expected, $email->getData()->StructuredEmailProcessor->getPreheader());
    }

    public function testNoStructuredEmailProcessor()
    {
        $email = Email::create();
        $this->assertNull($email->getData()->StructuredEmailProcessor);
    }

    public function testTemplate(): void
    {
        $data = [
            'Body' => file_get_contents(__DIR__ . '/data/template.html')
        ];

        $subject = 'Welcome to the show';

        $email = Email::create();
        $email->setTo("to@example.com");
        $email->setCc("cc@example.com");
        $email->setBcc("bcc@example.com");
        $email->setFrom(["from@example.com" => "Jiminy Crickets", "another@example.com" => "Bob Pokemon"]);
        $email->addFrom("secondary@example.com");
        $email->setSubject($subject);
        $email->setData($data);

        $processor = StructuredEmailProcessor::create($email);
        $processor->setPreheader('Test generic email that needs your attention');
        $processor->setViewAction('Confirm your identify', 'https://confirm.example.com?token=suitably-long-token');
        $email->addData('StructuredEmailProcessor', $processor);

        $email->send();

        $html = $email->getHtmlBody();

        $this->saveOutput($html, "StructuredEmail");

        $message = $email->toString();
        $this->saveOutput($message, "StructuredEmail", ".txt");


        // assert email contains subject
        $this->assertStringContainsString(
            "Subject: " . $subject,
            $message
        );
    }

    /**
     * Send a forgot password email
     */
    public function testForgotEmail(): void
    {
        $template = 'SilverStripe/Control/Email/ForgotPasswordEmail';
        $token = "really-bad-token";
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        /** @phpstan-ignore argument.type */
        $resetPasswordLink = Security::getPasswordResetLink($member, $token);
        $subject = _t(
            'SilverStripe\\Security\\Member.SUBJECTPASSWORDRESET',
            "Your password reset link",
            'Email subject'
        );
        $email = Email::create()
            ->setHTMLTemplate($template)
            ->setData($member)
            ->setSubject($subject)
            ->addData('PasswordResetLink', $resetPasswordLink)
            ->setTo($member->Email);
        $email->send();

        $html = $email->getHtmlBody();

        $this->saveOutput($html, "ForgotPasswordEmail");

        $message = $email->toString();
        $this->saveOutput($message, "ForgotPasswordEmail", ".txt");

        // test that email is rendered with reset link and in structured email
        $this->assertStringContainsString(
            htmlspecialchars($resetPasswordLink), // entitised link
            $html // in the HTML
        );

        // assert email contains subject
        $this->assertStringContainsString(
            "Subject: " . $subject,
            $message
        );
    }

    public function testChangePassword(): void
    {
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        $subject = _t(
            'SilverStripe\\Security\\Member.SUBJECTPASSWORDCHANGED',
            "Your password has been changed",
            'Email subject'
        );
        $email = Email::create()
            ->setHTMLTemplate('SilverStripe\\Control\\Email\\ChangePasswordEmail')
            ->setData($member)
            ->setTo($member->Email)
            ->setFrom('from@example.com')
            ->setSubject($subject);
        $email->send();

        $this->saveOutput($email->getHtmlBody(), "ChangePasswordEmail");

        $message = $email->toString();
        $this->saveOutput($message, "ChangePasswordEmail", ".txt");

        // assert email contains subject
        $this->assertStringContainsString(
            "Subject: " . $subject,
            $message
        );
    }

    public function testStandardEmail(): void
    {
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        $subject = 'Subject of an important message';
        $email = Email::create()
            ->setHTMLTemplate(\SilverStripe\Control\Email\Email::class)
            ->setData([
                'EmailContent' => file_get_contents(__DIR__ . '/data/template.html')
            ])
            ->setTo($member->Email)
            ->setFrom('from@example.com')
            ->setSubject($subject);
        $email->send();

        $this->saveOutput($email->getHtmlBody(), "StandardEmail");

        $message = $email->toString();
        $this->saveOutput($message, "StandardEmail", ".txt");

        // assert email contains subject
        $this->assertStringContainsString(
            "Subject: " . $subject,
            $message
        );
    }
}
