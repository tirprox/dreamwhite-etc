<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 09.10.2017
 * Time: 4:49
 */

class OrderExporter {
   function prepareCustomerJSON($order){
      $firstName = $order->get_shipping_first_name();
      $lastName = $order->get_shipping_last_name();
      $combo = $firstName . $lastName;
      return $combo;
   }
   
   function getAddress($user_id){
      $address = '';
      $address .= get_user_meta( $user_id, 'shipping_first_name', true );
      $address .= ' ';
      $address .= get_user_meta( $user_id, 'shipping_last_name', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_company', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_address_1', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_address_2', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_city', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_state', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_postcode', true );
      $address .= "\n";
      $address .= get_user_meta( $user_id, 'shipping_country', true );
      return $address;
   }
   
}