<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class UPAPI
    {
      var $objCurl;
      var $result = array();
      var $xml='';

      function getResult()
      {
        return $this->result;
      }
        
      function checkError($result)
      {
        if(isset($result['ERR']))
        {
         // >>> echo '<h2> Error Code: ' . $result['STATUS'] . '</h2>';
         // >>> echo '<h3>' . $result['ERR'] . '</h3>';
         // >>> exit;
         $flag_continue = FALSE;

        }
      }
      
        function doAction($toDo, $parameters = array())
        {
        if (is_string($toDo))
        {
          if (is_array($parameters))
          {
            $parameters['apiKey'] = SUWP_API_ACCESS_KEY_IMEISERVICELIST_UI;
            $parameters['userId'] = SUWP_USERNAME_IMEISERVICELIST_UI;
            $parameters['action'] = $toDo;
            $this->objCurl = curl_init( );
            curl_setopt( $this->objCurl, CURLOPT_HEADER, false );
            curl_setopt( $this->objCurl, CURLOPT_CONNECTTIMEOUT, 10 );
            curl_setopt( $this->objCurl, CURLOPT_TIMEOUT, 60 );
            curl_setopt( $this->objCurl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            // curl_setopt( $this->objCurl, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $this->objCurl, CURLOPT_RETURNTRANSFER, true );
            if( is_array( $parameters ) ):
              $vars = implode( '&', $parameters);
            endif;
            curl_setopt( $this->objCurl, CURLOPT_URL, SUWP_DHRUFUSION_URL_IMEISERVICELIST_UI );
            // curl_setopt( $this->objCurl, CURLOPT_URL, SUWP_DHRUFUSION_URL_IMEISERVICELIST_UI.'/gsmfusion_api/index.php');
            curl_setopt( $this->objCurl, CURLOPT_POST, true );
            curl_setopt( $this->objCurl, CURLOPT_POSTFIELDS, $parameters);
            $this->result = curl_exec( $this->objCurl );
            curl_close($this->objCurl);
          }
        }
        }

      function XmlToArray($xml)
      {
        $this->xml = $xml;	
      }

      function _struct_to_array($values, &$i)
      {
        $child = array(); 
        if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']); 
        
        while ($i++ < count($values)) {

          if ( isset($values[$i]['type']) ) {

            switch ($values[$i]['type']) { 
              case 'cdata': 
                    array_push($child, $values[$i]['value']); 
              break; 
              
              case 'complete': 
                $name = $values[$i]['tag']; 
                if( !empty($name)  && array_key_exists('value', $values[$i]) ) {
                $child[$name]= ($values[$i]['value'])?($values[$i]['value']):''; 
                if(isset($values[$i]['attributes'])) {					
                  $child[$name] = $values[$i]['attributes']; 
                } 
              }	
                  break; 
              
              case 'open': 
                $name = $values[$i]['tag']; 
                $size = isset($child[$name]) ? sizeof($child[$name]) : 0;
                $child[$name][$size] = $this->_struct_to_array($values, $i); 
              break;
              
              case 'close': 
                    return $child; 
              break; 
            }

          } // if ( isset($values[$i]['type']) )

        } // while ($i++ < count($values))
        
        return $child; 
      }//_struct_to_array
      function createArray()
      { 
        $xml    = $this->xml;
        $values = array(); 
        $index  = array(); 
        $array  = array(); 
        $parser = xml_parser_create(); 
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $xml, $values, $index);
        xml_parser_free($parser);
        $i = 0; 
        $name = $values[$i]['tag']; 
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : ''; 
        $array[$name] = $this->_struct_to_array($values, $i); 
        return $array; 
      }//createArray
    }

  $objUPAPI = new UPAPI();
  $objUPAPI->doAction('getpackages', array());
  $objUPAPI->XmlToArray($objUPAPI->getResult());
  $arrayData = $objUPAPI->createArray();
  if(isset($arrayData['error']) && sizeof($arrayData['error']) > 0)
  {
    // >>> echo '<b>'.$arrayData['error'][0].'</b>';
    // >>> exit;
    $flag_continue = FALSE;
  }
  
  // error_log( 'UNLOCKINGPORTAL ALL IMEI SERVICES: '. print_r($arrayData,true) );

  $request = array();
  if(isset($arrayData['Packages']['Package']) && sizeof($arrayData['Packages']['Package']) > 0) {

    $request = $arrayData['Packages']['Package'];
    $flag_continue = TRUE;
  }

  if ( $flag_continue ) {

    $total = count($request);

    for($count = 0; $count < $total; $count++)
    {
      
      $package_id = '';
      $category = '';
      $category_id = '';
      $package_title = '';
      $package_price = '';
      $time_taken = '';
      $must_read = '';
      
      if( !empty($request[$count]['PackageId'])  && array_key_exists('PackageId', $request[$count]) ) {
        $package_id = $request[$count]['PackageId'];
      }
      if( !empty($request[$count]['Category'])  && array_key_exists('Category', $request[$count]) ) {
        $category = $request[$count]['Category'];
      }
      if( !empty($request[$count]['CategoryId'])  && array_key_exists('CategoryId', $request[$count]) ) {
        $category_id = $request[$count]['CategoryId'];
      }
      if( !empty($request[$count]['PackageTitle'])  && array_key_exists('PackageTitle', $request[$count]) ) {
        $package_title = $request[$count]['PackageTitle'];
      }
      if( !empty($request[$count]['PackagePrice'])  && array_key_exists('PackagePrice', $request[$count]) ) {
        $package_price = $request[$count]['PackagePrice'];
      }
      if( !empty($request[$count]['TimeTaken'])  && array_key_exists('TimeTaken', $request[$count]) ) {
        $time_taken = $request[$count]['TimeTaken'];
      }
      if( !empty($request[$count]['MustRead'])  && array_key_exists('MustRead', $request[$count]) ) {
        $must_read = htmlspecialchars( strip_tags( str_replace('</p>', chr(10) , $request[$count]['MustRead']) ) );
      }
      
      $services[] = array(
        'GROUPID' => $category_id,
        'ID' => $package_id,
        'ID_DISPLAY' => $package_id,
        'NETWORKPROVIDER' => '',
        'GROUPNAME' => $category,
        'SERVICENAME' => $package_title,
        'INFO' => $must_read,
        'Requires.Network' => 'None',
        'Requires.Mobile' => 'None',
        'Requires.Provider' => 'None',
        'Requires.PIN' => 'None',
        'Requires.KBH' => 'None',
        'Requires.MEP' => 'None',
        'Requires.PRD' => 'None',
        'Requires.Type' => 'None',
        'Requires.Locks' => 'None',
        'Requires.Reference' => 'None',
        'CREDIT' => $package_price,
        'TIME' => $time_taken,
      );

    }
    
  } else {

    // could not connect to API server, probably due to failed internet connection
    $request = array(
          'ERROR' => array(
                        array('MESSAGE' => 'Invalid Access Key'),
                        array('MESSAGE' => 'Invalid Username'),
                          ),
          'apiversion' => 'unlockingportal.1.0',
    );
    
  }
  