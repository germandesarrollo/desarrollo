<?php
/**
 * PostsController class
 *
 * @uses          AppController
 * @package       mongodb
 * @subpackage    mongodb.samples.controllers
 */
App::import('Router', 'Routing');
App::import('CakeRequest', 'Network');
App::import('CakeResponse', 'Network');
App::import('Controller', 'Controller');
App::import('Scaffold', 'Controller');
App::import('View', 'View');
App::import('Debugger', 'Utility');
require('CakeResponse.php');
require('CakeRequest.php');
App::import('Utility','Xml');
class PostsController extends AppController {

	public $Post;

/**
 * name property
 *
 * @var string 'Posts'
 * @access public
 */
	public $name = 'Posts';

/**
 * index method
 *
 * @return void
 * @access public
 */
     //   var $components = array('RequestHandler');
var $components = array( 'RequestHandler' ); 

        function hola () {
            $request=new CakeRequest();
          $xml= $request->_readInput();
         //$xmll=  Xml::toArray(Xml::build($xml));
          
        // echo $xmll['persona']['nombre'];
         
   // $holaaaa = "hoooolllllaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
 // $this->render('/posts/prueba.xml');
            // $this->RequestHandler->requestedWith();
 
         // $xmlSalida=  $this->RequestHandler->convertXml($xml);
 
 
 
// $params = array(
//			'fields' => array('sesion', 'body', 'hoge'),
			//'fields' => array('Post.title', ),
			//'conditions' => array('title' => 'hehe'),
			//'conditions' => array('hoge' => array('$gt' => '10', '$lt' => '34')),
			//'order' => array('title' => 1, 'body' => 1),
//			'order' => array('_id' => -1),
//			'limit' => 35,
//			'page' => 1,
//		);
		//$results = $this->Post->find('all', $params);
		//$result = $this->Post->find('count', $params);
    //  $this->Form->create('Post' , array( 'type' => 'post' ));
      
 $bodyp = "<familia>";
         //        foreach ($results as $result):
           //      foreach ($result['Post'] as $resultd):
                     
                
             //        $bodyp = $bodyp."<padre><nombre>".$resultd['sesion']."</nombre></padre>";
               //   endforeach;
               //  endforeach;
      $fp = fopen("/home/german/Escritorio/miarchivo.xml","w+"); // Crear XML!!!! XDDD
if($fp == false){
  die("No se ha podido crear el archivo.");
}
fwrite($fp, $xml . PHP_EOL);
fclose($fp);  

//App::import('Xml');

    // your XML file's location
    //$file = "/home/german/Escritorio/miarchivo.xml";

    // now parse it
   // $parsed_xml = new XML($file);
  //  $parsed_xml = Set::reverse($parsed_xml); // this is what i call magic

    // see the returned array
    //debug($parsed_xml); 
    $params = array(
			'fields' => array('comentario'),
			//'fields' => array('Post.title', ),
			//'conditions' => array('title' => 'hehe'),
			//'conditions' => array('hoge' => array('$gt' => '10', '$lt' => '34')),
			//'order' => array('title' => 1, 'body' => 1),
			'order' => array('_id' => -1),
			'limit' => 35,
			'page' => 1,
		);
           
		$results = $this->Post->find('all', $params);
                 
                       $xml=$xml.$result; 
              
   
 
	
$bodyp=$bodyp."<padre><nombre>hola</nombre></padre></familia>";
 $respon=new CakeResponse();
 $respon->type("xml");
$respon->body($xml); //Agrego el cuerpo
 $respon->send();
        
  
  //echo $this->request->url;
 // if ($this->request->is('xml')) {
// Execute XML-only code
 //   echo "XML!!!!!";
//}
  //$data = $this->request->input('Xml::build', array('return' => 'domdocument'));
}
	public function index() {
		$params = array(
			'fields' => array('title', 'body', 'hoge'),
			//'fields' => array('Post.title', ),
			//'conditions' => array('title' => 'hehe'),
			//'conditions' => array('hoge' => array('$gt' => '10', '$lt' => '34')),
			//'order' => array('title' => 1, 'body' => 1),
			'order' => array('_id' => -1),
			'limit' => 35,
			'page' => 1,
		);
                
		$results = $this->Post->find('all', $params);
		//$result = $this->Post->find('count', $params);
	$this->set(compact('results'));
	}

/**
 * add method
 *
 * @return void
 * @access public
 */
	public function add() {
	//echo $this->data."HOLAAAAAA";
         foreach ($this->data as $salida):
                         //    echo $salida;
                        endforeach;    
        if (!empty($this->data)) {

			$this->Post->create();
			if ($this->Post->save($this->data)) {
			echo $this->data."HOLAAAAAA4";
                        foreach ($this->data as $salida):
                               foreach ($salida as $salid):
                                echo $salid;
                               endforeach;
                        
                        endforeach;
                           
                        
                            $this->flash(__('Post saved.', true), array('action' => 'index'));
                           // echo $this->data."HOLAAAAAA3";
                                foreach ($this->data as $salida):
                               foreach ($salida as $salid):
                            //    echo $salid;
                               endforeach;
                        
                        endforeach;
			} else {
			}
		}
	}

/**
 * edit method
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	public function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash(__('Invalid Post', true), array('action' => 'index'));
		}
		if (!empty($this->data)) {

			if ($this->Post->save($this->data)) {
				$this->flash(__('The Post has been saved.', true), array('action' => 'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Post->read(null, $id);
			//$this->data = $this->Post->find('first', array('conditions' => array('_id' => $id)));
		}
	}

/**
 * delete method
 *
 * @param mixed $id null
 * @return void
 * @access public
 */
	public function delete($id = null) {
		if (!$id) {
			$this->flash(__('Invalid Post', true), array('action' => 'index'));
		}
		if ($this->Post->delete($id)) {
			$this->flash(__('Post deleted', true), array('action' => 'index'));
		} else {
			$this->flash(__('Post deleted Fail', true), array('action' => 'index'));
		}
	}

/**
 * deleteall method
 *
 * @return void
 * @access public
 */
	public function deleteall() {
		$conditions = array('title' => 'aa');
		if ($this->Post->deleteAll($conditions)) {
			$this->flash(__('Post deleteAll success', true), array('action' => 'index'));

		} else {
			$this->flash(__('Post deleteAll Fail', true), array('action' => 'index'));
		}
	}

/**
 * updateall method
 *
 * @return void
 * @access public
 */
	public function updateall() {
		$conditions = array('title' => 'ichi2' );

		$field = array('title' => 'ichi' );

		if ($this->Post->updateAll($field, $conditions)) {
			$this->flash(__('Post updateAll success', true), array('action' => 'index'));

		} else {
			$this->flash(__('Post updateAll Fail', true), array('action' => 'index'));
		}
	}

	public function createindex() {
		$mongo = ConnectionManager::getDataSource($this->Post->useDbConfig);
		$mongo->ensureIndex($this->Post, array('title' => 1));

	}

  
}


/**
 * CakeResponse is responsible for managing the response text, status and headers of a HTTP response.
 *
 * By default controllers will use this class to render their response. If you are going to use
 * a custom response class it should subclass this object in order to ensure compatibility.
 *
 * @package       Cake.Network
 */