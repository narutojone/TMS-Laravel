<?php namespace App\Core\Validators;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{

    /**
     * An array of reserved subdomain names
     *
     * @var array
     */
    protected $reserved = ['admin', 'www', 'dev'];

    /**
     * Validate a string to be a price format
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateCurrency($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["currency" => 'The :attribute format is not valid. It must have the following format e.g.: 23.25']);
        return (preg_match('/^-?[0-9]+(?:\.[0-9]{1,2})?$/', $value) && $value >= 0);
        //return (preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $value) && $value >= 0);
    }

    /**
     * Validate a string to be a zip code
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateZip($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["zip" => 'The :attribute format is not a valid zip code.']);
        return preg_match('/^[0-9]{5}(\-[0-9]{4})?$/', $value);
    }

    /**
     * Validate a string to be a phone number
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validatePhone($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["phone" => 'The :attribute format is not a valid phone number.']);
        return preg_match("/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i", $value);
    }

    /**
     * Validate a number to be a tinyint
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateTinyInt($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["tinyInt" => 'The :attribute must by tinyint.']);
        return (($value >= -128) && ($value <= 127));
    }

    /**
     * Validate a number to be a smallint
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateSmallInt($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["smallInt" => 'The :attribute must by smallint.']);
        return (($value >= -32768) && ($value <= 32767));
    }

    /**
     * Validate a number to be a mediumint
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateMediumInt($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["mediumInt" => 'The :attribute must by mediumint.']);
        return (($value >= -8388608) && ($value <= 8388607));
    }

    /**
     * Validate a number to be a mediumint
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateInt($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["int" => 'The :attribute must by int.']);
        return (($value >= -2147483648) && ($value <= 2147483647));
    }

    /**
     * Validate a number to be a bigint
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateBigInt($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["bigInt" => 'The :attribute must by bigint.']);
        return (($value >= -9223372036854775808) && ($value <= 9223372036854775807));
    }

    /**
     * Validate the value to be a valid credit card number
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return int
     */
    public function validateCreditCardNumber($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["creditCardNumber" => 'The :attribute is not a valid credit card number.']);
        return preg_match('/^[0-9]{16}$/', $value);
    }

    /**
     * Validate the value to be a valid credit card expiry year
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return int
     */
    public function validateCreditCardExpiryYear($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["creditCardExpiryYear" => 'The :attribute is not a valid expiry year for credit card.']);
        return preg_match('/^[0-9]{4}$/', $value);
    }

    /**
     * Validate the value to be a valid credit card expiry month
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return int
     */
    public function validateCreditCardExpiryMonth($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["creditCardExpiryMonth" => 'The :attribute is not a valid expiry month for credit card.']);
        return preg_match('/^[0-9]{1,2}$/', $value);
    }

    /**
     * Validate the value to be a valid CVV credit card
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return int
     */
    public function validateCreditCardCVV($attribute, $value, $parameters)
    {
        $this->setCustomMessages(["creditCardCVV" => 'The :attribute is not a valid CVV for credit card.']);
        return preg_match('/^[0-9]{3}$/', $value);
    }

}
