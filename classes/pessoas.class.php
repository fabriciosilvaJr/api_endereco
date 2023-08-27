    <?php

    class Pessoas
    {

    

        public function listar (){

        }

        public function adicionar(){

        }

        public function  alterar(){

        }

        public function deletar(){

        }




        public function login()

        {
            $req = json_decode(file_get_contents('php://input'), true);

            if ($req) {
                if (!$req['login'] OR !$req['senha']) {
                    echo json_encode(['ERRO' => 'Falta informacoes!']); exit; 
                } else {
                    $login = $req['login'];
                    $senha = $req['senha'];

                     $secretJWT = $GLOBALS['secretJWT'];


                    $db = DB::connect();
                     $rs = $db->prepare("SELECT * FROM TB_PESSOA WHERE login = :login");
                     $exec = $rs->execute(array(
                        'login' => $login
                    ));
                    $obj = $rs->fetchObject();
                    $rows = $rs->rowCount();

                    if ($rows > 0) {
                        $idDB          = $obj->codigo_pessoa;
                        $nameDB        = $obj->nome;
                        $passDB        = $obj->senha;
                        $validUsername = true; 
                        // $validPassword = password_verify($senha, $passDB) ? true : false;

                        if ($passDB == $senha) {
                            $validPassword = true;
                        }

                    } else {
                        $validUsername = false;
                        $validPassword = false;
                    }

                    if ($validUsername and $validPassword) {
                        //$nextWeek = time() + (7 * 24 * 60 * 60);
                        $expire_in = time() + 60000;
                        $token     = JWT::encode([
                            'id'         => $idDB,
                            'name'       => $nameDB,
                            'expires_in' => $expire_in,
                        ], $GLOBALS['secretJWT']);

                        // $db->query("UPDATE usuarios SET token = '$token' WHERE id = $idDB");
                        echo json_encode(['token' => $token, 'data' => JWT::decode($token, $secretJWT)]);
                    } else {
                        if (!$validPassword) {  
                             echo json_encode(array("mensagem" => "Login ou Senha inválida", "status" => 404));
                            http_response_code(404); exit;

                        }
                    }
                }
            } else {
                echo json_encode(['ERRO' => 'Falta informacoes!']); exit; 
            }

        }

        public function verificar()
        {
              
         try {
            $headers = getallheaders();
              //var_dump($headers['Authorization']);
             if (isset($headers['Authorization'])) {
                $token = str_replace("Bearer ", "", $headers['Authorization']);
                $secretJWT = $GLOBALS['secretJWT'];
                $decodedJWT = JWT::decode($token, $secretJWT);
                 
                return true;

            } else  {
                echo json_encode(['mensagem' => 'Você não está logado, ou seu token é inválido.']);
                 http_response_code(404);
                return false;
               

                
            }
             
         } catch (Exception $e) {
            echo json_encode(['mensagem' => 'Você não está logado, ou seu token é inválido.']);
             http_response_code(404);
                return false;

             
         }
        
           

        }
    }

