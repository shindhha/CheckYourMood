<?php header( 'Cache-Control: max-age=900' );?>
<?php include("session.php"); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta HTTP-EQUIV="Pragma" content="no-cache">
	<meta HTTP-EQUIV="Expires" content="-1">
    <title>Check Your Mood - Humeurs</title>
    <link rel="stylesheet" href="/check-your-mood/css/visualisation.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="/check-your-mood/css/header.css">
	<!-- Police Font Awesome pour les icônes -->
	<link href="fontawesome-free-6.2.1-web/css/all.css" rel="stylesheet">
	<link rel="icon" href="/check-your-mood/images/YeuxLogo.png">


    <?php
    $ChoixTypeDeRepresentation = ['Choissez votre représentation','Jour','Semaine','Année']
    ?>

	<!-- Création du srcipt js pour le digramme de comparaison d'humeur par année-->
    <script>
        window.onload = function () {

        var chart = new CanvasJS.Chart("chartconteneur", {
            animationEnabled: true,
            theme: "light2",
            title:{
                text: "Diagramme de comparaison par année"
            },
            axisY:{
                includeZero: true
            },
            legend:{
                cursor: "pointer",
                verticalAlign: "center",
                horizontalAlign: "right",
                itemclick: toggleDataSeries
            },
            data: [{
                type: "column",
                name: <?php echo '"'.$anneeComparaison.'"';?>,
                indexLabel: "{y}",
                showInLegend: true,
                dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); var_dump($dataPoints2);?>
            },{
                type: "column",
                name: <?php echo '"'.$anneeChoisi.'"';?>,
                indexLabel: "{y}",
                showInLegend: true,
                dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();

        function toggleDataSeries(e){
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            }
            else{
                e.dataSeries.visible = true;
            }
            chart.render();
            }
        }
    </script>


</head>
<body>
	<?php include("header.php"); ?>
<script>	 
	let popupIsActive = false;
	
    function openPopupContexte(id) {
		popupIsActive = true; 
		document.getElementById("popupContexte" + id).style.display = "block";
		document.querySelector(".table-mood").classList.add("flou");
	}

	function closePopupContexte(id) {
		document.getElementById("popupContexte" + id).style.display = "none";
		document.querySelector(".table-mood").classList.remove("flou");

		

	}
</script>

<?php
spl_autoload_extensions(".php");
spl_autoload_register();

$day = [2 => 'Lundi',3 => 'Mardi',4 => 'Mercredi',5 => 'Jeudi',6 => 'Vendredi',7 => 'Samedi',1 => 'Dimanche'];

use yasmf\HttpHelper;
?>
    <?php
    if(!isset($_POST['premiereCo'])){
        ?>
        <div class="conteneur-main">
            <div class="conteneurVisu">
                <div class="loginVisu left col-md-6 col-sm-12 hidden-xs">
                    <img class="logo" src="/check-your-mood/images/visualisation.png" alt="Logo Check Your Mood">
                </div>
                <div class="loginVisu col-md-6 col-sm-12">
                    <div class="row">
                        <span class="login-textVisu">Prêt à visualiser vos données ?</span>
                    </div>
                    <div class="row-btn">
                        <form action="index.php" method="post">
                            <input type="hidden" name="premiereCo" value="1">
                            <input type="hidden" name="controller" value="donnees">
                            <input type="hidden" name="action" value="goToMood">
                            <input type="hidden" name="namepage" value="visualisation">

                            <!-- Choisir l'année dont on souhaite visualiser les données-->

                                <div class="btn-connect">
                                    <select name="anneeChoisi" class = "btnVisu" >
                                        <?php
                                        for($nbr = 2021; $nbr <= $anneeActuelle; $nbr ++){
                                            if(isset($_POST['anneeChoisi'])){
                                                if($_POST['anneeChoisi'] == $nbr){
                                                    ?>
                                                    <option class="form-control" value="<?php echo $_POST['anneeChoisi'];?>" selected><?php echo $_POST['anneeChoisi'];?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option class="form-control" value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
                                                    <?php
                                                }
                                            } else {
                                                if($anneeActuelle == $nbr){
                                                    ?>
                                                    <option class="form-control" value="<?php echo $anneeActuelle;?>" selected><?php echo $anneeActuelle ;?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option class="form-control" value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>


                                <br>
                                <!-- Choisir si on veut afficher les données par jur semaine mois ou année dont on souhaite visualiser les données-->
                                <div class="btn-connect">
                                    <select name="typeDeRpresentation" class = "btnVisu ">
                                        <?php
                                        for($nbr = 1; $nbr <= 3; $nbr ++){
                                            if(isset($_POST['typeDeRpresentation'])){
                                                if($_POST['typeDeRpresentation'] == $nbr){
                                                    ?>
                                                    <option value="<?php echo $_POST['typeDeRpresentation'];?>" selected><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?php echo $nbr;?>"><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <option value="<?php echo $nbr;?>"><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            <br>
                            <input type="submit" value ="Valider" class = "btnVisu">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {


		?>
		<div class="container-fluid page">
			<div class="row visu">
				<div class="col-md-12 container-fluid sansCadre">
					<form action="index.php" method="post">
						<input type="hidden" name="premiereCo" value="1">
						<input type="hidden" name="controller" value="donnees">
						<?php
						if(isset($humeurRadar)){
							?>
							<input type="hidden" name="humeur" value="<?php echo $humeurRadar; ?>">
							<?php
						}
						?>
						<input type="hidden" name="anneeAComparer" value="<?php echo $anneeComparaison; ?>">
						<input type="hidden" name="action" value="goToMood">
						<input type="hidden" name="namepage" value="visualisation">

						<?php
						if($_POST['typeDeRpresentation'] != 1 ){
							?>
							<div class="col-md-3  sansCadre">
								<select name="anneeChoisi" class = "btnVisu2" >
									<?php
									for($nbr = 2021; $nbr <= $anneeActuelle; $nbr ++){
										if(isset($_POST['anneeChoisi'])){
											if($_POST['anneeChoisi'] == $nbr){
												?>
												<option value="<?php echo $_POST['anneeChoisi'];?>" selected><?php echo $_POST['anneeChoisi'];?></option>
												<?php
											} else {
												?>
												<option value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
												<?php
											}
										} else {
											if($anneeActuelle == $nbr){
												?>
												<option value="<?php echo $anneeActuelle;?>" selected><?php echo $anneeActuelle ;?></option>
												<?php
											} else {
												?>
												<option value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
												<?php
											}
										}
									}
									?>
								</select>
							</div>
							<?php
							}
						?>
						<div class="<?php echo $typeDeRpresentation == 1 ? "col-md-4 sansCadre": "col-md-3 sansCadre"; ?> ">
							<select name="typeDeRpresentation" class = "btnVisu2">
								<?php
								for($nbr = 1; $nbr <= 3; $nbr ++){
									if(isset($_POST['typeDeRpresentation'])){
										if($_POST['typeDeRpresentation'] == $nbr){
											?>
											<option value="<?php echo $_POST['typeDeRpresentation'];?>" selected><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
											<?php
										} else {
											?>
											<option value="<?php echo $nbr;?>"><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
											<?php
										}
									} else {
										?>
										<option value="<?php echo $nbr;?>"><?php echo $ChoixTypeDeRepresentation[$nbr];?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<?php
						if($_POST['typeDeRpresentation'] == 2){
							?>
							<div class="<?php echo $typeDeRpresentation == 1 ? "col-md-4 sansCadre": "col-md-3 sansCadre"; ?>">
								<select name="weekGeneral" class = "btnVisu2">
									<?php
									if($anneeActuelle == $anneeChoisi){
										for($i = 1; $i <= $currentWeek ; $i++){
											if(isset($_POST['weekGeneral'])){
												if($_POST['weekGeneral'] == $i){
													$weekGeneral = $_POST['weekGeneral'];
													echo "<option value = '".$_POST['weekGeneral']."' selected >Semaine ".$_POST['weekGeneral']."</option>";
												} else {
													echo "<option value = '".$i."' >Semaine ".$i."</option>";
												}
											} else {
												if($i == $currentWeek){
													$weekGeneral = $i;
													echo "<option value = '".$i."' selected >Semaine ".$i."</option>";
												} else {
													echo "<option value = '".$i."' >Semaine ".$i."</option>";
												}
											}
										}
									} else {
										for($i = 1; $i <= 52 ; $i++){
											if($_POST['weekGeneral'] == $i){
												$weekGeneral = $_POST['weekGeneral'];
												echo "<option value = '".$_POST['weekGeneral']."' selected >Semaine ".$_POST['weekGeneral']."</option>";
											} else {
												echo "<option value = '".$i."' >Semaine ".$i."</option>";
											}
										}
									}

									?>
								</select>
							</div>
							<?php
						}
						?>
						<?php
						if($_POST['typeDeRpresentation'] == 1){
							?>
							<div class="<?php echo $typeDeRpresentation == 1 ? "col-md-4 sansCadre": "col-md-3 sansCadre"; ?>">
								<!-- Choisir la semaine dont on souhaite visualiser les données pour le graph Radar-->
								<input class = "btnVisu2" type="date" name="dateChoisiDonught" value="<?php echo $dateDonught; ?>" min="2021-01-01" max="<?php echo $anneeChoisi == $anneeActuelle ? $currentDay : $anneeChoisi + "-12-31";?>">
							</div>
							<?php
						}
						?>
						<div class="<?php echo $typeDeRpresentation == 1 ? "col-md-4 sansCadre": "col-md-3 sansCadre"; ?>">
							<input type="submit" value ="Valider" class = "btnVisu2">
						</div>
					</form>
				</div>
			</div>
			<br>
			<?php
			if($typeDeRpresentation == 2){
				?>
			<div class = "rowVisualisation">
				<div class = "col-md-6">
					<div class="radarPlus">
						<!-- partie du formulaire pour faire le graphe radar -->
						<form action="index.php" method="post">
							<input type="hidden" name="anneeChoisi" value="<?php echo $anneeChoisi; ?>">
							<input type="hidden" name="premiereCo" value="1">
							<input type="hidden" name="typeDeRpresentation" value="<?php echo $typeDeRpresentation; ?>">
							<input type="hidden" name="weekGeneral" value="<?php echo $weekGeneral; ?>">
							<input type="hidden" name="controller" value="donnees">
							<input type="hidden" name="action" value="goToMood">
							<input type="hidden" name="namepage" value="visualisation">
							<!-- Formulaire pour le graphe radar -->
							<select id="humeur" name="humeur" class="selectAutoWidth selectFormGraph">
							<?php
								/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
								/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
								/* mettre un selected qui fonctionne parce que sah j y arrive pas et j ai pas envi */
								while($row = $libellesRadar->fetch()){
									if(isset($_POST['humeur']) && $_POST['humeur'] == $row['codeLibelle']){
										$humeurRadar = $_POST['humeur'];
										echo "<option value = '".$row['codeLibelle']."' selected >".$row['libelleHumeur']." ".$row['emoji']."</option>";
									}else{
										$humeurRadar = isset($humeurRadar) ? $humeurRadar: $row['codeLibelle'];
									echo "<option value = '".$row['codeLibelle']."'>".$row['libelleHumeur']." ".$row['emoji']."</option>";
									}
								}
							?>
							</select>
							
							<button type="submit" class="btn-ajout"><span>OK</span></button>
						</form>
						<!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  -->
						<!-- fin de la partie du formulaire pour le graphe radar  -->
						<!-- affichage du graphe radar -->
						<br>
						<div class="grapheConteneur">
							<canvas id="myChart" ></canvas>
						</div>
					</div>
				</div>
				<div class = "col-md-6">
					<div class="table-responsive card" >
						<!-- partie affichage du tableau des humeurs -->
						<table class="table-mood rounded-scrollbar table-striped table ">
							<thead>
								<th>
									Jour
								</th>
								<th>
									Emoji
								</th>
								<th>
									Libelle
								</th>
								<th>
									Heure
								</th>
								<th class="last-column">
									Contexte
								</th>
							</thead>

							<?php
								$ancienneDate = NULL;
								$i = 0; 
								while($row = $visualisationTableau->fetch()){
									echo "<tr>";
									if ($row['jourDeLaSemaine'] == $ancienneDate) {
										echo "<td> </td>";
									} else {
										echo "<td>".$day[$row['jourDeLaSemaine']]."</td>";
									}
									echo "<td>".$row['emoji']."</td>";
									echo "<td>".$row['libelle']."</td>";
									echo "<td>".$row['heure']."</td>";
									echo "<td class = 'last-column'><button onclick=\"openPopupContexte(".$i.")\"  class=\"form-control btn btn-info boutonEye\"/><span
									class='fas fa-solid fa-eye'></button></td>";
									echo "</tr>";
									$ancienneDate = $row['jourDeLaSemaine'];
									?>
									<?php
									echo '<fieldset id="popupContexte'.$i.'" class="containerPopup">';
										echo '<legend class="title text-center">Contexte de l\'humeur</legend><br/>';
										echo '<div class="contextePopup">'. $row["contexte"] . '</div>';

										echo '<div class="btnNav">';
											echo '<button type="button" class="annulerPopup" onclick="closePopupContexte('.$i.')">Fermer</button>';
										echo '</div>';
									echo '</fieldset>';
									$i++; 
								}

							?>
						</table>
					</div>
				</div>
			</div>
			
			<div class = "row">
				<div class = "col-md-12 humeurPlusFrequente borderTop">
					<?php
					while($row = $humeursLaPlusFrequente->fetch()){
						?>
						<div class="container emojiFrequent">
							<div class="texteGrossi">
							<span>Voici l'humeur qui est revenu le plus cette semaine :</span>
							</div>
							<br>
							<span class="emojie emojiGrossi"><?php echo $row['emoji'] ; ?></span>
							<br>
							<div class="emojiFrequentLibelle">
								<span><?php echo $row['libelle']; ?></span>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
			}
			echo '<script type="text/javascript">';
			echo "var dataHumeur = '".implode(",", $visualisationRadar)."'.split(',');";
			echo 'console.table(dataHumeur);';
			echo '</script>';
		?>




			<!-- Affichage de du diagramme batton qui permet de visualiser un humeur en fonction des mois et années-->
			<?php
			if($typeDeRpresentation == 3){
				?>
			<!-- carré du bas à droite -->
					
					<div class="col-md-6">

							<span>Choisissez l'année à comparer</span>
							<form action="index.php" method="post">
							<?php
								if($typeDeRpresentation == 2){
									?>
									<input type="hidden" name="humeur" value="<?php echo $humeurRadar; ?>">
									<?php
								}
								?>
								<input type="hidden" name="anneeChoisi" value="<?php echo $anneeChoisi; ?>">
								<input type="hidden" name="typeDeRpresentation" value="<?php echo $typeDeRpresentation; ?>">
								<input type="hidden" name="premiereCo" value="1">
								<input type="hidden" name="controller" value="donnees">
								<input type="hidden" name="action" value="goToMood">
								<input type="hidden" name="namepage" value="visualisation">
								<select name="anneeAComparer"  class="selectAutoWidth selectFormGraph">
									<?php
									for($nbr = 2021; $nbr <= $anneeActuelle; $nbr ++){
										if(isset($_POST['anneeAComparer'])){
											if($_POST['anneeAComparer'] == $nbr){
												?>
												<option value="<?php echo $_POST['anneeAComparer'];?>" selected><?php echo $_POST['anneeAComparer'];?></option>
												<?php
											} else {
												?>
												<option value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
												<?php
											}
										} else {
											if($anneeChoisi == $nbr){
												?>
												<option value="<?php echo $anneeChoisi;?>" selected><?php echo $anneeChoisi ;?></option>
												<?php
											} else {
												?>
												<option value="<?php echo $nbr;?>"><?php echo $nbr;?></option>
												<?php
											}
										}
									}
									?>
								</select> 

								<select class="selectAutoWidth selectFormGraph" id="humeurDigrammeBatton" name="humeurDigrammeBatton">
								<?php
									while($row = $libellesRadar->fetch()){
										if(isset($_POST['humeurDigrammeBatton']) && $_POST['humeurDigrammeBatton'] == $row['codeLibelle']){
											$humeurDigrammeBatton = $_POST['humeurDigrammeBatton'];
											echo "<option value = '".$row['codeLibelle']."' selected >".$row['libelleHumeur']." ".$row['emoji']."</option>";
										}else{
											$humeurDigrammeBatton = isset($humeurDigrammeBatton) ? $humeurDigrammeBatton: $row['codeLibelle'];
										echo "<option value = '".$row['codeLibelle']."'>".$row['libelleHumeur']." ".$row['emoji']."</option>";
										}
									}
								?>
								</select>
								<button type="submit" class="btn-ajout"><span class="text">OK</span></button>
							</form>
						<div id="chartconteneur"></div>
					</div>

					<div class = "col-md-6">
						<?php
						while($row = $humeursLaPlusFrequenteAnnee->fetch()){
						?>
						<div class="emojiFrequent">
							<div class="texteGrossi">
								<span>Voici l'humeur la plus récurrente cette année : </span>
							</div>
							<br>
							<span class="emojie emojiGrossi"><?php echo $row['emoji']; ?></span>
							<br>
							<div class="emojiFrequentLibelle">
								<span><?php echo $row['libelle']; ?></span>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				<?php
			}
			if($typeDeRpresentation == 1){
				// Test s'il y a une humeur à ce jour
				if ($tableauCountDonught[0] == 0) {
					// Il n'y a pas d'humeur à ce jour
					echo '<div class="col-md-12">';
					echo '<div class="container containCadre">';
					echo '<span class="texteGrossi">Aïe, vous n\'avez pas d\'humeur à ce jour 😅</span>';
					echo '</div>';
					echo '</div>';

					
				} else {
					// Il y a au moins une humeur à ce jour
				?>
					<div class="col-md-6">
						<div class="donutGraph">
							<?php
							echo '<script type="text/javascript">';
							echo "var dataCountDonught = '".implode(",", $tableauCountDonught)."'.split(',');";
							echo "var dataLibelleDonught = '".implode(",", $tableauLibelleDonught)."'.split(',');";
							echo 'console.table(dataHumeur);';
							echo '</script>';
							?>
							
							<div class="grapheConteneur">
								<canvas id="myChart2" class="containGraph"></canvas>
							</div>
						</div>
						
					</div>
				
					<div class = "col-md-6">
						<?php
						while($row = $humeursLaPlusFrequenteJour->fetch()){
							?>
							<div class=" emojiFrequent">
								<div class="texteGrossi">
									<span class>Voici l'humeur qui est revenu le plus ce jour-ci :</span>
								</div>
								<br>
								<span class="emojie emojiGrossi"><?php echo $row['emoji']; ?></span>
								<br>
								<div class="emojiFrequentLibelle">
									<span><?php echo $row['libelle']; ?></span>
								</div>
							</div>
						<?php
						}
						?>
					</div>
				
				<?php
				}
			}
			?>
		</div>
	</div>
	<button onclick="scrollToBottom()" id="top-button" title="Go to bottom"><i class="fa fa-arrow-down"></i></button>
	<?php
	}
	?>
	<script>
		window.onscroll = function() {scrollFunction()};

		function scrollFunction() {

			document.getElementById("top-button").style.display = "block";

			if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
				document.getElementById("top-button").style.display = "none";
			}
		}


		function scrollToBottom() {
			window.scrollTo({
				top: document.body.scrollHeight,
				behavior: "smooth"
			});
		}
	</script>



        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="script/script.js"></script>
        <script src="script/scriptjs2.js"></script>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    </body>
</html>