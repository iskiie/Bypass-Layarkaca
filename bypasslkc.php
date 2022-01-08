<?php

balik:
echo "Masukkan kata kunci judul film : ";
$judul = trim(fgets(STDIN));

$get = json_decode(curl('https://search.indexmovies.xyz/?s=' . $judul, 0, 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0')[1]);

if (!isset($get->results[0]->title)) {
    echo "Data tidak di temukan...\n\n";
    goto balik;
}

foreach ($get->results as $key => $value) {
    echo "[$key] " . $value->title . "\n";
}


echo "\nKetik [N] untuk mencari judul yang baru...\n";

nomor:
echo "\nNomor berapa : ";
$nomor = trim(fgets(STDIN));

if (strtolower($nomor) == "n") {
    echo "\n";
    goto balik;
} elseif (!is_numeric($nomor)) {
    echo "Silahkan pilih nomor yang benar...\n";
    goto nomor;
} elseif ($key < $nomor) {
    echo "Angka [$nomor] tidak ada, hanya ada angka [0] sampai [$key]...\n";
    goto nomor;
}

foreach ($get->results as $key2 => $value2) {
    if ($nomor == $key2) {
        echo "[$key2] " . $value2->title . "\n";
        $get = curl('http://dl.indexmovies.xyz/iframe/top.php', array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8', 'Accept: */*', 'X-Requested-With: XMLHttpRequest'), 'slug=' . $value2->slug, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0')[1];
        preg_match_all('/<a href="(.*?)" target="_parent">KLIK UNTUK KE HALAMAN DOWNLOAD<\/a>/', $get, $data);
        $url_film = explode("/", $data[1][0]);
        $get_link = curl('https://' . $url_film[2] . '/verifying.php', array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8', 'Accept: */*', 'X-Requested-With: XMLHttpRequest'), 'slug=' . $url_film[4], 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0')[1];

        preg_match_all('/<a href="(.*?)">/', $get_link, $view_film);
        echo "\nTonton Film :\n";
        foreach ($view_film[1] as $linkdl) {
            echo "$linkdl\n";
        }

        preg_match_all("/href='(.*?)'/", $get_link, $download_filem);
        echo "\nDownload Film :\n";
        foreach ($download_filem[1] as $linkdl1) {
            echo "$linkdl1\n";
        }

        preg_match_all('/target="_blank" href="(.*?)"/', $get_link, $data_link);
        echo "\nTonton Film / Download Film :\n";
        foreach ($data_link[1] as $linkdl2) {
            if (preg_match_all("/layarkacaxxi/i", $linkdl2)) {
                echo "bypass => $linkdl2\n";
                $get = json_decode(curl("https://layarkacaxxi.icu/api/source/".explode("/", $linkdl2)[4], array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8', 'Accept: */*', 'X-Requested-With: XMLHttpRequest'), "POST", 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0')[1]);
                //print_r($get);
                foreach ($get->data as $data_download) {
                    echo "=> $data_download->label\n=> ";
                    $short = curl("https://api-ssl.bitly.com/v4/shorten", array('Authorization: Bearer a2d9f84fb737859d335310078049ea3fb030903f','Content-Type: application/json'), json_encode(['long_url' => $data_download->file]))[1];
                    $pendekin = json_decode($short);
                    echo "$pendekin->link\n";
                }
                echo "\n";
            } else {
                echo "$linkdl2\n";
            }
        }
    }
}

function curl($url, $header = null, $postfields = null, $useragent = null, $cookie = null, $proxy = null)
{
    $c = curl_init();
    if ($proxy) {
        curl_setopt($c, CURLOPT_PROXY, $proxy);
    }

    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    if ($header) {
        curl_setopt($c, CURLOPT_HTTPHEADER, $header);
    }

    if ($postfields) {
        curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
    }

    curl_setopt($c, CURLOPT_HEADER, 1);
    if ($cookie) {
        curl_setopt($c, CURLOPT_COOKIE, $cookie);
    }

    if ($useragent) {
        curl_setopt($c, CURLOPT_USERAGENT, $useragent);
    }
    $response = curl_exec($c);
    $header   = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE));
    $body     = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE));
    curl_close($c);
    return array($header, $body);
}