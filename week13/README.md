# Week13: 独自ドメイン + HTTPS化

## 概要

Week11でデプロイしたLaravelアプリ（EC2 + Docker Compose、`week10/基本課題`のコードを使用）に、
独自ドメイン `shio-portfolio.com` を設定し、Let's EncryptでHTTPS化した。

- 公開URL: https://shio-portfolio.com
- www付きアクセスは、https://shio-portfolio.com にリダイレクトされる

## 構成

- ドメイン取得: お名前.com（.com、年間1,408円）
- DNS: お名前.comのDNSレコード設定でAレコードをEC2のElastic IPに向けている
- Webサーバー: EC2ホスト上のNginx（リバースプロキシ）→ Dockerコンテナ内のNginx（ポート8080）→ PHP-FPM（Laravelアプリ）
- SSL証明書: Let's Encrypt（Certbot）

## デプロイ手順（Week11との差分）

1. EC2インスタンスを再構築（Ubuntu 26.04, t3.micro）
2. セキュリティグループにHTTP(80)・HTTPS(443)を追加、SSH(22)は自分のIPのみに制限
3. Elastic IPを取得・関連付け
4. お名前.comでドメインを取得
5. AレコードをElastic IPに設定、DNS反映を確認
6. EC2にDocker・Docker Composeをインストールし、アプリをclone
7. docker-compose.ymlのWebサーバーのポートを8090:80から8080:80に変更（ホストのNginxと衝突しないようにするため）
8. EC2ホストにNginxをインストールし、リバースプロキシ設定を作成
9. Certbotでドメインとwwwサブドメインの証明書を取得
10. www用に専用のNginx server blockを追加し、wwwありのアクセスをwwwなしにリダイレクト

## 環境構築時のトラブルと対処

- EBSボリュームの容量不足: デフォルトの8GBではDockerイメージのビルドで容量が足りず、20GBに拡張し、growpartとresize2fsでパーティション・ファイルシステムを拡張した。
- メモリ不足によるMySQLコンテナの強制終了: t3.microは1GBメモリしかなく、MySQL・PHP・Nginxなどを同時に動かすとOOM Killerに強制終了させられることがあったため、1GBのスワップファイルを作成して対応した。
- ログファイルの所有者不一致による500エラー: storage/logs/laravel.logの所有者がrootになっており、PHP-FPMを実行するwww-dataが書き込めずアプリ全体がエラーになっていた。所有者をwww-dataに統一して解決した。

## 練習課題1: コマンドの動作確認

### dig ドメイン名 +short
結果: 18.178.6.249
ドメイン名に対応するIPアドレス（Aレコードの値）が返ってきている。これは、DNSサーバーに登録したElastic IPアドレスが正しく反映されていることを示している。

### curl -I https://ドメイン名
結果: HTTP/1.1 200 OK、Server: nginx/1.28.3 (Ubuntu)、X-Powered-By: PHP/8.2.33
200 OKは「リクエストが成功し、正常にコンテンツが返された」ことを意味する。Serverヘッダーは応答したWebサーバーソフトウェア（Nginx）を示し、X-Powered-Byはバックエンドで動いているPHPのバージョンを示している。

### sudo certbot certificates
結果: Expiry Date: 2026-11-23 (VALID: 89 days)
取得したSSL証明書の有効期限が表示される。Let's Encryptの証明書は90日間有効で、Certbotがインストール時に自動でsystemdタイマーを設定しているため、期限が近づくと自動的に更新される仕組みになっている。


### curl -I http://ドメイン名（HTTP→HTTPSリダイレクトの確認）
結果:
```
HTTP/1.1 301 Moved Permanently
Location: https://shio-portfolio.com/
```
301はリソースが恒久的に別のURLに移動したことを示すステータスコードで、`Location`ヘッダーに
移動先のURL（HTTPS版）が示されている。ブラウザはこれを受けて自動的にHTTPS版へ再アクセスするため、
利用者が`http://`を入力しても最終的に暗号化された接続に切り替わる。
## 練習課題2: wwwなし・ありの統一

wwwありのHTTPSアクセスにも対応する証明書をCertbotで取得した上で、www用に専用のNginx server blockを追加し、wwwありのアクセスをwwwなしのURLに301リダイレクトするよう設定した。

## Nginx設定ファイル抜粋

`/etc/nginx/sites-available/shio-portfolio.com`（メインドメイン用、Certbotが自動でSSL設定とリダイレクトを追記）:

```nginx
server {
    server_name shio-portfolio.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/shio-portfolio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/shio-portfolio.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}

server {
    if ($host = shio-portfolio.com) {
        return 301 https://$host$request_uri;
    }
    if ($host = www.shio-portfolio.com) {
        return 301 https://$host$request_uri;
    }

    listen 80;
    server_name shio-portfolio.com;
    return 404;
}
```

`/etc/nginx/sites-available/www-redirect.com`（wwwありのアクセスをwwwなしにリダイレクトするための専用設定）:

```nginx
server {
    listen 443 ssl;
    server_name www.shio-portfolio.com;

    ssl_certificate /etc/letsencrypt/live/shio-portfolio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/shio-portfolio.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    return 301 https://shio-portfolio.com$request_uri;
}
```

`proxy_pass`でリクエストをDockerコンテナ内のNginx（ホストのポート8080にマッピング）に転送し、
`proxy_set_header`で元のリクエストのホスト名やIPアドレスなどの情報をバックエンドに引き継いでいる。
