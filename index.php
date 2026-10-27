<?php

require_once "connexion.php";

$erreurs = [];

// DOSE 1 et 2
$stmt = $pdo->prepare("
    SELECT id_produit, quantite
    FROM produits
    WHERE id_produit IN (1, 2)
");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$nbDose_1 = $produits[1];
$nbDose_2 = $produits[2];
$surfaceMax_1 = $nbDose_1 / 3;
$surfaceMax_2 = $nbDose_2 / 3;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"] ?? "";
    $prenom = $_POST["prenom"] ?? "";
    $telephone = $_POST["telephone"] ?? "";
    $code_postal = $_POST["code_postal"] ?? "";
    $variete = $_POST["variete"] ?? "";
    $surface = $_POST["surface"] ?? "";
    $condition = $_POST["condition"] ?? false;


    // Nom et prénom
    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $nom)) {
        $erreurs[] = "Le nom est invalide.";
    }
    if (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $prenom)) {
        $erreurs[] = "Le prénom est invalide.";
    }

    // Téléphone
    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
        $erreurs[] = "Le numéro de téléphone doit contenir exactement 10 chiffres.";
    }
    // Code postal : exactement 5 chiffres
    if (!preg_match("/^[0-9]{5}$/", $code_postal)) {
        $erreurs[] = "Le code postal doit contenir exactement 5 chiffres.";
    }

    // Variété
    if ($variete !== "1" && $variete !== "2") {
        $erreurs[] = "La variété sélectionnée est invalide.";
    }

    // Surface
    if (
        filter_var($surface, FILTER_VALIDATE_INT) === false ||
        (int)$surface < 1 ||
        (int)$surface > 40
    ) {
        $erreurs[] = "La surface doit être un nombre entier compris entre 1 et 40.";
    }
    else if ($variete == "1" && ((int)$surface * 3) > $nbDose_1){
    	$erreurs[] = "Le stock de doses est insuffisant, la quantité maximum est de $nbDose_1 doses ($surfaceMax_1 ha).";
    }
    else if ($variete == "2" && ((int)$surface * 3) > $nbDose_2){
    	$erreurs[] = "Le stock de doses est insuffisant, la quantité maximum est de $nbDose_2 doses ($surfaceMax_2 ha).";
    }

    // Condition
    if ($condition == false){
    	$erreurs[] = "Vous devez obligatoirement cocher la condition en bas de page.";
    }

    // Telephone existant
    $stmt = $pdo->prepare("
        SELECT 1
        FROM clients
        WHERE telephone = :telephone
    ");
    $stmt->execute([
        ":telephone" => $telephone,
    ]);
    if ($stmt->fetchColumn() !== false) {
        $erreurs[] = "Votre numéro de téléphone a déjà passé une commande.";
    }


    if (empty($erreurs)) {

    	$nom_variete = "07P32";
		if ($variete == "2") $nom_variete = "11N20";
        $id_produit = (int)$variete;

        $nombre_doses = (int)$surface * 3;

        if ((int)$surface < 20) {
            $prix_dose = 70;
        } else {
            $prix_dose = 65;
        }

        $prix_total = $nombre_doses * $prix_dose;



        try {
            $pdo->beginTransaction();

            // AJOUT CLIENT
            $stmt = $pdo->prepare("
                INSERT INTO clients
                (nom, prenom, telephone, code_postal)
                VALUES
                (:nom, :prenom, :telephone, :code_postal)
            ");

            $stmt->execute([
                ":nom" => $nom,
                ":prenom" => $prenom,
                ":telephone" => $telephone,
                ":code_postal" => $code_postal,
            ]);

            $id_client = $pdo->lastInsertId();


            // AJOUT RESERVATION
            $stmt = $pdo->prepare("
                INSERT INTO reservations
                (id_client, id_produit, doses, prix_total)
                VALUES
                (:id_client, :id_produit, :doses, :prix_total)
            ");

            $stmt->execute([
                ":id_client" => $id_client,
                ":id_produit" => $id_produit,
                ":doses" => $nombre_doses,
                ":prix_total" => $prix_total,
            ]);


            // ENLEVER PRODUIT
            $stmt = $pdo->prepare("
                UPDATE produits
                SET quantite = quantite - :doses
                WHERE id_produit = :id_produit
            ");

            $stmt->execute([
                ":doses" => $nombre_doses,
                ":id_produit" => $id_produit
            ]);


            // Tout s'est bien passé
            $pdo->commit();

        } catch (PDOException $e) {

            // Annule toutes les opérations précédentes
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erreurs[] = "Une erreur est survenue lors de l'enregistrement de votre réservation.";
        }
    }
}


//AFFICHAGES DES PAGES
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($erreurs)) {

    // Formulaire valide
    include "confirmation.php";

} else {

    // Première visite OU formulaire invalide
    include "formulaire.php";

}

include "footer.php";

?>