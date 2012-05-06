<?php

/**
 * GeoController class
 *
 * @uses          AppController
 * @package       mongodb
 * @subpackage    mongodb.samples.controllers
 */
require('CakeResponse.php');
require('CakeRequest.php');
require('Xml.php');

class UsuariosController extends AppController {

    /**
     * name property
     *
     * @var string 'Mensaje'
     * @access public
     */
    public $name = 'Usuario';

    /**
     * index method
     *
     * @return void
     * @access public
     */
    public function CambioStringXml($xml) {

        $fp = fopen("/var/www/Desarroll/controllers/archivo.xml", "w+"); // Crear XML!!!! XDDD
        if ($fp == false) {
            die("No se ha podido crear el archivo.");
        }
        $xml = utf8_encode($xml);
        fwrite($fp, $xml);
        fclose($fp);
        $fp = fopen("/var/www/Desarroll/controllers/archivo.xml", "r+"); // Crear XML!!!! XDDD
        if ($fp == false) {
            die("No se ha podido crear el archivo.");
        }
        $xml = utf8_encode($xml);
        fwrite($fp, $xml);
        fclose($fp);

        $xml_content = new SimpleXMLElement("/var/www/Desarroll/controllers/archivo.xml", LIBXML_NOCDATA, true);
//echo $xml_content->getName(). "Hola!!1!";



        $xml_array = array();
        $namespaces = array_merge(array('' => ''), $xml_content->getNamespaces(true));
        Xml::_toArray($xml_content, $xml_array, '', array_keys($namespaces));

        return $xml_array;
    }

    public function Registrarse() {
        $request = new CakeRequest();

        $xml = $request->_readInput();
        $xml_array = $this->CambioStringXml($xml);
        $params = array(
            'limit' => 35,
            'page' => 1,
        );
        $params['conditions'] = array('nick' => $xml_array['Usuario']['nick']);
        $respon = new CakeResponse();
        $xml = "";
        $respon->type("xml");
        $params['order'] = array('_id' => -1);
        $results = $this->Usuario->find('count', $params);

        if ($results > 0) {
            $xml = "<error>existe</error>";
            $respon->type("xml");
            $respon->body($xml); //Agrego el cuerpo
            $respon->send();
        } else {


            //  $xml_array=$this->CambioStringXml($xml);

            $xml = $this->add($xml_array);


            $respon->body($xml); //Agrego el cuerpo
            $respon->send();
        }
//$this->set(compact('results'));
    }

    public function perfil($nickname, $type = null, $lat = null, $long = null, $opt1 = null, $opt2 = null) {

        $params = array(
            'limit' => 35,
            'page' => 1,
        );
        $params['conditions'] = array('nick' => $nickname);

        $results = $this->Usuario->find('all', $params);
        //$this->set(compact('results'));



        $xml = '<Usuario>';
        foreach ($results as $result):

            $xml = $xml . '<nombre>' . $result['Usuario']['nombre'] . '</nombre>';
            $xml = $xml . '<apellido>' . $result['Usuario']['apellido'] . '</apellido>';
            $xml = $xml . '<correo>' . $result['Usuario']['correo'] . '</correo>';
            $fecha = date('Y-M-d h:i:s', $result['Usuario']['fecha_nac']->sec);
            $xml = $xml . '<fecha_nac>' . $fecha . '</fecha_nac>';
            $xml = $xml . '<pais_origen>' . $result['Usuario']['pais_origen'] . '</pais_origen>';
            $xml = $xml . '<biografia>' . $result['Usuario']['biografia'] . '</biografia>';
            $xml = $xml . '<foto>' . $result['Usuario']['foto'] . '</foto>';
//            $xml = $xml . "<comentario>" . $result['Mensaje']['comentario'] . "</comentario>";
//               echo $result['Mensaje']['comentario'];    
        // $xml=$xml."<comentario>".$result['Mensaje']['megustalista']['idusuario']."</comentario>";
        //      $xml=$xml."<comentario>".$result['Mensaje']['nomegustalista']."</comentario>";
//            $xml = $xml . "<respuesta>" . $result['Mensaje']['respuestas'] . "</respuesta>";
//            $fecha = date('Y-M-d h:i:s', $result['Mensaje']['fecha_creacion']->sec);
//            $xml = $xml . "<fechacreacion>" . $fecha . "</fechacreacion>";
//            $xml = $xml . "<fechacreacion>" . $result['Mensaje']['fecha_creacion'] . "</fechacreacion>";
//            $xml = $xml . "<notificacion>" . $result['Mensaje']['notificacion'] . "</notificacion>";
//            $xml.="<submensajes>";
//            foreach ($result['Mensaje']['listaMensaje'] as $lista):
////                $xml = $xml . "<submensaje>" . $lista['_idmensaje'] . "</submensaje>";
//            endforeach;
//            $xml.="</submensajes>";


        endforeach;
        $xml = $xml . '</Usuario>';

        $respon = new CakeResponse();
// $respon->type("xml");
        $respon->body($xml); //Agrego el cuerpo
        $respon->send();
    }

    /**
     * add method
     *
     * @return void
     * @access public
     */
    public function add($data) {

//                 foreach ($data as $dat):
//                 foreach ($dat as $da):
//                     echo " ".$da." ";
//                 endforeach;
//                     
//                 endforeach;

        $data['Usuario']['fecha_nac'] = new MongoDate(strtotime($data['Usuario']['fecha_nac']));
        $this->data = $data;
        if ($this->Usuario->save($data)) {
            return "<registrado>Exito</registrado>";
        }
        return "<Error Desconocido>Registro Abortado</Error desconocido>";
    }

    /**
     * delete method
     *
     * @param mixed $id null
     * @return void
     * @access public
     */
    public function delete($id = null) {
        $params = array(
            'limit' => 35,
            'page' => 1,
        );

        if (!empty($type) && !empty($lat) && !empty($long)) {
            $lat = floatval($lat);
            $long = floatval($long);
            $opt1 = floatval($opt1);
            $opt2 = floatval($opt2);

            switch ($type) {
                case('near'):
                    if (!empty($opt1)) {
                        $cond = array('loc' => array('$near' => array($lat, $long), '$maxDistance' => $opt1));
                    } else {
                        $cond = array('loc' => array('$near' => array($lat, $long)));
                    }
                    break;
                case('box'):
                    $lowerLeft = array($lat, $long);
                    $upperRight = array($opt1, $opt2);
                    $cond = array('loc' => array('$within' => array('$box' => array($lowerLeft, $upperRight))));
                    break;
                case('circle'):
                    $center = array($lat, $long);
                    $radius = $opt1;
                    $cond = array('loc' => array('$within' => array('$center' => array($center, $radius))));
                    break;
            }
            $params['conditions'] = $cond;
        } else {
            $params['order'] = array('_id' => -1);
        }

        $results = $this->Usuario->find('all', $params);
        foreach ($results as $result):
            $id = $result['Mensaje']['_id'];
        endforeach;
        if (!$id) {
            $this->flash(__('Invalid Geo', true), array('action' => 'index'));
        }
        if ($this->Usuario->delete($id)) {
            $this->flash(__('Geo deleted', true), array('action' => 'index'));
        } else {
            $this->flash(__('Geo deleted Fail', true), array('action' => 'index'));
        }
    }

}
