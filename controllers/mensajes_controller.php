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

class MensajesController extends AppController {

    /**
     * name property
     *
     * @var string 'Mensaje'
     * @access public
     */
    public $name = 'Mensaje';

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

    public function busquedaportags($type = null, $lat = null, $long = null, $opt1 = null, $opt2 = null) {
        $params = array(
            'limit' => 35,
            'page' => 1,
        );
        $request = new CakeRequest();
        $xml = $request->_readInput();
        $xml_array = $this->CambioStringXml($xml);
        $params = array(
            'limit' => 35,
            'page' => 1,
        );

        $campos = $xml_array['Mensaje']['tags'];

        foreach ($campos as $campo):
            $params['conditions'] = array('tags' => $campo);
        endforeach;

//        $params['conditions'] = array('nick' => $xml_array['Mensaje']['nick']);
        $respon = new CakeResponse();
        $xml = "";
        $respon->type("xml");
        $params['order'] = array('_id' => -1);
        $results = $this->Mensaje->find('all', $params);
        $xml = $xml . "<Mensaje>";
        foreach ($results as $result):
            $xml = $xml . "<nombre>" . $result['Mensaje']['nombre'] . "</nombre>";
            $fecha = date('Y-M-d h:i:s', $result['Mensaje']['fecha_creacion']->sec);
            $xml = $xml . "<fechacreacion>" . $fecha . "</fechacreacion>";
            foreach ($result['Mensaje']['tags'] as $aux):
                $xml = $xml . "<tag>" . $aux . "</tag>";
            endforeach;

        endforeach;
        $xml = $xml . "</Mensaje>";
        $respon = new CakeResponse();
        $respon->body($xml); //Agrego el cuerpo
        $respon->send();
    }

    public function show_all($type = null, $lat = null, $long = null, $opt1 = null, $opt2 = null) {

        $params = array(
            'limit' => 35,
            'page' => 1,
        );
//$mongoDbObject = $this->Model->getMongoDb();
        //'conditions' => array('title' => 'hehe'),

        /* Parametros de busqueda
         * Sort mas nuevas primero: db.mensajes.find().sort( {"fecha_creacion" : -1});
         * campos deberia ser un array obetnido con XML
         * 
         * Para sacar una fecha:
         * $fecha = date('Y-M-d h:i:s', $result['Mensaje']['fecha_creacion']->sec); 
         * $xml = $xml . "<fechacreacion>" . $fecha . "</fechacreacion>";
         * 
         * Para usuario:
         * $campo= 'Legna';
         * $params['conditions'] = array('idusuario' => $campo);
         * 
         * Horas:
         * $dateDiff = $date1 - $date2;
         * $fullDays = floor($dateDiff/(60*60*24));
         * $fullHours = floor(($dateDiff-($fullDays*60*60*24))/(60*60));
         * $fullMinutes = floor(($dateDiff-($fullDays*60*60*24)-($fullHours*60*60))/60);
         * echo "Differernce is $fullDays days, $fullHours hours and $fullMinutes minutes.";
         * 
         */



//                array('tag' => array('idtag' => 'ron') );
//                array('tags','idtag' => 'ron');




        $results = $this->Mensaje->find('all', $params);
        //$this->set(compact('results'));



        $xml = '<mensajes><mensaje>';
        foreach ($results as $result):
//            $xml = $xml . "<comentario>" . $result['Mensaje']['comentario'] . "</comentario>";
//               echo $result['Mensaje']['comentario'];    
            // $xml=$xml."<comentario>".$result['Mensaje']['megustalista']['idusuario']."</comentario>";
            //      $xml=$xml."<comentario>".$result['Mensaje']['nomegustalista']."</comentario>";
//            $xml = $xml . "<respuesta>" . $result['Mensaje']['respuestas'] . "</respuesta>";

            $fecha = date('Y-M-d h:i:s', $result['Mensaje']['fecha_creacion']->sec);
            $xml = $xml . "<fechacreacion>" . $fecha . "</fechacreacion>";



//            $xml = $xml . "<fechacreacion>" . $result['Mensaje']['fecha_creacion'] . "</fechacreacion>";
//            $xml = $xml . "<notificacion>" . $result['Mensaje']['notificacion'] . "</notificacion>";
            $xml.="<submensajes>";
            foreach ($result['Mensaje']['listaMensaje'] as $lista):
//                $xml = $xml . "<submensaje>" . $lista['_idmensaje'] . "</submensaje>";
            endforeach;
            $xml.="</submensajes>";


        endforeach;
        $xml.="</mensaje></mensajes>";
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
    public function add() {

        $this->data['Mensaje']['comentario'] = "HolaSIII!!! ";
        $this->data['Mensaje']['notificacion'] = "HolaSIII!!! ";
        $this->data['Mensaje']['fecha_creacion'] = "HolaSIII!!! ";
        echo "hola";

        if ($this->Mensaje->save($this->data)) {
            $this->flash(__('Geo saved.', true), array('action' => 'index'));
        }
    }

    public function enviarComentario() {
        $request = new CakeRequest();

        $xml = $request->_readInput();
        $xml_array = $this->CambioStringXml($xml);
        $params = array(
            'limit' => 35,
            'page' => 1,
        );
//        $params['conditions'] = array('nick' => $xml_array['Mensaje']['nick']);
        $respon = new CakeResponse();
        $xml = "";
        $respon->type("xml");
        $params['order'] = array('_id' => -1);
//        $results = $this->Mensaje->find('count', $params);

       
            //  $xml_array=$this->CambioStringXml($xml);

            $xml = $this->add($xml_array);


            $respon->body($xml); //Agrego el cuerpo
            $respon->send();
        
//$this->set(compact('results'));
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

        $results = $this->Mensaje->find('all', $params);
        foreach ($results as $result):
            $id = $result['Mensaje']['_id'];
        endforeach;
        if (!$id) {
            $this->flash(__('Invalid Geo', true), array('action' => 'index'));
        }
        if ($this->Mensaje->delete($id)) {
            $this->flash(__('Geo deleted', true), array('action' => 'index'));
        } else {
            $this->flash(__('Geo deleted Fail', true), array('action' => 'index'));
        }
    }

}
