<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class EmailTemplateTest extends SapphireTest {

    protected static $fixture_file = 'EmailTemplateTest.yml';

    protected static $illegal_extensions = [
        Member::class => '*',
    ];

    protected $usesDatabase = false;

    private function saveOutput($html, $template,  $ext = ".html") {
        $full = dirname(__FILE__) . '/__output/' . $template;
        $path = dirname($full);
        @mkdir($path, 0700, true);
        file_put_contents($full . $ext, $html);
    }

    public function testTemplate() {

        $data = [
            'Body' => file_get_contents(dirname(__FILE__) . '/data/template.html'),
            'Preheader' => 'Test generic email that needs your attention'
        ];

        $email = StructuredEmail::create();
        $email->setTo("to@example.com");
        $email->setCc("cc@example.com");
        $email->setBcc("bcc@example.com");
        $email->setFrom("from@example.com");
        $email->setData($data);
        $email->Send();

        $html = $email->getBody();

        $this->saveOutput($html, "StructuredEmail");

        $message = $email->getSwiftMessage();
        $this->saveOutput($message, "StructuredEmail", ".txt");

    }

    /**
     * Send a forgot password email
     */
    public function testForgotEmail() {
        $template = 'SilverStripe/Control/Email/ForgotPasswordEmail';
        $token = "really-bad-token";
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        /** @var StructuredEmail $email */
        $email = StructuredEmail::create()
            ->setHTMLTemplate($template)
            ->setData($member)
            ->setSubject(_t(
                'SilverStripe\\Security\\Member.SUBJECTPASSWORDRESET',
                "Your password reset link",
                'Email subject'
            ))
            ->addData('PasswordResetLink', Security::getPasswordResetLink($member, $token))
            ->setTo($member->Email);
        // test preheader
        $email->setPreHeader(
            'Your password reset link'
        );
        $result = $email->send();

        $html = $email->getBody();

        $this->saveOutput($html, "ForgotPasswordEmail");

        $message = $email->getSwiftMessage();
        $this->saveOutput($message, "ForgotPasswordEmail", ".txt");

        // test that email is rendered with link and in structured email
    }

    public function testChangePassword() {
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        $email = StructuredEmail::create()
            ->setHTMLTemplate('SilverStripe\\Control\\Email\\ChangePasswordEmail')
            ->setData($member)
            ->setTo($member->Email)
            ->setFrom('from@example.com')
            ->setSubject(_t(
                'SilverStripe\\Security\\Member.SUBJECTPASSWORDCHANGED',
                "Your password has been changed",
                'Email subject'
            ));
        $email->send();

        $this->saveOutput($email->getBody(), "ChangePasswordEmail");

        $message = $email->getSwiftMessage();
        $this->saveOutput($message, "ChangePasswordEmail", ".txt");
    }

    public function testStandardEmail() {
        $member = $this->objFromFixture(Member::class, 'forgotpassword');
        $email = StructuredEmail::create()
            ->setHTMLTemplate('SilverStripe\\Control\\Email\\Email')
            ->setData([
                'EmailContent' => file_get_contents(dirname(__FILE__) . '/data/template.html')
            ])
            ->setPreHeader('An important message')
            ->setTo($member->Email)
            ->setFrom('from@example.com')
            ->setSubject('Subject of an important message');
        $email->send();

        $this->saveOutput($email->getBody(), "StandardEmail");

        $message = $email->getSwiftMessage();
        $this->saveOutput($message, "StandardEmail", ".txt");
    }

}
