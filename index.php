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
            font-size: 1.5em; /* Yazı boyutunu büyüttük */
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

        // cURL oturumu başlatın
        $ch = curl_init();

        // cURL seçeneklerini ayarlayın
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');

        // API'den gelen veriyi alın
        $response = curl_exec($ch);

        // HTTP durum kodunu kontrol edin
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // cURL oturumunu kapatın
        curl_close($ch);

        if ($http_code == 200) {
            // JSON verisini PHP nesnesine dönüştürün
            $data = json_decode($response);

            // İkinci cURL oturumu başlatın
            $ch_repos = curl_init();

            // cURL seçeneklerini ayarlayın
            curl_setopt($ch_repos, CURLOPT_URL, $api_urlr);
            curl_setopt($ch_repos, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch_repos, CURLOPT_USERAGENT, 'PHP');

            // API'den gelen veriyi alın
            $response_repos = curl_exec($ch_repos);

            // HTTP durum kodunu kontrol edin
            $http_code_repos = curl_getinfo($ch_repos, CURLINFO_HTTP_CODE);

            // cURL oturumunu kapatın
            curl_close($ch_repos);

            if ($http_code_repos == 200) {
                // JSON verisini PHP nesnesine dönüştürün
                $repos = json_decode($response_repos);
            } else {
                $repos = [];
            }

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