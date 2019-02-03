<?php namespace Diveramkt\MailerLite\Models;

use Model;

class Settings extends Model
{
	use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'mailerlite_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public $rules = [
        'api_key' => 'required'
    ];
}
