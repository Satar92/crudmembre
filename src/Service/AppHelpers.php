<?php 

namespace App\Service;
use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class AppHelpers 
{
    private $db;
    private $security;
    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->db = $doctrine->getManager();
        $this->security = $security;
    }

    public function getUser()
    {
      $user = $this->security->getUser();
      if($user){
        $isLoggedIn = true;
      } else {
        $isLoggedIn = false;
      }


      if($this->security->isGranted('ROLE_ADMIN')) {
        $isAdmin = true;
      } else {
        $isAdmin = false;
      }

      $userObj = new \stdClass();
      $userObj->user = $user;
      $userObj->isLoggedIn = $isLoggedIn;
      $userObj->isAdmin = $isAdmin;

      return $userObj;
    }


    public function installBdd(): void 
    {
        // récuperation des catégories dans la table Catégorie
        $categories = $this->db->getRepository(Categorie::class)->findAll();
        // Si pas de cat trouvée en BDD on installe celles 
        // du tableau retourné par getCategoryList()
        if(!count($categories)){
            $this->installCategories();
        }

        $produit = $this->db->getRepository(Produit::class)->findAll();

         if(!count($produit)) {
            $this->InstallProduit();
         }
    }

    private function InstallProduit(): void 
    {
        $listeProduits = $this->getProductList();
        foreach($listeProduits as $lp){
            $produit = new Produit;
            $categorie = $this->db->getRepository(Categorie::class)->findBy(["nom" => $lp['cat']]);

            $produit->setReference($lp['reference']);
            $produit->setCategorie($categorie[0]);
            $produit->setTitre($lp['titre']);
            $produit->setDescription($lp['description']);
            $produit->setCouleur($lp['couleur']);
            $produit->setTaille($lp['taille']);
            $produit->setSexe($lp['sexe']);
            $produit->setPhoto($lp['photo']);
            $produit->setPrix($lp['prix']);
            $produit->setStock($lp['stock']);
            $this->db->persist($produit);
        }
        $this->db->flush();
    }

    private function installCategories(): void 
    {
        $catList = $this->getCategoryList();
        foreach($catList as $cat) {
            $category = new Categorie();
            $category->setNom($cat['nom']);
            $this->db->persist($category);
        }
        $this->db->flush();
    }

    private function getCategoryList(): array 
    {
        return [
            [
                "nom" => "t-shirt",
            ],
            [
                "nom" => "chemise",
            ],
            [
                "nom" => "pull",
            ],
        ];
    }

    private function getProductList(): array
  {
    return [
      [
        "reference" => "11-d-23",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col V",
        "description" => "Tee-shirt en coton flammé liseré contrastant",
        "couleur" => "bleu",
        "taille" => "M",
        "sexe" => "m",
        "photo" => "100_bleu.jpg",
        "prix" => 20,
        "stock" => 53,
      ],

      [
        "reference" => "66-f-15",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col V rouge",
        "description" => "c'est vraiment un super tshirt en soir&eacute;e !",
        "couleur" => "rouge",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "102_rouge.png",
        "prix" => 15,
        "stock" => 230,
      ],

      [
        "reference" => "88-g-77",
        "cat" => "t-shirt",
        "titre" => "Tshirt Col rond vert",
        "description" => "Il vous faut ce tshirt Made In France !!!",
        "couleur" => "vert",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "103_vert.png",
        "prix" => 29,
        "stock" => 63,
      ],

      [
        "reference" => "55-b-38",
        "cat" => "t-shirt",
        "titre" => "Tshirt jaune",
        "description" => "e jaune reviens &agrave; la mode, non? :-)",
        "couleur" => "jaune",
        "taille" => "S",
        "sexe" => "m",
        "photo" => "101_jaune.png",
        "prix" => 20,
        "stock" => 3,
      ],

      [
        "reference" => "31-p-33",
        "cat" => "t-shirt",
        "titre" => "Tshirt noir original",
        "description" => "voici un tshirt noir tr&egrave;s original :p",
        "couleur" => "noir",
        "taille" => "XL",
        "sexe" => "m",
        "photo" => "2332_full_t-shirt.jpg",
        "prix" => 25,
        "stock" => 80,
      ],

      [
        "reference" => "56-a-65",
        "cat" => "chemise",
        "titre" => "Chemise Blanche",
        "description" => "Les chemises c'est bien mieux que les tshirts",
        "couleur" => "blanc",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "105_chemiseblanchem.jpg",
        "prix" => 49,
        "stock" => 73,
      ],

      [
        "reference" => "63-s-63",
        "cat" => "chemise",
        "titre" => "Chemise Noir",
        "description" => "Comme vous pouvez le voir c'est une chemise noir...",
        "couleur" => "blanc",
        "taille" => "M",
        "sexe" => "m",
        "photo" => "106_chemisenoirm.jpg",
        "prix" => 59,
        "stock" => 120,
      ],

      [
        "reference" => "77-p-79",
        "cat" => "pull",
        "titre" => "Pull gris",
        "description" => "Pull gris pour l'hiver",
        "couleur" => "gris",
        "taille" => "XL",
        "sexe" => "f",
        "photo" => "104_pullgrism2.jpg",
        "prix" => 79,
        "stock" => 99,
      ],

      [
        "reference" => "15-kjf-85",
        "cat" => "t-shirt",
        "titre" => "T-shirt homme à personnaliser",
        "description" => "Superbe tshirt noir personnalisable!",
        "couleur" => "noir",
        "taille" => "L",
        "sexe" => "m",
        "photo" => "1648068059_tshirt_noir_l.webp",
        "prix" => 16,
        "stock" => 15,
      ],
    ];
  }

}