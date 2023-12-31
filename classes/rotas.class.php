<?php 

	class Rotas{


	    private $listaRotas = [''];
	    private $listaCallback = [''];
	    private $listaProtecao = [''];



		public function add($metodo, $rota, $callback, $protecao){

		$this->listaRotas[] = strtoupper($metodo).':'.$rota;
        $this->listaCallback[] = $callback;
        $this->listaProtecao[] = $protecao;

        return $this;

		}

		public function ir($rota){
	    $param = '';
        $callback = '';
        $protecao = '';
        $methodServer = $_SERVER['REQUEST_METHOD'];
        $methodServer = isset($_POST['_method']) ? $_POST['_method'] : $methodServer;
        $rota = $methodServer.":/".$rota;

        //var_dump(substr_count($rota, "/"));

        
        if (substr_count($rota, "/") >= 2) {
            $param = substr($rota, strrpos($rota, "/")+1);
            if($param != login){
                  $rota = substr($rota, 0, strrpos($rota, "/"))."/[PARAM]";

            }

        }
        
        $indice = array_search($rota, $this->listaRotas);
        if ($indice > 0) {
            $callback = explode("::", $this->listaCallback[$indice]);
            $protecao = $this->listaProtecao[$indice];
        }

        $class = isset($callback[0]) ? $callback[0] : '';
        $method = isset($callback[1]) ? $callback[1] : '';


        if (class_exists($class))
        {
            if (method_exists($class, $method))
            {
                $instanciaClass = new $class();
                if ($protecao) {
                    $verificacao = new Pessoas();
                    if ($verificacao->verificar()) {
                        return call_user_func_array(
                            array($instanciaClass, $method),
                            array($param)
                        );
                    } 
                } else {
                    return call_user_func_array(
                        array($instanciaClass, $method),
                        array($param)
                    );
                }
            } else {
                $this->naoExiste();
            }
        } else {
            $this->naoExiste();
        }

		}

		public function naoExiste()
		{
            echo json_encode(array("mensagem" => "Não foi possível encontrar a rota.", "status" => 404));
			http_response_code(404);
		}



	}


 ?>