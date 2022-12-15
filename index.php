<!DOCTYPE html>
<html lang="PL">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <title>Książki</title>
</head>
<body>
  <?php 
    include 'database.php';

    $statement = $conn->prepare('SELECT * from ksiazki ORDER BY data_dodania DESC LIMIT 10');
    $statement->execute();

    $result = $statement->fetchAll();
  ?>

  <?php 
    $title=$releaseDate=$additionDate=$authorNameSurname=$isbn="";
    $titleError=$releaseDateError=$authorNameSurnameError=$isbnError="";
    $isBorrowed=false;
    $isFormValid = true;
    $isFormSubmitted = false;

    function validateISBN($isbn){
      return strlen(strval($isbn)) == 10 || strlen(strval($isbn)) == 13;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
      $isFormSubmitted = true;
      if (empty($_POST["title"])) {
        $titleError = "Tytuł jest wymagane";
        $isFormValid = false;
      } else {
        $title = $_POST["title"];
      }

      if (empty($_POST["release_date"])) {
        $releaseDateError = "Data wydania jest wymagana";
        $isFormValid = false;
      } else {
        $releaseDate = $_POST["release_date"];
      }

      if (empty($_POST["authors_name_surname"])) {
        $authorNameSurnameError = "Imie i nazwisko autora są wymagane";
        $isFormValid = false;
      } else {
        $authorNameSurname = $_POST["authors_name_surname"];
      }

      if (empty($_POST["isbn"])) {
        $isbnError = "ISBN jest wymagany";
        $isFormValid = false;
      } else {
        if(validateISBN($_POST["isbn"])){
          $isbn = $_POST["isbn"];
        }else{
          $isFormValid = false;
          $isbnError = "ISBN jest nieprawidłowy";
        }
      }

      if(empty($_POST["borrowed"])){
        $isBorrowed = false;
      }else{
        $isBorrowed = true;
      }
    }
  ?>
  <header class="header">
    <div class="logo">
      <img src="logo.png" alt="LOGO" class="logo"/>
    </div>
    <ul class="menu">
      <li><a href="https://www.google.com/" class="link">LINK1</a></li>
      <li><a href="https://www.google.com/" class="link">LINK2</a></li>
      <li><a href="https://www.google.com/" class="link">LINK3</a></li>
    </ul>
  </header>
  <div class="books">
      <?php foreach($result as $book): ?>
            <p>"<?php echo $book["tytul"]?>" - <?php echo $book["imie_nazwisko_autora"]?></p>
      <?php endforeach;?>
  </div>
  <form method="post" class="form">
      <label for="title">Tytuł</label>
      <input type="text" name="title" id="title" required maxlength="255"/>
      <?php if($titleError): ?>
        <p class="error"><?php echo $titleError?></p>
      <?php endif;?>

      <label for="release_date">Data Wydania</label>
      <input type="date" name="release_date" id="release_date" required/>
      <?php if($releaseDateError): ?>
        <p class="error"><?php echo $releaseDateError?></p>
      <?php endif;?>

      <label for="addition_date">Data Dodania</label>
      <input type="date" name="addition_date" id="addition_date"/>

      <label for="authors_name_surname">Imie i nazwisko autora</label>
      <input type="text" name="authors_name_surname" id="authors_name_surname" maxlength="255" required/>
      <?php if($authorNameSurnameError): ?>
        <p class="error"><?php echo $authorNameSurnameError?></p>
      <?php endif;?>

      <label for="isbn">ISBN</label>
      <input type="number" name="isbn" id="isbn" required/>
      <?php if($isbnError): ?>
        <p class="error"><?php echo $isbnError?></p>
      <?php endif;?>

      <label for="borrowed">Czy wypożyczona</label>
      <input type="checkbox" name="borrowed" id="borrowed">
      <button type="submit">Dodaj</button>
      <?php 
        if($isFormValid && $isFormSubmitted){
           try{
              $sth = $conn->prepare('
              INSERT INTO ksiazki (tytul,data_wydania,data_dodania,imie_nazwisko_autora,isbn,wypozyczone) VALUES
              (:tytul,:data_wydania,:data_dodania,:imie_nazwisko_autora,:isbn,:wypozyczone)'
              );
              $sth->bindValue(":tytul",$title);
              $sth->bindValue(":data_wydania",$releaseDate);
              $sth->bindValue(":data_dodania",$additionDate ? $additionDate : date("Y-m-d"));
              $sth->bindValue(":imie_nazwisko_autora",$authorNameSurname);
              $sth->bindValue(":isbn",$isbn);
              $sth->bindValue(":wypozyczone", $isBorrowed ? $isBorrowed : false);
              $sth->execute();
              echo '<h2 class="success">Dodano książkę !!</h2>';
            }catch(Exception $e){
              echo '<h2 class="error">Błąd w dodaniu książki !!</h2>';
            }
        }
      ?>
  </form>
  <address class="info">
      Warszawa ul. Fajna 15 <br/>
      Numer Telefonu: 123456789
  </address>
  <script>
    document.getElementById('addition_date').valueAsDate = new Date();

    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  </script>
</body>
</html>