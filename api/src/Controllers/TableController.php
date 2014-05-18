<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 14.5.17
 * Time: 23.26
 */

namespace Controllers;

use Services\TableService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

class TableController
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var TableService
     */
    protected $tableService;

    public function __construct($twigService, $tableService)
    {
        $this->twig = $twigService;
        $this->tableService = $tableService;
    }

    /**
     * Main page to say hello and show table status via json api
     *
     * @return string
     */
    public function index()
    {
        return $this->twig->render('index.twig');
    }

    /**
     * Get table status.
     *
     * returns table status:
     * status: ok
     * message: table free|table busy
     *
     * @return JsonResponse
     */
    public function status()
    {
        return $this->tableService->getTableStatus();
    }
} 