Vypracování ukázkového projektu pro ulovdomov.  Viz [zadání](https://www.ulovdomov.cz/front/other/jirka "zadání").
Výsledná implementace by měla být plně funkční a plně odpovídat zadání. Případné nejasnosti/odlišnosti by měli být popsány níže v poznámkách k řešení a v kódu.

## Prostředí a instalace
Původně jsem si chtěl trochu *zamachrovat* a použít PHP 7.4 a nové Nette 3.x, ale po několika hodinách pokusů slinkovat balíčky dohromady tak aby fungovali jsem se rozhodl, že toto nechám na budoucí studium a použiji již ověřenou kombinaci.
#### Použitá technologie
- PHP 5.6
- Nette 2.4 (použit web-project skeleton)
- Mysql
- Doctrine 2

#### Instalace
Po naklonování repozitáře z GitHubu by mělo stačit stáhnout vendor přes composer:
`composer update`

Provést restore databáze z přiloženého dumpu:
`mysql -u [user] -p [database_name] < MySql\dbDump.sql`

Ve složce `app/config/` vytvořit `config.local.neon` jako kopii souboru `config.local.neon.sample ` a zapsat přihlašovací údaje do databáze.

Nyní by mělo být možné používat a testovat ukázkový model.
## Model a jeho používání
Většina logiky se odehrává v modelu Authorization.php
Továrničku modelu je možné si vstříknout pomocí Dependency Injection, své závislost si pak model také získá pomocí DI sám. 
Příklad injekce do presenteru:

	` /**
	 *@var IAuthorizationFactory Fasáda pro manipulaci s typy autorizace
     * @inject
     */
    public $authorizationFactory;
	
	//vytvoření instance modelu
	$auth = $this->authorizationFactory->create();`

Model obsahuje základní veřejné metody:

    get(User $user, AuthorizationType $authType): Array
    set(user $user, Array $authArray) : void
a zároveň dvě pomocné metody pro otestování bez nutnosti vytváření Entit které jsou předávány parametry. U následujících metod stačí znát pouze ID uživatele, případně kód požadovaného oprávnění:

    getByIdAndCode(int $userId, string $authTypeCode): Array
    setById(int $userId, Array $authArray): void
## Poznámky k řešení
 - databázový návrh: chvíli jsem přemýšlel jestli návrh db ještě malinko nezploštit. Ač zadání specifikuje, že typy oprávnění jsou pouze dvě, zvolil jsem možnost, že i typy oprávnění budou v čase přidávány/mazány, což mě dovedlo k současné podobě čtyř tabulek (ER diagram přiložen ve složce MySQL):
 	- tabulka village - lokality
 	- tabulka user - uživatelé
 	- tabulka authorization_type - typy oprávnění (zde je k diskuzi, zda-li by jako primární 	klíč nepostačila jedinečná hodnota sloupce *code* )
 	- tabulka user_admin - tato tabulka nese kombinaci uživatel/typ oprávnění/lokalita jenž by měla být vždy jedinečná a je použita jako složený primární klíč

- ze zadání: *Nový uživatel má automaticky všechna práva na všechna města* - řeším pomocí v DB triggeru na tabulce měst - viz trigger `after_new_village_insert`

- ze zadání:  *Nové město - automaticky práva podle určených pravidel:* - opět řešeno triggerem v DB - viz `after_new_user_insert`

- ze zadání: *Derek není vůbec v tabulce `user_admin` a tím pádem nemá žádná práva* - vzhledem k mému návrhu DB by u nově vytvářeného uživatele k takovému stavu vůbec nemělo dojít. Pokud by již takový uživatel, který by v user_admin neměl žádné záznamy existoval, přistupuju k němu jako chybnému - get() na takového uživatele vrací Exception s popisem chyby.

- testování - testy jako takové jsem k tomuto zadání nepsal. Ve složce tests sem ale vytvořil ukázkový skeleton pro testování Doctrine ORM fasád, které používám. Zároveň je zde vidět nastavení prostředí pro testování - počítá se s testováním přes nette/tester

- další poznámky - některé další poznámky k řešení ke specifickému kódu jsou uvedeny v komentářích přímo v kódu. Kdyby se ale objevili nějaké nejasnosti proč jsem něco řešil tak jak jsem řešil nebo zda-li by nebyl lepší přístup, budu rád za diskuzi.

Díky za fajn zadání:)