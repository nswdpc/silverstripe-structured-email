<?php

namespace NSWDPC\StructuredEmail;

use SilverStripe\Dev\SapphireTest;

class EmailTemplateTest extends SapphireTest {

    protected $usesDatabase = false;

    public function testTemplate() {

        $data = [
            'Body' => file_get_contents(dirname(__FILE__) . '/data/template.html'),
            'EmailPreHeader' => 'Please follow the instructions in this email',
            'EmailMasthead' => 'Welcome to email test',
            'EmailMastheadLink' => 'https://example.com?foo=bar',
            'EmailPhysical' => ''
        ];

        $template = "Email";
        $template_location = "NSWDPC/StructuredEmail";

        $email = StructuredEmail::create();
        $email->setTo("to@example.com");
        $email->setCc("cc@example.com");
        $email->setBcc("bcc@example.com");
        $email->setFrom("from@example.com");
        $email->setData($data);

        $email->setHTMLTemplate($template_location . "/" . $template);

        // add customised data into the email
        $email->render();

        $body = $email->getBody();

    }

}
