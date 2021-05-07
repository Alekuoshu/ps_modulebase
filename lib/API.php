<?php
/**
* Clase para consumir API Rest
* Las operaciones soportadas son:
* 	
* 	- POST		: Agregar
* 	- GET		: Consultar
* 	- DELETE	: Eliminar
* 	- PUT		: Actualizar
* 	- PATCH		: Actualizar por parte
* 
* Extras
* 	- autenticación de acceso básica (Basic Auth)
*  	- Conversor JSON
 *
 * @author     	Alekuoshu <alekuoshu@gmail.com>
 * @version 	1.0
 */
class API{

	/**
	 * autenticación de acceso básica (Basic Auth)
	 * Ejemplo Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==
	 *
	 * @param string $URL url para acceder y obtener un token
	 * @param string $usuario usuario
	 * @param string $password clave
	 * @return JSON
	 */
	static function Authentication($URL,$usuario,$password){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$usuario:$password");
		$result = curl_exec($ch);
		curl_close($ch);  
		return $result;
	}

	/**
	 * autenticación de acceso (Auth0 2.0)
	 *
	 * @param string $URL url para acceder y obtener un token
	 * @param string $params
	 * @return JSON
	 */
	static function Authentication2($params,$URL){
		   
		$str_params = '';
		foreach($params as $key=>$value) {
			$str_params .= $key . "=" . urlencode($value) . "&";
		}
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $URL,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_HTTPHEADER => array('Content-Type:application/x-www-form-urlencoded'),
		CURLOPT_POSTFIELDS => $str_params
		));
		$curl_response = curl_exec($curl);
		curl_close($curl);  
		return $curl_response;
	}

	/**
	 * Enviar parámetros a un servidor a través del protocolo HTTP (POST).
	 * Se utiliza para agregar datos en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso
	 * @param string $TOKEN token de autenticación
	 * @param array $ARRAY parámetros a envíar
	 * @return JSON
	 */
	static function POST($URL,$TOKEN,$ARRAY){
		// $datapost = '';
		// foreach($ARRAY as $key=>$value) {
		//     $datapost .= $key . "=" . $value . "&";
		// }

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$ARRAY);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$TOKEN, 'Content-Type: application/json'));
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Enviar parámetros a un servidor a través del protocolo HTTP (POST).
	 * Se utiliza para agregar datos en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso
	 * @param array $ARRAY parámetros a envíar
	 * @return JSON
	 */
	static function POSTBASIC($URL,$ARRAY){

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$ARRAY);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Consultar a un servidor a través del protocolo HTTP (GET).
	 * Se utiliza para consultar recursos en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso/(id) no obligatorio
	 * @param string $TOKEN token de autenticación
	 * @return JSON
	 */
	static function GET($URL,$TOKEN){
		$headers 	= array('Authorization: Bearer ' . $TOKEN);
		$ch 		= curl_init();
		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Consultar a un servidor a través del protocolo HTTP (DELETE).
	 * Se utiliza para eliminar recursos en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso/id
	 * @param string $TOKEN token de autenticación
	 * @return JSON
	 */
	static function DELETE($URL,$TOKEN){
		$headers 	= array('Authorization: Bearer ' . $TOKEN);
		$ch 		= curl_init();

		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Enviar parámetros a un servidor a través del protocolo HTTP (PUT).
	 * Se utiliza para editar un recurso en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso/id
	 * @param string $TOKEN token de autenticación
	 * @param array $ARRAY parámetros a envíar
	 * @return JSON
	 */
	static function PUT($URL,$TOKEN,$ARRAY){
		$datapost = '';
		foreach($ARRAY as $key=>$value) {
		    $datapost .= $key . "=" . $value . "&";
		}

		$headers 	= array('Authorization: Bearer ' . $TOKEN);
		$ch 		= curl_init();
		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datapost);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Enviar parámetros a un servidor a través del protocolo HTTP (PATCH).
	 * Se utiliza para editar un atributo específico (recurso) en una API REST
	 *
	 * @param string $URL URL recurso, ejemplo: http://website.com/recurso/id
	 * @param string $TOKEN token de autenticación
	 * @param array $ARRAY parametros parámetros a envíar
	 * @return JSON
	 */
	static function PATCH($URL,$TOKEN,$ARRAY){
		$datapost = '';
		foreach($ARRAY as $key=>$value) {
		    $datapost .= $key . "=" . $value . "&";
		}

		$headers 	= array('Authorization: Bearer ' . $TOKEN);
		$ch 		= curl_init();
		curl_setopt($ch,CURLOPT_URL,$URL);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datapost);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Convertir JSON a un ARRAY
	 *
	 * @param json $json Formato JSON
	 * @return ARRAY
	 */
	static function JSON_TO_ARRAY($json){
		return json_decode($json,true);
	}
}
?>