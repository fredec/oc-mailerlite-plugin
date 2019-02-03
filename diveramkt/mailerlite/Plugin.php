<?php namespace Diveramkt\Mailerlite;

use System\Classes\PluginBase;
use Illuminate\Support\Facades\Event;
use MailerLiteApi\MailerLite;
use DiveraMkt\MailerLite\Models\Settings;

use Validator;
use ValidationException;
use ApplicationException;
use Log;

class Plugin extends PluginBase
{
    public $require = [
        'Martin.Forms'
    ];
    
    public function pluginDetails()
    {
        return [
            'name'        => 'MailerLite',
            'description' => 'Provides MailerLite integration services.',
            'author'      => 'Frederico Marinho',
            'icon'        => 'icon-envelope',
            'homepage'    => 'https://github.com/rainlab/octplugin-mailerlite'
        ];
    }

    public function registerComponents()
    {
        return [
            'DiveraMkt\MailerLite\Components\Signup' => 'mailSignup',
        ];
    }

    public function registerPermissions()
    {
        return [
            'diveramkt.mailerlite.configure' => [
                'tab'   => 'MailerLite',
                'label' => 'Configure MailerLite API access.',
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'MailerLite Integration',
                'description' => 'Create an integration with the MailerLite email tool',
                'category'    => 'DiveraMkt',
                'icon'        => 'icon-envelope',
                'class'       => 'DiveraMkt\Mailerlite\Models\Settings',
                'order'       => 500,
                'keywords'    => 'divera sites mailerlite email integration',
                'permissions' => ['Mailerlite.manage_mailerlite']
            ]
        ];
    }

    public function boot() 
    {
        Event::listen('martin.forms.beforeSaveRecord', function (&$formdata) {

            if (@$formdata['group']) {
                $settings = Settings::instance();
                if (!$settings->api_key) {
                    throw new ApplicationException('MailerLite API key is not configured.');
                }

                /*
                 * Validate input
                 */
                $data = $formdata;

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

                $groups = $this->groupsApi->get();
                foreach ($groups as $group) {
                    if ($group->name == $formdata['group']) {
                        $newGroup = $group;
                        break;
                    }
                }

                /* Create the group, in case there is not yet */
                if (!@$newGroup)
                    $newGroup = $this->groupsApi->create(['name' => $formdata['group']]);

                $subscriberData = [
                    'email' => $formdata['email'],
                    'name'  => @$formdata['name'],
                    'type'  => 'active'
                ];

                $this->groupsApi->addSubscriber($newGroup->id, $subscriberData);
            }

        });
    }

}
