<?php namespace DiveraMkt\MailerLite\Components;

use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use DiveraMkt\MailerLite\Models\Settings;
use MailerLiteApi\MailerLite;

class Signup extends ComponentBase
{
    protected $groupsApi;
    protected $subscribersApi;

    public function componentDetails()
    {
        return [
            'name'        => 'MailerLite Signup Form',
            'description' => 'Sign up a new person to a mailing list.'
        ];
    }

    public function defineProperties()
    {
        return [
            'group_id' => [
                'title'         => 'Group ID',
                'description'   => 'In MailerLite account, click in the Profile - Integrations - Developer API and look for the Group ID.',
                'type'          => 'string',
                'required'      => true,
                'validationMessage' => 'The ID Group must be informed.'
            ],
        ];
    }

    public function onSignup()
    {
        $settings = Settings::instance();
        if (!$settings->api_key) {
            throw new ApplicationException('MailerLite API key is not configured.');
        }

        /*
         * Validate input
         */
        $data = post();

        $rules = [
            'email' => 'required|email|min:2|max:64',
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        /*
         * Sign up to MailerLite via the API
         */

        $MailerLite = new MailerLite($settings->api_key);
        $this->groupsApi = $MailerLite->groups();

        $subscriberData = [
            'email' => post('email'),
            'name'  => @post('name'),
            'type'  => 'active'
        ];

        $this->groupsApi->addSubscriber($this->property('group_id'), $subscriberData);

        /*if (!$MailerLite->success()) {
            $this->page['error'] = $MailerLite->getLastError();
        }*/
    }
}
