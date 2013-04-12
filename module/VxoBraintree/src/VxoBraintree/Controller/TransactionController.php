<?php

namespace VxoBraintree\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Braintree_Transaction;
use Braintree_Configuration;

class TransactionController extends AbstractActionController
{
    
    public function createAction()
    {
        Braintree_Configuration::environment('sandbox');
        Braintree_Configuration::merchantId('rh4gx5xdtspkkn82');
        Braintree_Configuration::publicKey('5522v4dhzxmsmdtf');
        Braintree_Configuration::privateKey('1b78e512186aaa9d1be056946e58b9b2');
        
        $form = new \VxoBraintree\Form\TransactionForm();
        $form->get('submit')->setValue(_('Create transaction'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setData($request->getPost()); 
            if ($form->isValid()) {  
                
                $info = $form->getData();
                $result = \Braintree_Transaction::sale([
                    'amount' => '1000.00',
                    'creditCard' => [
                        'number' => $info['number'],
                        'cvv' => $info['cvv'],
                        'expirationMonth' => $info['month'],
                        'expirationYear' => $info['year'],
                    ],
                    'options' => [
                        'submitForSettlement' => true
                    ],
                ]);
                
                if ($result->success) {
                    $response = "<h1>Success! Transaction ID: " 
                        . $result->transaction->id . "</h1>";
                } else {
                  $response = "<h1>Error: " . $result->message . "</h1>";
                }
                
                return $this->redirect()->toRoute(
                    'offers/success_payment'
                );
            }
        }
        return ['form' => $form]; 
    }
}

