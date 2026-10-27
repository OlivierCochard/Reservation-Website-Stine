<!DOCTYPE html>

<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Olivier Cochard">
	<title>Formulaire réservation Stine</title>
	<link rel="stylesheet" href="Styles/style.css">
	<link rel="icon" type="image/png" href="Imports/favicon.png">
</head>

<body>

	<div class="logo">
		<img src="Imports/stine-logo-horizontal.png" alt="stine logo"/>
	</div>

	<form method="post" action="">
		<h1>PRÉ-RÉSERVEZ VOTRE SOJA STINE</h1>
		<br>

		<div class="form-row">
		    <div>
		        <label for="nom">Nom</label>
		        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
		    </div>
		    <div>
		        <label for="prenom">Prénom</label>
		        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom ?? '') ?>"required>
		    </div>
		</div>

		<div class="form-row">
			<div>
				<label for="telephone">Numéro de téléphone</label>
				<input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" maxlength="10" value="<?= htmlspecialchars($telephone ?? '') ?>" required>
			</div>
			<div>
				<label for="code_postal">Code postal</label>
				<input type="text" id="code_postal" name="code_postal" pattern="[0-9]{5}" maxlength="5" value="<?= htmlspecialchars($code_postal ?? '') ?>" required>
			</div>
		</div>

		<p>Vos doses seront bloquées pendant 30 jours. Un conseiller vous appellera pour confirmer votre commande.</p>

		<br>
		<br>

		<label style="text-align: center;">Choisissez votre variété de soja (non OGM)</label>
		<div class="form-row">
			<label class="variete">
				<input type="radio" name="variete" value="1" required checked>
				<div class="variete-contenu">
					<p class="variete-nom">07P32</p>
					<p class="variete-groupe">Groupe 0</p>
					<p class="variete-quantite" id="quantite-1">Quantite</p>
				</div>
			</label>
			<label class="variete">
				<input type="radio" name="variete" value="2" required>
				<div class="variete-contenu">
					<p class="variete-nom">11N20</p>
					<p class="variete-groupe">Groupe 1</p>
					<p class="variete-quantite" id="quantite-2">Quantite</p>
				</div>
			</label>
		</div>

		<div class="form-row-2">
			<div>
				<label for="surface">Surface (ha)</label>
				<input type="number" id="surface" name="surface" min="0" step="1" value="0" required>
			</div>
			<div>
			    <label for="nombre_doses">Nombre doses</label>
			    <input type="number" id="nombre_doses" value="0" readonly>
			</div>
			<div>
			    <label for="prix_dose">Prix HT/Doses</label>
			    <input type="text" id="prix_dose" value="-" readonly>
			</div>
			<div>
			    <label for="prix_total">Prix HT Total</label>
			    <input type="text" id="prix_total" value="-" readonly>
			</div>
		</div>
		<p>Prix de base 70€ HT/doses, remisé a 65€ HT/doses à partir de 20 hectares de semence commandé. Chaque agriculteurs est limité à 40ha chacun.</p>

		<br>

		<?php if (isset($erreurs) && !empty($erreurs)): ?>
	        <div class="erreurs">
	            <strong>Veuillez corriger les erreurs suivantes :</strong>
	            <ul>
	                <?php foreach ($erreurs as $erreur): ?>
	                    <li><?= htmlspecialchars($erreur) ?></li>
	                <?php endforeach; ?>
	            </ul>
	        </div>
	    <?php endif; ?>

		<p class="checkbox"> <input type="checkbox" name="condition" value="acceptee" required>  J'accepte les conditions de pré-réservation et d'être recontacté par <strong>DV and Co</strong>, distributeur Stine. <a href="conditions.php">Appuyez ici pour les consulter.</a></p>

		<button type="submit">PRÉ-RÉSERVER MES DOSES</button>

		<p>Sous réserve de la possibilité finale des semences après récolte et multiplication.</p>

		<script>
			//Nombre Doses Disponibles
			let nbDose_1 = <?php echo "$nbDose_1"; ?>;
			let nbDose_2 = <?php echo "$nbDose_2"; ?>;
			let nbSurface_1 = <?php echo "$surfaceMax_1"; ?>;
			let nbSurface_2 = <?php echo "$surfaceMax_2"; ?>;

			const textNbDose_1 = document.getElementById("quantite-1");
			const textNbDose_2 = document.getElementById("quantite-2");
			textNbDose_1.textContent = "Plus que " + nbDose_1 + " doses";
			textNbDose_2.textContent = "Plus que " + nbDose_2 + " doses";


			//Input Variete
			let doseMax = nbDose_1;
			const varietes = document.querySelectorAll('input[name="variete"]');
			varietes.forEach(function(variete) {
			    variete.addEventListener("change", function() {
			    	if (parseInt(this.value) == parseInt(1)) doseMax = nbDose_1;
			    	else doseMax = nbDose_2;
			    	UpdateSurface();
			    });
			});


			//Input Surface
			const surface = document.getElementById("surface");
			const nombre_doses = document.getElementById("nombre_doses");
			const prix_dose = document.getElementById("prix_dose");
			const prix_total = document.getElementById("prix_total");
			surface.addEventListener("input", UpdateSurface);

			function UpdateSurface() {
				const ha = parseInt(surface.value);

				//Securites
				if (ha < 0) {
					surface.value = 0;
					UpdateSurface();
					return;
				}
				if (ha > 40) {
					surface.value = 40;
					UpdateSurface();
					return;
				}
				if (ha > (doseMax/3)){
					surface.value = doseMax/3;
					UpdateSurface();
					return;
				}
				if ((ha/3) > doseMax){
					surface.value = doseMax;
					UpdateSurface();
					return;
				}

				//Pas de surface
				if (ha == 0){
					nombre_doses.value = 0;
					prix_dose.value = "-";
					prix_total.value = "-";
					return;
				}

				//Nombre doses
			    nombre_doses.value = ha * 3;
			    //Prix dose
			    if (ha < 20){
			    	prix_dose.value = parseInt(70);
			    } else {
			    	prix_dose.value = parseInt(65);
			    }
			    prix_dose.value += "€";
			    //Prix total
			    if (ha < 20){
			    	prix_total.value = parseInt(70 * nombre_doses.value);
			    } else {
			    	prix_total.value = parseInt(65 * nombre_doses.value);
			    }
			    prix_total.value += "€";
			}
		</script>

	</form>
	


</body>
