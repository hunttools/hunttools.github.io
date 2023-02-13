<?php
function registerSolution($solution) {
  $solution = mb_strtoupper(preg_replace('/\s/', '', $solution));
  $solution = preg_replace('/[^\p{L}\p{N}\s]/u', '', $solution);
  $hash = hash("sha512", $solution);
  
  $code = rand(10000000, 99999999);
  $data = array("code" => $code, "hash" => $hash);
  file_put_contents("dasoluces.txt", json_encode($data) . PHP_EOL, FILE_APPEND);
  
  return "<script>alert('Votre solution a été enregistrée avec le code de vérification suivant : " . $code . "');</script>";
}

function compareSolution($code, $solution) {
  $solution = mb_strtoupper(preg_replace('/\s/', '', $solution));
  $solution = preg_replace('/[^\p{L}\p{N}\s]/u', '', $solution);
  $hash = hash("sha512", $solution);

  $solutions = file("dasoluces.txt");
  $match = false;
  
  foreach ($solutions as $line) {
    $data = json_decode($line, true);
    if ($data["code"] == $code) {
      $match = true;
		if ($data["code"] == $code) {
		  $code_count++;
		  if ($code_count > 1) {
			return "<script>alert('Désolé, vous avez déjà enregistré votre résultat. Veuillez demander un nouveau code de vérification')</script>";
			break;
		  }
		}
		}
		}
	  
  foreach ($solutions as $line) {
    $data = json_decode($line, true);
    if ($data["code"] == $code) {
      $match = true;
      if ($data["hash"] == $hash) {
		$data = array("code" => (int)$code, "hash" => $hash);
		file_put_contents("dasoluces.txt", json_encode($data) . PHP_EOL, FILE_APPEND);
        return "<script>alert('Félicitations! Vos solutions sont identiques')</script>";
      } else {
		$data = array("code" => (int)$code, "hash" => $hash);
		file_put_contents("dasoluces.txt", json_encode($data) . PHP_EOL, FILE_APPEND);
        return "<script>alert('Désolé, vos solutions sont différentes')</script>";
      }
      break;
    }
  }
  if (!$match) {
    return "<script>alert('Aucune solution enregistrée ne correspond à ce code de vérification')</script>";
  }
}

function verifySolution($code) {
  $solutions = file("dasoluces.txt");
  $hashes = [];
  foreach ($solutions as $line) {
    $data = json_decode($line, true);
    if ($data["code"] == $code) {
      $hashes[] = $data["hash"];
    }
  }
  if (count($hashes) == 0) {
    return "<script>alert('Aucune solution enregistrée ne correspond à ce code de vérification')</script>";
  } else if (count($hashes) == 1) {
    return "<script>alert('Pas d\'autres résultats pour le moment')</script>";
  } else if (count(array_unique($hashes)) == 1) {
    return "<script>alert('Les solutions associées à ce code de vérification sont bien identiques')</script>";
  } else {
    return "<script>alert('Les solutions associées à ce code de vérification sont différentes')</script>";
  }
}

if (isset($_POST["submit"])) {
  if ($_POST["action"] == "register") {
echo registerSolution($_POST["solution"]);
} else if ($_POST["action"] == "compare") {
echo compareSolution($_POST["code"], $_POST["solution"]);
} else if ($_POST["action"] == "verify") {
echo verifySolution($_POST["verification"]);
}
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>COMPARATEUR DE SOLUTIONS</title>
    <style>
      .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 5%;
      }
	  .form-check {
    margin-bottom: 1rem;
    color: #333;
  }

  .form-control {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    text-align: center;
    background-color: #fff;
    color: #333;
  }

  .btn {
    background-color: #00D8FF;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    padding: 0.5rem;
    font-size: 1rem;
    margin-top: 1rem;
  }
</style>
  </head>
<body>
    <div class="container" align="center">
        COMPARATEUR DE SOLUTIONS
        </br></br>Vos résultats ne sont JAMAIS stockés en clair</br>Le comparateur utilise leurs versions encryptées en <a href="https://fr.wikipedia.org/wiki/SHA-2#SHA-512" target="_blank">SHA-512</a>
    </div>
    <div class="container">
        <form action="" method="post">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="action" value="register" id="register" checked onclick="document.getElementById('solution').style.display = 'block'; document.getElementById('code').style.display = 'none'; document.getElementById('verification').style.display = 'none';">
                <label class="form-check-label" for="register">Entrer ma solution et obtenir mon code de vérification</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="action" value="compare" id="compare" onclick="document.getElementById('solution').style.display = 'block'; document.getElementById('code').style.display = 'block'; document.getElementById('verification').style.display = 'none';">
                <label class="form-check-label" for="compare">Comparer ma solution avec celle d'un autre utilisateur</br>(ATTENTION : un seul essai par code de vérification)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="action" value="verify" id="verify" onclick="document.getElementById('solution').style.display = 'none'; document.getElementById('code').style.display = 'none'; document.getElementById('verification').style.display = 'block';">
                <label class="form-check-label" for="verify">Vérifier si les solutions matchent entre elles</label>
            </div>
            <div id="solution">
                <input type="text" class="form-control" name="solution" placeholder="Ma solution">
            </div>
            <div id="code" style="display: none;">
                <input type="text" class="form-control" name="code" placeholder="Code de vérification (8 chiffres)">
            </div>
            <div id="verification" style="display: none;">
                <input type="text" class="form-control" name="verification" placeholder="Code de vérification (8 chiffres)">
            </div>
            <div>
                <input type="submit" class="btn" name="submit" value="Envoyer">
            </div>
        </form>
    </div>
</body>
</html>
