<?php

class IMDB {

    public $apiURL = 'http://app.imdb.com/';
    public $userAgent = 'IMDb/3.4.1.103410110 (GT-I9500; Android 19; SAMSUNG)';
    public $appID = 'android030401';
    public $device = 'fbbd68fb-ad11-473f-b5da-28fe6c0bda4c';
    public $sig = 'and2';
    public $key = 'eRnAYqbvj2JWXyPcu62yCA';
    // Please set this to 'TRUE' for debugging purposes only.
    public $debug = false;

    //Define  the language (en_US, fr_FR, de_DE, es_ES, it_IT, pt_PT)
    // if there is no version in desired language the retuened data will be in english as fallback)
    public function __construct($input, $language = 'en_US', $timeOut = 5) {
        $this->language = $language;
        $this->timeOut = $timeOut;
        $this->input = $input;
        $this->data = $this->getMovieDetails();
        $this->data = $this->data['data'];
        if (isset($this->data['error'])) {
            $this->isReady = false;
            $this->status = $this->data['error']['message'];
        } else {
            $this->isReady = true;
            $this->status = 'OK';
        }
    }

    private function get_data($url) {

        if ($this->debug)
            echo $url . "\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, true);
    }

    private function saveImage($imgURL) {
        //Saved the image to 'attachments/_posters/' . $this->_strId . '.jpg';
    }

    private function getTimeStamp() {
        return time();
    }

    private function getAPIURL($func) {
        $url = $this->apiURL . $func . "appid=" . $this->appID . "&device=" . $this->device . "&locale=" . $this->language . "&timestamp=" . $this->getTimeStamp() . "&sig=" . $this->sig;
        $sig_hash = hash_hmac('sha1', $url, $this->key);
        $url .= "-" . $sig_hash;
        return $url;
    }

    private function doCurl($strUrl, $bolOverWriteSource = TRUE) {
        if ($this->debug)
            echo $strUrl . "\n";
        $oCurl = curl_init($strUrl);
        $lang_parts = explode("_", $this->language);
        curl_setopt_array($oCurl, array(
            CURLOPT_VERBOSE => FALSE,
            CURLOPT_HEADER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Accept-Language: ' . str_replace("_", "-", $this->language) . ", " . $lang_parts[1] . ';q=0.5'
            ),
            CURLOPT_FRESH_CONNECT => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => $this->timeOut,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_REFERER => 'http://www.google.com',
            CURLOPT_USERAGENT => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_COOKIEFILE => FALSE
        ));
        return curl_exec($oCurl);
    }

    private function matchRegex($strContent, $strRegex, $intIndex = NULL) {
        $arrMatches = FALSE;
        preg_match_all($strRegex, $strContent, $arrMatches);
        if ($arrMatches === FALSE)
            return FALSE;
        if ($intIndex != NULL && is_int($intIndex)) {
            if ($arrMatches[$intIndex]) {
                return $arrMatches[$intIndex][0];
            }
            return FALSE;
        }
        return $arrMatches;
    }

    private function removeAccents($string) {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    private function getMovieDetails() {

        //check if input was ID or name
        if (preg_match('~(\d{6,})~', $this->input, $result)) {
            if ($this->debug)
                echo "Doing movie details call\n";
            $this->ImdbId = 'tt' . $result[0];
            $url = $this->getAPIURL("title/tt" . $result[0] . "/maindetails?");
        }else {
            if ($this->debug)
                echo "Trying to find ImdbID\n";
            $url = $this->getAPIURL("find?q=" . urlencode($this->input) . "&");
            $search_result = $this->get_data($url);
            if ($this->debug)
                echo "Doing movie details call\n";
            $this->ImdbId = $search_result['data']['results'][0]['list'][0]['tconst'];
            $url = $this->getAPIURL("title/" . $search_result['data']['results'][0]['list'][0]['tconst'] . "/maindetails?");
        }

        return $this->get_data($url);
    }

    public function getUserComments($limit = 5) {

        $url = $this->getAPIURL("title/usercomments?tconst=" . $this->ImdbId . "&limit=" . $limit . "&");
        $userCommentData = $this->get_data($url);
        $userComments = array();
        if(isset($userCommentData['data']['user_comments'])){
            foreach ($userCommentData['data']['user_comments'] as $comment) {
                $comment['text'] = $this->removeAccents($comment['text']);
                $userComments[] = $comment;
            }
        }

        return $userComments;
    }

    public function getParentalGuide() {

        $url = $this->getAPIURL("title/parentalguide?tconst=" . $this->ImdbId . "&");
        $parentalGuideData = $this->get_data($url);
        $parentalGuide = array();
        if(isset($parentalGuideData['data']['parental_guide'])){
            foreach ($parentalGuideData['data']['parental_guide'] as $guide) {

                $guide['text'] = $this->removeAccents($guide['text']);
                $parentalGuide[] = $guide;
            }
        }
        return $parentalGuide;
    }

    private function getScrape() {

        if (!isset($this->_strSource) || $this->_strSource == null || $this->_strSource == "") {
            $this->_strSource = $this->doCurl($this->getUrl());
        }

        return $this->_strSource;
    }

    //Get IMDB api return data
    public function getImdbResponce() {
        return $this->data;
    }

    public function getAka() {
        if ($strReturn = $this->matchRegex($this->getScrape(), '~Also Known As:</h4>(.*)<span~Ui', 1)) {
            return trim($strReturn);
        } else {
            return "N/A";
        }
    }

    public function getLanguagesArray() {
        $arrReturned = $this->matchRegex($this->getScrape(), '~href="/language/(?:.*)itemprop=\'url\'>(.*)</a>~Uis');
        if (count($arrReturned[1])) {
            foreach ($arrReturned[1] as $strName) {
                $arrReturn[] = trim($strName);
            }
            return $arrReturn;
        } else {
            return array("N/A");
        }
    }

    public function getTrailer() {
        $YoutTubeSearchQuery = html_entity_decode(str_replace(' ', '+', $this->getTitle() . "+" . $this->getYear() . "+trailer"));
        $YoutTubeSearchQuery = preg_replace('/[^A-Za-z0-9]\+/', '', $YoutTubeSearchQuery);
        $YoutTubeXML = simplexml_load_file("http://gdata.youtube.com/feeds/api/videos?q=" . $YoutTubeSearchQuery . "&start-index=1&max-results=2");
        if (isset($YoutTubeXML->entry[0]->id)) {
            $YoutubeID = substr($YoutTubeXML->entry[0]->id, 42);
        } else {
            $YoutubeID = "";
        }

        return $YoutubeID;
    }

    public function getLanguagesString() {
        return implode(" | ", $this->getLanguagesArray());
    }

    public function getLanguages() {
        $arr = array();
        $arrr = $this->getLanguagesArray();
        for ($i = 0; $i < 2; $i++) {
            $arr[] = $arrr[$i];
        }
        if (count($arr) > 1)
            return "N/A";
        if (count($arr) == 1)
            return $arr[0];

        return implode(" | ", $arr);
    }

    public function getCastArray() {
        $cast_list = array();
        if(isset($this->data['cast_summary'])){
            foreach ($this->data['cast_summary'] as $cast) {
                $img = isset($cast['name']['image']['url']) ? $cast['name']['image']['url'] : 'n/a';
                $cast['char'] = isset($cast['char']) ? $cast['char'] : 'Unknown';
                $cast_list[] = array('name' => $this->removeAccents($cast['name']['name']), 'id' => $cast['name']['nconst'], 'url' => 'http://www.imdb.com/name/' . $cast['name']['nconst'] . '/', 'image' => $img, 'character' => $this->removeAccents($cast['char']));
            }
        }
        return $cast_list;
    }

    public function getDirectorArray() {
        $dir_array = array();
        foreach ($this->data['directors_summary'] as $director) {
            $img = isset($director['name']['image']['url']) ? $director['name']['image']['url'] : 'n/a';
            $dir_array[] = array('name' => $this->removeAccents($director['name']['name']), 'id' => $director['name']['nconst'], 'url' => 'http://www.imdb.com/name/' . $director['name']['nconst'] . '/', 'image' => $img);
        }
        return $dir_array;
    }

    //Can set the max amount of generes to return
    public function getGenreArray($max = 0) {
        if (is_int($max) && $max > 0) {
            $genre_array = array();
            for ($i = 0; $i < $max; $i++) {
                $genre_array[] = $this->data['genres'][$i];
            }
        } else {
            $genre_array = $this->data['genres'];
        }
        return $genre_array;
    }

    //Can set the max amount of generes to return
    public function getGenreString($max = 0) {
        return trim(implode(" | ", $this->getGenreArray($max)), " | ");
    }

    public function getGenre($max = 0) {
        return $this->getGenreString($max);
    }

    public function getMpaa() {
        return isset($this->data['certificate']['certificate']) ? $this->data['certificate']['certificate'] : 'N/A';
    }

    //short description
    public function getDescription() {
        return isset($this->data['plot']['outline']) ? $this->data['plot']['outline'] : 'N/A';
    }

    //long description
    public function getPlot() {
        if ($this->debug)
            echo "Doing movie plot call\n";
        $json_res = $this->get_data($this->getAPIURL("title/plot?tconst=" . $this->ImdbId . "&"));
        return isset($json_res['data']['plots'][0]['text']) ? $json_res['data']['plots'][0]['text'] : $this->getDescription();
    }

    public function getImdbID() {
        return $this->ImdbId;
    }

    public function getUrl() {
        return "http://www.imdb.com/title/" . $this->ImdbId . "/";
    }

    public function getPoster() {
        if (isset($this->data['image']['url']))
            return $this->data['image']['url'];

        if (isset($this->data['photos'][0]['image']['url']))
            return $this->data['photos'][0]['image']['url'];

        return 'N/A';
    }

    public function getRating() {
        return isset($this->data['rating']) ? number_format($this->data['rating'], 1) : 'N/A';
    }

    public function getRuntime() {
        return isset($this->data['runtime']) ? ( $this->data['runtime']['time'] / 60 ) : 'N/A';
    }

    public function getTitle() {
        return isset($this->data['title']) ? $this->data['title'] : 'N/A';
    }

    //We want 'feature' for movie
    public function getType() {
        return isset($this->data['type']) ? $this->data['type'] : 'N/A';
    }

    public function isTvShow() {
        return $this->getType() == 'tv_series' ? true : false;
    }

    public function isVideo() {
        return $this->getType() != 'feature' && $this->getType() == 'tv_series' ? true : false;
    }

    public function getYear() {
        return isset($this->data['year']) ? $this->data['year'] : 'N/A';
    }

    public function getAll() {
        $oData = array();
        $oData['castArray'] = $this->getCastArray();
        $oData['directorArray'] = $this->getDirectorArray();
        $oData['genreArray'] = $this->getGenreArray();
        $oData['genreString'] = $this->getGenreString();
        $oData['mpaa'] = $this->getMpaa();
        $oData['description'] = $this->getDescription();
        $oData['plot'] = $this->getPlot();
        $oData['imdbID'] = $this->getImdbID();
        $oData['imdbURL'] = $this->getUrl();
        $oData['poster'] = $this->getPoster();
        $oData['rating'] = $this->getRating();
        $oData['runtime'] = $this->getRuntime();
        $oData['title'] = $this->getTitle();
        $oData['AKA'] = $this->getAka();
        $oData['languagesArray'] = $this->getLanguagesArray();
        $oData['languagesString'] = $this->getLanguagesString();
        $oData['trailer'] = $this->getTrailer();
        $oData['isTV'] = $this->isTvShow();
        $oData['type'] = $this->getType();
        $oData['year'] = $this->getYear();
        $oData['userComments'] = $this->getUserComments();
        $oData['parentalGuide'] = $this->getParentalGuide();
        return $oData;
    }

}

?>
