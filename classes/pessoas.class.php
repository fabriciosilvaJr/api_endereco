    <?php

    class Pessoas
    {

        public function listar (){
            try {
                $db = new DB(); 
                parse_str($_SERVER['QUERY_STRING'], $parameters);
                $conexao = $db ->connect();

                if (!is_null($parameters['codigoPessoa'])) {

                  $rs = $conexao->prepare("SELECT p.codigo_pessoa,
                    p.nome,
                    p.sobrenome,
                    p.idade,
                    p.login,
                    p.senha,
                    p.status,
                    e.codigo_endereco,
                    e.codigo_bairro,
                    e.nome_rua,
                    e.numero,
                    e.complemento,
                    e.cep,
                    b.codigo_municipio,
                    b.nome AS nome_bairro,
                    b.status AS status_bairro,
                    m.codigo_uf,
                    m.nome AS nome_municipio,
                    m.status AS status_municipio,
                    uf.sigla,
                    uf.nome AS nome_uf,
                    uf.status AS status_uf
                    FROM tb_pessoa p
                    INNER JOIN tb_endereco e ON e.codigo_pessoa = p.codigo_pessoa
                    INNER JOIN tb_bairro b ON b.codigo_bairro = e.codigo_bairro
                    INNER JOIN tb_municipio m ON m.codigo_municipio = b.codigo_municipio
                    INNER JOIN tb_uf uf ON uf.codigo_uf = m.codigo_uf
                    WHERE p.codigo_pessoa= :codigoPessoa");
                  $rs->execute(array(
                    'codigoPessoa' => $parameters['codigoPessoa']
                ));
                  $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);
                  if (count($obj) > 0) {
                    $response=array(
                        codigoPessoa=> $obj[0]['codigo_pessoa'],
                        nome=>   $obj[0]['nome'],
                        sobrenome=> $obj[0]['sobrenome'],
                        idade=>   $obj[0]['idade'],
                        login=> $obj[0]['login'],
                        senha=> $obj[0]['senha'],
                        status=> $obj[0]['status'],
                        enderecos => array_map(function ($pessoa) {
                            return array(
                                codigoEndereco       => $pessoa['codigo_endereco'],
                                codigoPessoa     => $pessoa['codigo_pessoa'],
                                codigoBairro => $pessoa['codigo_bairro'],
                                nomeRua    => $pessoa['nome_rua'],
                                numero    => $pessoa['numero'],
                                complemento    => $pessoa['complemento'],
                                cep    => $pessoa['cep'],
                                bairro => array(
                                   codigoBairro=> $pessoa['codigo_bairro'],
                                   codigoMunicipio=> $pessoa['codigo_municipio'],
                                   nome=> $pessoa['nome_bairro'],
                                   status=>$pessoa['status_bairro'],
                                   municipio=>array(
                                    codigoMunicipio=> $pessoa['codigo_bairro'],
                                    codigoUF=> $pessoa['codigo_uf'],
                                    nome=> $pessoa['nome_municipio'],
                                    status=> $pessoa['status_municipio'],
                                    uf=>array(
                                        codigoUF=>$pessoa['codigo_uf'], 
                                        sigla=>$pessoa['sigla'],
                                        nome=>$pessoa['nome_uf'],
                                        status=>$pessoa['status_uf'],

                                    )

                                )
                               )
                            );
                        }, $obj)


                    );

                    echo json_encode($response);


                } else {

                    echo json_encode([]);

                }

            }


            if (!is_null($parameters['login']) || !is_null($parameters['status'])) {
             $parametros  = [];
             $sql = 'SELECT * FROM tb_pessoa WHERE 1 = 1 ';
             if(!is_null($parameters['login'])){
                 $sql .= ' AND login = :login ';
                 $parametros += array(
                    'login' => $parameters['login']
                );
             }
             if(!is_null($parameters['status'])){
                 $sql .= ' AND status = :status ';
                 $parametros += array(
                    'status' => $parameters['status']
                );
             }

             $sql .= " ORDER BY codigo_pessoa DESC ";


             $rs = $conexao->prepare($sql);
             $rs->execute($parametros);
             $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);

             $result = array_map(function ($pessoa) {
                return [
                    codigoPessoa => $pessoa['codigo_pessoa'],
                    nome => $pessoa['nome'],
                    sobrenome => $pessoa['sobrenome'],
                    idade => $pessoa['idade'],
                    login => $pessoa['login'],
                    senha => $pessoa['senha'],
                    status => $pessoa['status'],
                    enderecos => [],
                ];
            }, $obj);



             echo json_encode($result);

         }

         $rs = $conexao->prepare("SELECT * FROM tb_pessoa order by codigo_pessoa DESC");
         $rs->execute();
         $obj =  $rs-> fetchAll(PDO::FETCH_ASSOC);

         $semFiltro = array_map(function ($pessoa) {
          return [     
            codigoPessoa => $pessoa['codigo_pessoa'],
            nome => $pessoa['nome'],
            sobrenome => $pessoa['sobrenome'],
            idade => $pessoa['idade'],
            login => $pessoa['login'],
            senha => $pessoa['senha'],
            status => $pessoa['status'],
            enderecos => [],
        ];
    }, $obj);


         if(is_null($parameters['codigoPessoa']) && is_null($parameters['login']) && is_null($parameters['status'])){
           echo json_encode($semFiltro);


       }  

   } catch (PDOException $e) {
       //print "Error!: " . $e->getMessage() . "</br>";
      echo json_encode(array("mensagem" => "Não foi possível consultar Pessoa no banco de dados.", "status" => 404));
      http_response_code(404); 

  }




}



public function adicionar(){
    try {
        $req = json_decode(file_get_contents('php://input'), true);
        $db = new DB();
        $conexao = $db ->connect();
        $conexao->beginTransaction();
        $rs =  $conexao->prepare("INSERT INTO tb_pessoa (codigo_pessoa,nome,sobrenome, idade, login, senha, status)
         Values (nextval('sequence_pessoa'), :nome, :sobrenome, :idade, :login, :senha, :status)");

        $exec =  $rs->execute(array(
            'nome' => $req['nome'],
            'sobrenome' => $req['sobrenome'],
            'idade' => $req['idade'],
            'login' => $req['login'],
            'senha' => $req['senha'],
            'status' => $req['status'],

        ));


        if($exec){

         $codigoPessoa = $conexao->lastInsertId();
                 //var_dump($codigoPessoa);
         $contador = count($req['enderecos']);
         $queryEndereco = "INSERT INTO tb_endereco (codigo_endereco, codigo_pessoa, codigo_bairro, nome_rua, numero, complemento, cep)Values (nextval('sequence_endereco'), :codigoPessoa , :codigoBairro, :nomeRua, :numero, :complemento, :cep)";
         $rsE = $conexao->prepare($queryEndereco);
         for ($i=0; $i <  $contador; $i++) { 
            $execEd = $rsE->execute( array_merge(array(
                'codigoPessoa' => $codigoPessoa
            ),$req['enderecos'][$i])); 


        }

        if($execEd){
           $conexao->commit();
           $pessoas = new Pessoas();
           $pessoas ->listar();

       }



   } 

} catch (PDOException $e) {
  $conexao->rollback();
          //print "Error!: " . $e->getMessage() . "</br>";
  echo json_encode(array("mensagem" => "Não foi possível inserir Pessoa no banco de dados.", "status" => 404));
  http_response_code(404); 



}



}

public function  alterar(){

    try {
        $req = json_decode(file_get_contents('php://input'), true);
        $db = new DB();
        $conexao = $db ->connect();
        $conexao->beginTransaction(); 

        // Aatualizando pessoas no banco de dados
        $rs =  $conexao->prepare("UPDATE tb_pessoa set nome = :nome, sobrenome= :sobrenome, idade = :idade, login = :login, senha= :senha, status= :status WHERE codigo_pessoa = :codigoPessoa");

        $exec =  $rs->execute(array(
            'nome' => $req['nome'],
            'sobrenome' => $req['sobrenome'],
            'idade' => $req['idade'],
            'login' => $req['login'],
            'senha' => $req['senha'],
            'status' => $req['status'],
            'codigoPessoa' => $req['codigoPessoa'],


        ));

        $rsEd =  $conexao->prepare("SELECT * FROM tb_endereco  where codigo_pessoa= :codigoPessoa");
        $execEd = $rsEd->execute(array(
            'codigoPessoa' => $req['codigoPessoa'],
        )); 
        $obj =  $rsEd-> fetchAll(PDO::FETCH_ASSOC);

        $pessoaByid = array_map(function ($data) {
          return [     
            'codigoEndereco'       => $data['codigo_endereco'],
            'codigoPessoa'     => $data['codigo_pessoa'],
            'codigoBairro' => $data['codigo_bairro'],
            'nomeRua'    => $data['nome_rua'],
            'numero'    => $data['numero'],
            'complemento'    => $data['complemento'],
            'cep'    => $data['cep'],
        ];
    }, $obj);


        function getDiferenca($array1, $array2) {
            return array_filter($array1, function($object1) use ($array2) {
                return !array_reduce($array2, function($carry, $object2) use ($object1) {
                    return $carry || ($object1['codigoEndereco'] === $object2['codigoEndereco']);
                }, false);
            });
        }
       // Deletando endereço que não foi passado na atualização
        $diferenca =  getDiferenca($pessoaByid, $req['enderecos']);

        if (count($diferenca) > 0) {
            foreach ($diferenca as $u) {
                $codigoEndereco = $u['codigoEndereco'];
                $query = "DELETE FROM tb_endereco WHERE codigo_endereco = :codigoEndereco";
                $stmt = $conexao->prepare($query);
                $stmt->bindParam(':codigoEndereco', $codigoEndereco, PDO::PARAM_INT);
                $stmt->execute();
            }


        }

       // Adicionando endereço que não foi passado o codigoEndereco na atualização

        $adicionar = array();
        foreach ($req['enderecos'] as $endereco) {
            if ($endereco['codigoEndereco'] === null) {
                $adicionar[] = $endereco;
            }
        }

        if (count($adicionar) > 0) {

            $query = "INSERT INTO tb_endereco (codigo_endereco, codigo_pessoa, codigo_bairro, nome_rua, numero, complemento, cep)
            VALUES (nextval('sequence_endereco'), :codigoPessoa, :codigoBairro, :nomeRua, :numero, :complemento, :cep)";

            $rs = $conexao->prepare($query);
            foreach ($adicionar as $add) {

             $exec= $rs->execute($add); 

         }
     }


   // Atualizando endereços que foi passado o cddigoEndreço

     $enderecos = array();
     foreach ($req['enderecos'] as $endereco) {
        if ($endereco['codigoEndereco'] !== null) {
            $enderecos[] = $endereco;
        }
    }

    if (count($enderecos) > 0) {

        $queryEndereco = "UPDATE tb_endereco 
        SET codigo_pessoa = :codigoPessoa, codigo_bairro = :codigoBairro, nome_rua = :nomeRua, numero = :numero, complemento = :complemento, cep = :cep 
        WHERE codigo_endereco = :codigoEndereco";


        for ($i = 0; $i < count($enderecos); $i++) {


            $rs = $conexao->prepare($queryEndereco);
            $rs->execute(
                array(
                    'codigoPessoa' => $enderecos[$i]['codigoPessoa'],
                    'codigoBairro' => $enderecos[$i]['codigoBairro'],
                    'nomeRua' => $enderecos[$i]['nomeRua'],
                    'numero' => $enderecos[$i]['numero'],
                    'complemento' => $enderecos[$i]['complemento'],
                    'cep' => $enderecos[$i]['cep'],
                    'codigoEndereco' => $enderecos[$i]['codigoEndereco'],


                )

            );
        }
        $conexao->commit();
        $pessoas = new Pessoas();
        $pessoas ->listar();


    }



} catch (PDOException $e) {
  $conexao->rollback();
  print "Error!: " . $e->getMessage() . "</br>";
  echo json_encode(array("mensagem" => "Não foi possível alterar Pessoa no banco de dados.", "status" => 404));
  http_response_code(404); 


}

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

