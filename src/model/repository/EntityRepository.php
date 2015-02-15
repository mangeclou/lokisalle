<?php

/**
 * Un repository centralise tout ce qui touche à la récupération de vos entités.
 * Concrètement donc, vous ne devez pas faire la moindre requête SQL ailleurs
 * que dans un repository, c'est la règle. 
 * Entity Repository ne connait pas "membre" ou autre, il ne connait que des
 * entités. cela doit rester générique afin que cela soit ré-utilisable.
 */

namespace repository;

require_once '/../../manager/PDOManager.php';
use Manager\PDOManager;
/**
 * Description of EntityRepository
 *
 * @author stagiaire
 */
class EntityRepository {
    private $db;
    public function __construct(){}
//-------
    public function getDb()
    {
        if(!$this->db) {
            // getInstance retourne un objet, on peux donc remettre une fléche pour appeler une méthode.
          
            $this->db = PDOManager::getInstance()->getPdo();         
        }
        return $this->db;
    }
//-------
    private function getTableName()
    {
		 // echo get_called_class() . '<br />'; // Dire à la fin : éxecuté ds findAll (fichier actuel), qui est éxecuté ds getAllEmploye (EmployeRepository), qui est éxecuté ds getRepository('Employe') (dans EmployeController), qui est éxécuté dans employeDisplay (dans EmployeController)
		  //return 'employe'; // permet de faire les tests pendant la construction avant de revenir ici plus tard pour le faire dynamiquement.
		 // echo strtolower(str_replace(array('Backoffice\\','Repository\\', 'Repository'), '', get_called_class()));
        return strtolower(str_replace(array('repository\\', 'repository'), '', get_called_class())); // je veux retirer Repository\\ et repository de Repository\EmployeRepository pour garder seulement Employe.
    }
//-------
    // permet d'aller chercher toutes les informations sur une entité - c'est à ce moment là que PDO est instancié!
    /**
     * 
     * @return boolean
     */
    public function findAll() 
    {
        // FROM le nom de la table recherchée
        $query = $this->getDb();
        $query->prepare('SELECT * FROM ' . $this->getTableName());
        $query->execute();
		// echo PDO::FETCH_PROPS_LATE;
		// echo PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE;
		// echo $this->getEntityClass();

        $resultatQuery = $query->fetchAll(\PDO::FETCH_CLASS, 'entity\\' . $this->getTableName()); // on récupère un tableau array composé d'objets. FETCH_CLASS fais une instanciation de Employe, en remplissant les propriétés qui correspondent aux noms des champs SQL. (c'est pourquoi il faut avoir la même ortographe entre les propriétés et les noms SQL). Il rempli tous meme les private et protected.
        // Pour chaque ligne SQL il instancie un objet. Donc il déclenche aussi l'autoload..
        if(!$resultatQuery) { // si la query ne fonctionne pas
            return false;
        }
        else { // sinon, on retourne les résultats
            return $resultatQuery;
        }
    }
//-------
    /**
     * Recherche dynamiquement dans la table demandée l'id demandée.
     * Retourne un array ou false.
     * @param type $id
     * @return boolean
     */
    public function findById($id)
    {
        $query = $this->getDb()->query('SELECT * FROM ' . $this->getTableName() 
                // Le premier champ est toujours le nom de la table.
                //  Caster en int permet d'éviter des erreurs de requete sql.
                . ' WHERE id' . ucfirst($this->getTableName()) . '= ' . (int) $id); 
        $resultatQuery = $query->fetchAll(\PDO::FETCH_CLASS, 'Entity\\' . $this->getTableName());

        if(!$resultatQuery) {
            return false;
        }
        else {
            // return $q->fetch(PDO::FETCH_ASSOC);
            return $resultatQuery;
        }
    }

     /**
     * Recherche dynamiquement dans la table demandée la demandée.
     * Retourne un array ou false.
     * @param type $id
     * @return boolean
     */
    public function findByTableAndColumnName($table,$columnName)
    {
        $query = $this->getDb();
        echo 'query';
        print_r($query);
        
        //TODO : faire la reque^te avec un prepare puis execute
        $query->query('SELECT * FROM ' . $table 
               //  Caster en int permet d'éviter des erreurs de requete sql.
                . " WHERE $columnName" . '= ' . (string) $columnName);
        //$query->execute();
        $resultatQuery = $query->fetchAll(\PDO::FETCH_CLASS, 'Entity\\' . $this->getTableName());

        if(!$resultatQuery) {
            return false;
        }
        else {
            // return $q->fetch(PDO::FETCH_ASSOC);
            return $resultatQuery;
        }
    }
    
    
    /**
     * Fonction qui insère dynamiquement dans la bonne table de la BDD le nouveau
     * membre à partir des données du $_POST
     * @return type
     */
    public function register()
    {
        //TODO : faire un prepare puis execute pour une meilleur sécurité
        //***************************************************************
        // (échappe automatiquement toutes les valeurs)
        // array_keys me permet de parcourrir les indices plutot que les valeur pour annoncer les champs.
        $query = $this->getDb()->query('INSERT INTO '. $this->getTableName() 
                . '(' . implode(',',array_keys($_POST)) . ') '
                . 'VALUES (' . "'" . implode("','", $_POST) . "'" . ')'); 
        return $this->getDb()->lastInsertId(); // dernier identifiant généré
    }
}