<?php
// API URL'sini tanımlayın
$api_url = "https://api.github.com/users/akara44";

// cURL oturumu başlatın
$ch = curl_init();

// cURL seçeneklerini ayarlayın
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');

// API'den gelen veriyi alın
$response = curl_exec($ch);

// cURL oturumunu kapatın
curl_close($ch);

// JSON verisini PHP nesnesine dönüştürün
$data = json_decode($response);

// Veriyi ekrana yazdırma
echo "Login: " . $data->login . "<br>";
echo "ID: " . $data->id . "<br>";
echo "Node ID: " . $data->node_id . "<br>";
echo "Avatar URL: <img src='" . $data->avatar_url . "' alt='Avatar'><br>";
echo "Profile URL: <a href='" . $data->html_url . "'>" . $data->html_url . "</a><br>";
echo "Followers URL: " . $data->followers_url . "<br>";
echo "Following URL: " . $data->following_url . "<br>";
echo "Gists URL: " . $data->gists_url . "<br>";
echo "Starred URL: " . $data->starred_url . "<br>";
echo "Subscriptions URL: " . $data->subscriptions_url . "<br>";
echo "Organizations URL: " . $data->organizations_url . "<br>";
echo "Repos URL: " . $data->repos_url . "<br>";
echo "Events URL: " . $data->events_url . "<br>";
echo "Received Events URL: " . $data->received_events_url . "<br>";
echo "Type: " . $data->type . "<br>";
echo "Site Admin: " . ($data->site_admin ? "Yes" : "No") . "<br>";
echo "Name: " . $data->name . "<br>";
echo "Company: " . ($data->company ?: "N/A") . "<br>";
echo "Blog: " . ($data->blog ?: "N/A") . "<br>";
echo "Location: " . ($data->location ?: "N/A") . "<br>";
echo "Email: " . ($data->email ?: "N/A") . "<br>";
echo "Hireable: " . ($data->hireable !== null ? ($data->hireable ? "Yes" : "No") : "N/A") . "<br>";
echo "Bio: " . ($data->bio ?: "N/A") . "<br>";
echo "Twitter Username: " . ($data->twitter_username ?: "N/A") . "<br>";
echo "Public Repos: " . $data->public_repos . "<br>";
echo "Public Gists: " . $data->public_gists . "<br>";
echo "Followers: " . $data->followers . "<br>";
echo "Following: " . $data->following . "<br>";
echo "Created At: " . $data->created_at . "<br>";
echo "Updated At: " . $data->updated_at . "<br>";
?>