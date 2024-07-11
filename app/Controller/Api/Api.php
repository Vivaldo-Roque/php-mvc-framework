<?php

namespace App\Controller\Api;

class Api
{

    /**
     * 
     * Metodo responsavel por retornar os detalhes da API
     * @param Request $request
     * @return array
     */
    public static function getDetails($request)
    {
        return [
            'nome' => 'API MVC',
            'versao' => 'v1.0.0',
            'autor' => 'Vivaldo Roque',
            'email' => '2001vivaldo@gmail.com'
        ];
    }

    /**
     * 
     * Método responsável por retornar os detalhes da paginacao
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     * 
     */
    protected static function getPagination($request, $obPagination)
    {

        // Query params
        $queryParams = $request->getQueryParams();

        // Paginas
        $pages = $obPagination->getPages();

        // Pagina atual
        $currentPage = 1;
        if(isset($queryParams['page'])){
            $currentPage = (int)$queryParams['page'];
        }

        // Total de paginas
        $totalPages = 1;
        if(!empty($pages)){
            $totalPages = count($pages);
        }

        // retorno
        return [
            'paginaAtual' => $currentPage,
            'quantidadePaginas' => $totalPages
        ];
    }
}
