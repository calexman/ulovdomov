<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model;

use App\Model\Entities\User;
use App\Model\Entities\AuthorizationType;
use App\Model\Facades\UserFacade;
use App\Model\Facades\AuthorizationTypeFacade;
use App\Model\Facades\VillageFacade;
use App\Model\Exceptions\EntityNotFoundException;
use App\Model\Exceptions\BadFormatException;
use Nette;

/**
 * Model pro práci s autorizací
 */
class Authorization {

    use Nette\SmartObject;

    /**
     * @var UserFacade Fasáda pro manipulaci s uživateli.
     */
    private $userFacade;

    /**
     * @var VillageFacade Fasáda pro manipulaci s uživateli.
     */
    private $villageFacade;

    /**
     * @var AuthorizationTypeFacade Fasáda pro manipulaci s typy autorizace
     */
    private $authorizationTypeFacade;

    /**
     * Konstruktor modelu
     * 
     * @param UserFacade $userFacade reference na fasádu uživatelů - získáno pomocí DI
     * @param AuthorizationTypeFacade $authTypeFacade reference na fasádu typů autorizace - získáno pomocí DI
     * @param VillageFacade $villageFacade reference na fasádů lokalit - získáno pomocí DI
     * @param UserAdminFacade $userAdminFacade reference na fasádu orpávnění uživatelů - získáno pomocí DI
     */
    public function __construct(
            UserFacade $userFacade,
            AuthorizationTypeFacade $authTypeFacade,
            VillageFacade $villageFacade) {
        $this->userFacade = $userFacade;
        $this->authorizationTypeFacade = $authTypeFacade;
        $this->villageFacade = $villageFacade;
    }

    /**
     * Vrací autorizační práva uživatele pro zadaný typ oprávnění.
     * 
     * Poznámka: určeno spíše pro testovací úkoly, aby bylo možné získávat
     * data pouze pomocí primitovních dat bez vytvořených entit uživatele a 
     * typu autorizace
     * 
     * @param int $userId ID uživatele, pro kterého se hledají oprávnění
     * @param string $authTypeCode - textový kód oprávnění
     * @return array ve formátu [village_id => village_name] s lokalitami, kde má uživatel požadované oprávnění
     * @throws EntityNotFoundException
     */
    public function getByIdAndCode(int $userId, string $authTypeCode): Array {
        // získání entity uživatele
        $user = $this->userFacade->getUser($userId);
        if (!$user) {
            throw new EntityNotFoundException("Can't find user with ID '{$userId}'.");
        }

        // získání entity typu autorizace
        $authType = $this->authorizationTypeFacade->getAuthorizationType($authTypeCode);
        if (!$authType) {
            throw new EntityNotFoundException("Can't find authorizatinType with ID '{$authTypeCode}'.");
        }

        // získáýní dat k autorizaci
        return $this->get($user, $authType);
    }

    /**
     * Vrací autorizační práva uživatele pro zadaný typ oprávnění.
     * 
     * @param User $user Entita uživatele, pro kterou se získává autorizace
     * @param AuthorizationType $authType - Entita typu oprávnění, pro které se získává autorizce
     * @return array ve formátu [village.id => village.name] s lokalitami, kde má uživatel požadované oprávnění
     * @throws EntityNotFoundException
     */
    public function get(User $user, AuthorizationType $authType): Array {
        //získání současné konfigurace autorizace uživatele
        $data = $user->getAuthorizationByType($authType);
        if (!count($data)) {
            throw new EntityNotFoundException("User with ID '{$user->id}' has no authorization data set.");
        }

        // přestavění pole do podoby [village.id => village.name]
        return array_reduce($data, function($result, $item) {
            $result[$item->village->id] = $item->village->name;
            return $result;
        });
    }

    /**
     * Nastavuje práva uživateli podle zadaných parametrů
     * 
     * Poznámka: určeno spíše pro testovací úkoly, aby bylo možné nastavovat
     * data pouze pomocí id uživatele bez vytvořené entity
     * 
     * @param int $userId ID uživatele, kterému jsou oprávnění nastavována
     * @param array $authArray pole s nastavením oprávnění uživatele
     *      Formát pole: [authorization_type.code => [village.id => bool], ...]
     *      Poznámka: pro účely ukázkového řešení jsem počítal s přístupem, že při nastavování
     *      oprávnění musí být vždy pole ve formátu, kdy jsou vyjmenována všechny existující
     *      a pro ně všechny existující lokality
     * @throws EntityNotFoundException
     */
    public function setById(int $userId, Array $authArray): void {

        // získání entity uživatele
        $user = $this->userFacade->getUser($userId);
        if (!$user) {
            throw new EntityNotFoundException("Can't find user with ID={$userId}");
        }
        //nastavení autorizace
        $this->set($user, $authArray);
    }

    /**
     * Nastavuje práva uživateli podle zadaných parametrů
     * 
     * @param User $user entita uživatele, kterému jsou oprávnění nastavována
     * @param array $authArray pole s nastavením oprávnění uživatele
     *      Formát pole: [authorization_type.code => [village.id => bool], ...]
     *      Poznámka: pro účely ukázkového řešení jsem počítal s přístupem, že při nastavování
     *      oprávnění musí být vždy pole ve formátu, kdy jsou vyjmenována všechny existující
     *      a pro ně všechny existující lokality
     */
    public function set(user $user, Array $authArray) : void {
        // poznámka: volání této metody by mohlo být obaleno v try-catch statementu
        // ale z mého pohledu by případná exception při zápisu do DB nebo 
        // ve špatném formátu vstupu měla být odchycena až o úroveň výše
        // společně s nějakou formou fail recovery
        // ověření správnosti a přeskládání formátu pole do interního stavu
        $setData = $this->checkAndRemapData($authArray);

        // nastavení autorizace
        $this->userFacade->setAuthorizations($user, $setData);
    }

    /**
     * Metoda provede ověření formátu dat a přeskládání do formátu vhodného
     * pro zápis do DB
     * 
     * @param array $authArray pole s nastavením orpvánění uživatele
     * @return array pole s oprávněním uživatele vhodné pro zápis do DB
     *      formát: ["authType" =>AuthorizationType, "village" => Village, "User"=>User]
     * @throws BadFormatException
     */
    private function checkAndRemapData(Array $authArray): Array {
        // získání dat pro ověření formátu pole a přeskládání
        $allVillages = $this->villageFacade->getAllVillages();
        $allAuthTypes = $this->authorizationTypeFacade->getAllAuthorizationTypes();
        $allCitiesKeys = array_keys($allVillages);
        $authTypesRemap = array_reduce($allAuthTypes, function($result, $item) {
            $result[$item->code] = $item->id;
            return $result;
        });
        $retArray = [];

        // porovná jestli typy oprávnění v zadaném poli souhlasí s možnými typy oprávnění
        if (!$this->compareArrays(array_keys($authTypesRemap), array_keys($authArray))) {
            throw new BadFormatException("Bad format of authorization types in array. There must be exactly one record for each authorization type.");
        }

        // pro každý typ oprávnění
        foreach ($authArray as $authTypeCode => $values) {
            $authType = $allAuthTypes[$authTypesRemap[$authTypeCode]];

            // porovná jestli se zadaná mšsta shodují s možnými městy
            if (!$this->compareArrays($allCitiesKeys, array_keys($values))) {
                throw new BadFormatException("Bad format of cities and authorization types array in key '{$authType->code}'. There must be exactly one record for each city.");
            }
            
            // pokud nebyla ve formuláři vybrána žádná hodnota "true" pro danou autorizaci
            if (!in_array(true, $authArray[$authType->code])) {
                // nastavíme přístup pro všechna města u daného typu autorizace
                $retArray = $this->addAuthForAllCities($authType, $allVillages, $retArray);
            } else {
                // nastavení pouze pro ta města která mají ve formuláři zadáno true
                $retArray = $this->addAuthByArray($authType, $values, $allVillages, $retArray);
            }
        }
        return $retArray;
    }

    /**
     * Porovmá dvě pole, jestli mají stejné hodnoty - pořadí hodnot (kliče) se neřeší.
     * @param array $firstArray
     * @param array $secondArray
     * @return bool - true: pole mají stejné prvky
     */
    private function compareArrays(Array $firstArray, Array $secondArray): bool {
        return count(array_diff($firstArray, $secondArray)) === 0;
    }

    /**
     * Přidá do oprvnění všechny kombinace měst pro daný typ oprávnění
     * @param AuthorizationType $authType Entita typu oprávnění
     * @param array $allVillages pole obsahující entity všech dostupných lokalit
     * @param array $remapArray pole oprvánění v DB podobě
     * @return array pole oprvánění v DB podobě s přidanými položkami
     */
    private function addAuthForAllCities(AuthorizationType $authType, Array $allVillages, Array $remapArray): Array {
        foreach ($allVillages as $villageId => $villageName) {
            $remapArray[] = ["authType" => $authType, "village" => $allVillages[$villageId]];
        }
        return $remapArray;
    }

    /**
     * Přidá do pole pro daný typ oprávnění 
     * @param AuthorizationType $authType Entita typu oprávnění
     * @param array $authorizations seznam nastavení oprávnění pro daný typ oprávnění
     * @param array $allVillages seznam entit všech dostupných lokalit
     * @param array $remapArray pole oprvánění v DB podobě
     * @return array pole oprvánění v DB podobě s přidanými položkami
     */
    private function addAuthByArray(AuthorizationType $authType, Array $authorizations, Array $allVillages, Array $remapArray): Array {
        foreach ($authorizations as $villageId => $value) {
            if ($value) {
                $remapArray[] = ["authType" => $authType, "village" => $allVillages[$villageId]];
            }
        }
        return $remapArray;
    }

}

/**
 * Továrnička pro vytvoření modelu autorizace. 
 */
interface IAuthorizationFactory {

    /**
     * @return \App\Model\Authorization
     */
    function create(): Authorization;
}
