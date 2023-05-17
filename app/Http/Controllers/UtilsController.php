<?php

namespace App\Http\Controllers;
use Log;

trait UtilsController {
    public function remove_acentos($var) {
        $array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
        ,"Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", " ", ",", "/","'","%","#", "?" );

        $array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
        ,"A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "_", "", "_","","","", "" );

        $var = str_replace( $array1, $array2, $var);

        $var = mb_strtolower($var);

        return $var;
    }

    private function validate_ip($ip){
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
        return true;
    }

    public function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) ) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP

                    if ($this->validate_ip($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
    }

    public function traduz_mes($mes){
        $mes = ($mes == 'January') ? 'Janeiro' : $mes;
        $mes = ($mes == 'February') ? 'Fevereiro' : $mes;
        $mes = ($mes == 'March') ? 'Março' : $mes;
        $mes = ($mes == 'April') ? 'Abril' : $mes;
        $mes = ($mes == 'May') ? 'Maio' : $mes;
        $mes = ($mes == 'June') ? 'Junho' : $mes;
        $mes = ($mes == 'July') ? 'Julho' : $mes;
        $mes = ($mes == 'August') ? 'Agosto' : $mes;
        $mes = ($mes == 'September') ? 'Setembro' : $mes;
        $mes = ($mes == 'October') ? 'Outubro' : $mes;
        $mes = ($mes == 'November') ? 'Novembro' : $mes;
        $mes = ($mes == 'December') ? 'Dezembro' : $mes;

        return $mes;
    }

    public function traduz_mes_para_numero($mes){
        $mes = ($mes == 'Janeiro') ? 1 : $mes;
        $mes = ($mes == 'Fevereiro') ? 2 : $mes;
        $mes = ($mes == 'Março') ? 3 : $mes;
        $mes = ($mes == 'Abril') ? 4 : $mes;
        $mes = ($mes == 'Maio') ? 5 : $mes;
        $mes = ($mes == 'Junho') ? 6 : $mes;
        $mes = ($mes == 'Julho') ? 7 : $mes;
        $mes = ($mes == 'Agosto') ? 8 : $mes;
        $mes = ($mes == 'Setembro') ? 9 : $mes;
        $mes = ($mes == 'Outubro') ? 10 : $mes;
        $mes = ($mes == 'Novembro') ? 11 : $mes;
        $mes = ($mes == 'Dezembro') ? 12 : $mes;

        return $mes;
    }

    public function envia_email_service($para,$assunto,$mensagem, $nome_dest){
    	try{

			$token="ca1761f0-ddd6-11e8-b7de-3bf7a5419b83";
			date_default_timezone_set('America/Sao_Paulo');
		    $fields = array("email" => $para, "texto" => $mensagem, "token"=>$token, "titulo" => $assunto, "nome"=>$nome_dest, "prioridade"=> 2, "anexos"=> [], "agendado_para"=> date("Y-m-d H:m:s") );

	        $data_string = json_encode($fields);

	        $ch = curl_init('https://gestormail.portalgov.com.br/email');
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                'Content-Type: application/json',
	                'Content-Length: ' . strlen($data_string))
	        );
			//execute post
			$result = curl_exec($ch);

			$resp = json_decode($result);

            return $resp;

		}
		catch(Exception $e){
            //echo $e->getMessage();
            return array (
                "code" => 500
            );
		}
	}

}
