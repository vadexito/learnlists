<?php

namespace VxoBraintree\Form;

use Zend\Form\Form;


class TransactionForm extends Form
{
    public function __construct()
    {
        parent::__construct('TransactionForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('id','braintree-payment-form');
        
        $this->add([
            'name' => 'number',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'number',
                'autocomplete'    => 'off',
                'required' => 'required',
                'data-encrypted-name' => 'number'
            ],
            'options' => [
                'label' => _('Card Number')
            ],
        ]);
        
        $this->add([
            'name' => 'cvv',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'cvv',
                'required' => 'required',
                'autocomplete'    => 'off',
                'data-encrypted-name' => 'cvv'
            ],
            'options' => [
                'label' => _('CVV')
            ],
        ]);
        
        $this->add([
            'name' => 'month',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'month',
                'required' => 'required',
                'autocomplete'    => 'off',
                'data-encrypted-name' => 'month'
            ],
            'options' => [
                'label' => _('Expiration Month')
            ],
        ]);
        
        $this->add([
            'name' => 'year',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'year',
                'required' => 'required',
                'autocomplete'    => 'off',
                'data-encrypted-name' => 'year'
            ],
            'options' => [
                'label' => _('Expiration Year')
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Check',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}