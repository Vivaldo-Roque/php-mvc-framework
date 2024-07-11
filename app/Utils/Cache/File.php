<?php

namespace App\Utils\Cache;

class File {

    /**
     * Metodo responsavel por retornar o caminho ate o arquivo de cache
     * @param string $hash
     * @return string
     */
    private static function getFilePath($hash){
        // Diretorio de cache
        $dir = getenv('CACHE_DIR');

        // Verifica a existencia do diretorio
        if(!file_exists($dir)){
            mkdir($dir, 0755, true);
        }

        // Retorna o caminho ate o arquivo
        return $dir . '/'. $hash;
    }

    /** 
     * Metodo responsavel por guardar informacoes no cache
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($hash, $content){
        // Serializa o retorno
        $serialize = serialize($content);

        // Obtem o caminho ate o arquivo de cache
        $cacheFile = self::getFilePath($hash);

        // Grava as informacoes no arquivo
        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * Metodo responsavel por guardar informacoes no cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    public static function getContentCache($hash, $expiration){
        // Obtem o caminho do arquivo
        $cacheFile = self::getFilePath($hash);

        // Verifica a existencia do arquivo
        if(!file_exists($cacheFile)){
            return false;
        }

        // Valida a expiracao do tempo
        // Verificado a ultima data da modificacao do arquivo
        // Linux => filectime
        // Windows => filemtime
        $createTime = filemtime($cacheFile);
        $diffTime = time() - $createTime;

        if($diffTime > $expiration){
            return false;
        }

        // Retorna o dado real
        $serialize = file_get_contents($cacheFile);
        return unserialize($serialize);
    }

    /**
     * Metodo responsavel por obter uma informacao de cache
     * @param string $hash
     * @param integer $expiration
     * @param Closure $function
     * @return mixed
     */
    public static function getCache($hash, $expiration, $function){
        
        // Verifica o conteudo gravado
        if($content = self::getContentCache($hash, $expiration)){
            return $content;
        }
        
        // Execucao da funcao
        $content = $function();

        //Grava o retorno no cache
        self::storageCache($hash, $content);

        // Retorna o conteudo
        return $content;
    }

}