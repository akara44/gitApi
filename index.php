<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Github Profile Cards</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    <style>
        .toggle-link {
            display: block;
            margin: 10px auto;
            text-align: center;
            color: white;
            cursor: pointer;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <form id="form" class="user_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="text" id="search" placeholder="GitHub User" autocomplete="off" name="user" required/>
        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Kullanıcı adını formdan alın
        $user = htmlspecialchars($_POST['user']);
        $api_url = "https://api.github.com/users/$user";
        $api_urlr = "https://api.github.com/users/$user/repos";

        // cURL oturumunu başlatın
        $mh = curl_multi_init();

        // cURL handler oluşturun
        $ch1 = curl_init();
        $ch2 = curl_init();

        // cURL seçeneklerini ayarlayın
        curl_setopt($ch1, CURLOPT_URL, $api_url);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch1, CURLOPT_USERAGENT, 'PHP');

        curl_setopt($ch2, CURLOPT_URL, $api_urlr);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_USERAGENT, 'PHP');

        // cURL handler'larını multi handle'a ekleyin
        curl_multi_add_handle($mh, $ch1);
        curl_multi_add_handle($mh, $ch2);

        // cURL işlemlerini çalıştırın
        do {
            $status = curl_multi_exec($mh, $active);
            curl_multi_select($mh);
        } while ($active && $status == CURLM_OK);

        // Sonuçları alın
        $response = curl_multi_getcontent($ch1);
        $response_repos = curl_multi_getcontent($ch2);

        // HTTP durum kodlarını kontrol edin
        $http_code = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
        $http_code_repos = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

        // cURL handler'larını kapatın
        curl_multi_remove_handle($mh, $ch1);
        curl_multi_remove_handle($mh, $ch2);
        curl_multi_close($mh);

        if ($http_code == 200 && $http_code_repos == 200) {
            // JSON verisini PHP nesnesine dönüştürün
            $data = json_decode($response);
            $repos = json_decode($response_repos);

            if ($data):
    ?>
                <div class="card">
                    <img class="user-image" src="<?= $data->avatar_url ?>" alt="User Image"/>

                    <div class="user-name">
                        <h2><?= $data->login ?></h2>
                        <small><?= $data->company ?: "N/A" ?></small> <br>
                        <small><?= $data->location ?: "N/A" ?></small>
                    </div>

                    <ul>
                        <li>
                            <i class="fa-solid fa-user-group"></i> <?= $data->followers ?>
                            <strong>Followers</strong>
                        </li>
                        <li>
                            <i class="fa-solid fa-user-plus"></i> <?= $data->following ?>
                            <strong>Following</strong>
                        </li>
                        <li>
                            <i class="fa-solid fa-bookmark"></i> <?= $data->public_repos ?>
                            <strong>Repositories</strong>
                        </li>
                    </ul>

                    <div class="repos" id="repos">
                        <?php 
                        $first_repos = array_slice($repos, 0, 3);
                        foreach ($first_repos as $repo): ?>
                            <a href="<?= $repo->html_url ?>" target="_blank">
                                <i class="fa-solid fa-book-bookmark"></i> <?= $repo->name ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($repos) > 3): ?>
                        <div id="toggleLink" class="toggle-link" onclick="toggleRepos()">Daha Fazla</div>
                    <?php endif; ?>
                </div>
                
                <script>
                    const allRepos = <?php echo json_encode($repos); ?>;
                    let showingAll = false;

                    function toggleRepos() {
                        const repoContainer = document.getElementById('repos');
                        repoContainer.innerHTML = '';

                        if (showingAll) {
                            const first_repos = allRepos.slice(0, 3);
                            first_repos.forEach(repo => {
                                const repoLink = document.createElement('a');
                                repoLink.href = repo.html_url;
                                repoLink.target = '_blank';
                                repoLink.innerHTML = `<i class="fa-solid fa-book-bookmark"></i> ${repo.name}`;
                                repoContainer.appendChild(repoLink);
                            });
                            document.getElementById('toggleLink').innerText = 'Daha Fazla';
                        } else {
                            allRepos.forEach(repo => {
                                const repoLink = document.createElement('a');
                                repoLink.href = repo.html_url;
                                repoLink.target = '_blank';
                                repoLink.innerHTML = `<i class="fa-solid fa-book-bookmark"></i> ${repo.name}`;
                                repoContainer.appendChild(repoLink);
                            });
                            document.getElementById('toggleLink').innerText = 'Gizle';
                        }

                        showingAll = !showingAll;
                    }
                </script>
    <?php
            else:
                echo "<p class='search_p'>JSON verisi çözümlenemedi.</p>";
            endif;
        } else {
            echo "<p class='search_p'>API isteği başarısız oldu. HTTP Kod: $http_code</p>";
        }
    }
    ?>

    <!--Script Axios-->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>