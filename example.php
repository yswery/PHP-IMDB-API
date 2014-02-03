<?php
include_once 'imdb.php';

//put in Name of movie or IMDB ID (tt0499549)
$imdb = new IMDB("Avatar 2009");
if($imdb->isReady){

	$imdb_api = array();
	$imdb_api['castArray'] = $this->getCastArray();
	$imdb_api['directorArray'] = $this->getDirectorArray();
	$imdb_api['genreArray'] = $this->getGenreArray();
	$imdb_api['genreString'] = $this->getGenreString();
	$imdb_api['mpaa'] = $this->getMpaa();
	$imdb_api['description'] = $this->getDescription();
	$imdb_api['plot'] = $this->getPlot();
	$imdb_api['imdbID'] = $this->getImdbID();
	$imdb_api['imdbURL'] = $this->getUrl();
	$imdb_api['poster'] = $this->getPoster();
	$imdb_api['rating'] = $this->getRating();
	$imdb_api['runtime'] = $this->getRuntime();
	$imdb_api['title'] = $this->getTitle();
	$imdb_api['AKA'] = $this->getAka();
	$imdb_api['languagesArray'] = $this->getLanguagesArray();
	$imdb_api['languagesString'] = $this->getLanguagesString();
	$imdb_api['trailer'] = $this->getTrailer();
	$imdb_api['isTV'] = $this->isTvShow();
	$imdb_api['type'] = $this->getType();
	$imdb_api['year'] = $this->getYear();

	echo "<PRE>";
	print_r($imdb_api);
	echo "</PRE>";

}else{
	echo $imdb->status;
}


?>
