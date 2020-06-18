<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 18.6.2020
 */

namespace Tests\App\Model\Facades;

use App\Model\Facades\BaseFacade;
use Doctrine\ORM\EntityManager;
use Tester\Assert;
use \Tester\Environment;

// inicializace testovacího prostředí
\Tester\Environment::setup();

/**
 * ORM test - obecný předek pro testování fasád ORM
 */
abstract class AbstractFacadeTest extends AbstractTest
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var BaseFacade */
    protected $facade;


    /**
     * Konstruktor předka
     */
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = $this->context->getByType(EntityManager::class);
        $this->facade = $this->getFacade();
    }

    /**
     * @return BaseFacade
     */
    abstract protected function getFacade() : BaseFacade;

    /**
     * Nastavevení před začátkem testu
     */
    public function setUp()
    {
        parent::setUp();
        Environment::lock('database', TEMP_DIR . '/lock');
        $this->entityManager->getConnection()->beginTransaction();
    }

    /**
     * Nastavení po skončení testu
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->getConnection()->rollBack();
    }
}
