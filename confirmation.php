<!DOCTYPE html>

<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Olivier Cochard">
	<title>Confirmation réservation Stine</title>
	<link rel="stylesheet" href="Styles/style.css">
	<link rel="icon" type="image/png" href="Imports/favicon.png">
</head>

<body>

	<div class="logo">
		<img src="Imports/stine-logo-horizontal.png" alt="stine logo"/>
	</div>

	<form>
		<h1>PRÉ-RÉSERVATION EFFECTUÉE</h1>
		<br>

		<p>Merci <?php echo "$prenom"; ?>, vos doses ont bien été bloquées. Nous allons vous contacter par téléphone dans un délai de 30 jours afin de valider votre commande.</p>
		<br>

		<label style="text-align: center;">Récapitulatif de votre commande</label>
		<div class="recapitulatif">
			<div class="caracteristique">
				<p>Variété</p>
				<?php echo "<p class='caracteristique-value'>$nom_variete, Groupe $variete</p>"; ?>
			</div>
			<div class="caracteristique-special">
				<p>Doses</p>
				<?php echo "<p class='caracteristique-value'>$nombre_doses ($surface ha)</p>"; ?>
			</div>

			<div class="caracteristique">
				<p>Tarif</p>
				<?php echo "<p class='caracteristique-value'>$prix_dose € HT/dose</p>"; ?>
			</div>

			<div class="caracteristique-special">
				<p>Total</p>
				<?php echo "<p class='caracteristique-value'>$prix_total € HT</p>"; ?>
			</div>
			
			<div class="caracteristique">
				<p>Livraison</p>
				<?php echo "<p class='caracteristique-value'>décembre - janvier</p>"; ?>
			</div>
		</div>

		<br>

		<a href="index.php" class="bouton">RETOURNER AU FORMULAIRE</a>

		<p>Sous réserve de la possibilité finale des semences après récolte et multiplication.</p>
	</form>
</body>